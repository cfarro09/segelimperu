<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Almacen_compras_model extends CI_Model
{
    public function __construct(){parent::__construct();$this->load->database();}
    public function obtener($id){return $this->db->get_where('almacen_compras',array('id'=>$id))->row();}
    // Varias órdenes por mes, en orden de generación (OC-202610-01, -02, ...).
    public function del_mes($periodo){return $this->db->order_by('numero')->get_where('almacen_compras',array('periodo'=>$periodo))->result();}
    public static function codigo($o){return 'OC-'.str_replace('-','',$o->periodo).'-'.str_pad((int)($o->numero ?? 1),2,'0',STR_PAD_LEFT);}
    // Aprobadas sin despachar que aún no están en ninguna orden: las que generaría o sumaría la próxima consolidación.
    public function nuevas($periodo)
    {
        return (int)$this->db->query("SELECT COUNT(*) AS n FROM almacen_asignaciones a JOIN almacen_clientes c ON c.id=a.cliente_id WHERE a.periodo=? AND a.estado='aprobado' AND c.activo=1 AND NOT EXISTS (SELECT 1 FROM almacen_despachos s WHERE s.asignacion_id=a.id) AND NOT EXISTS (SELECT 1 FROM almacen_compra_asignaciones l WHERE l.asignacion_id=a.id)",array($periodo))->row()->n;
    }
    // conteo_actual: si el producto tiene hoy conteo físico (puede haberse contado después de consolidar).
    public function detalles($id){return $this->db->select('d.*, p.stock_confirmado AS conteo_actual')->from('almacen_compra_detalles d')->join('almacen_productos p','p.id=d.producto_id','left')->where('d.compra_id',$id)->order_by('d.descripcion')->order_by('d.producto_id')->get()->result();}
    public function asignaciones($id){return $this->db->select('a.*,c.nombre,s.id AS despacho_id')->join('almacen_asignaciones a','a.id=l.asignacion_id')->join('almacen_clientes c','c.id=a.cliente_id')->join('almacen_despachos s','s.asignacion_id=a.id','left')->where('l.compra_id',$id)->order_by('c.nombre')->get('almacen_compra_asignaciones l')->result();}
    public function cantidades_por_cliente($id)
    {
        // Only the assignments included in this order; keep received history visible.
        $lineas=$this->db->select('d.producto_id,a.cliente_id,d.cantidad')
            ->join('almacen_asignaciones a','a.id=l.asignacion_id')
            ->join('almacen_asignacion_detalles d','d.asignacion_id=a.id')
            ->where('l.compra_id',$id)->get('almacen_compra_asignaciones l')->result();
        $cantidades=array();
        foreach($lineas as $linea) $cantidades[$linea->producto_id][$linea->cliente_id]=$linea->cantidad;
        return $cantidades;
    }
    public function consolidar($periodo,$usuario)
    {
        $this->db->trans_begin();
        // MySQL named lock serializes creation before the month has an order row.
        $nombre='almacen-compra-'.$periodo;
        $lock=$this->db->query('SELECT GET_LOCK(?, 5) AS tomado',array($nombre))->row();
        if(!$lock || !$lock->tomado){$this->db->trans_rollback();return array('error'=>'Otro usuario está consolidando este mes. Inténtalo nuevamente.');}
        $resultado=$this->consolidar_bloqueado($periodo,$usuario);
        if(isset($resultado['error']) || !$this->db->trans_status()){$this->db->trans_rollback();if(!isset($resultado['error']))$resultado=array('error'=>'No se pudo consolidar.');}
        else $this->db->trans_commit();
        $this->db->query('SELECT RELEASE_LOCK(?)',array($nombre));return $resultado;
    }
    private function consolidar_bloqueado($periodo,$usuario)
    {
        // Una sola orden abierta (borrador) por mes; las recibidas quedan cerradas con sus asignaciones.
        $compra=$this->db->query("SELECT * FROM almacen_compras WHERE periodo=? AND estado='borrador' ORDER BY numero DESC LIMIT 1 FOR UPDATE",array($periodo))->row();
        $q=$this->db->query("SELECT a.id FROM almacen_asignaciones a JOIN almacen_clientes c ON c.id=a.cliente_id WHERE a.periodo=? AND a.estado='aprobado' AND c.activo=1 AND NOT EXISTS (SELECT 1 FROM almacen_despachos s WHERE s.asignacion_id=a.id) AND NOT EXISTS (SELECT 1 FROM almacen_compra_asignaciones l JOIN almacen_compras o ON o.id=l.compra_id WHERE l.asignacion_id=a.id AND o.estado<>'borrador') ORDER BY a.id FOR UPDATE",array($periodo));
        if(!$q)return array('error'=>'No se pudieron verificar las asignaciones. Intenta nuevamente.');
        $aprobadas=$q->result();
        if(!$aprobadas && !$compra)return array('error'=>'No hay asignaciones aprobadas nuevas desde la última orden de compra.');
        // Stock libre = stock + lo que llegará en órdenes aprobadas aún no recibidas
        // - lo reservado para asignaciones de órdenes anteriores (aprobadas o recibidas) que aún no se despachan.
        $comprometido=array(); $entrante=array();
        foreach($this->db->query("SELECT d.producto_id, SUM(d.cantidad) AS cantidad FROM almacen_asignacion_detalles d JOIN almacen_asignaciones a ON a.id=d.asignacion_id JOIN almacen_compra_asignaciones l ON l.asignacion_id=a.id JOIN almacen_compras o ON o.id=l.compra_id WHERE a.estado='aprobado' AND o.estado<>'borrador' AND d.cantidad>0 AND NOT EXISTS (SELECT 1 FROM almacen_despachos s WHERE s.asignacion_id=a.id) GROUP BY d.producto_id")->result() as $r) $comprometido[$r->producto_id]=(float)$r->cantidad;
        foreach($this->db->query("SELECT d.producto_id, SUM(d.cantidad) AS cantidad FROM almacen_compra_detalles d JOIN almacen_compras o ON o.id=d.compra_id WHERE o.estado='aprobada' GROUP BY d.producto_id")->result() as $r) $entrante[$r->producto_id]=(float)$r->cantidad;
        $ids=array_map(function($a){return $a->id;},$aprobadas);
        $productos=$ids?$this->db->select('producto_id,SUM(cantidad) AS solicitado,MIN(unidad) AS unidad_min,MAX(unidad) AS unidad_max',false)->where_in('asignacion_id',$ids)->where('cantidad >',0)->group_by('producto_id')->order_by('producto_id')->get('almacen_asignacion_detalles')->result():array();
        if($ids && !$productos)return array('error'=>'Las asignaciones aprobadas no contienen productos.');
        $previos=array();if($compra)foreach($this->detalles($compra->id) as $d)$previos[$d->producto_id]=$d;
        $lineas=array();$total=0;
        foreach($productos as $d){
            $p=$this->db->query('SELECT * FROM almacen_productos WHERE id=? FOR UPDATE',array($d->producto_id))->row();
            if(!$p || !$p->activo)return array('error'=>'Un producto aprobado está inactivo. Revisa el catálogo antes de consolidar.');
            if($d->unidad_min!==$p->unidad || $d->unidad_max!==$p->unidad)return array('error'=>'La unidad de un producto cambió desde su asignación. Revisa esa presentación antes de consolidar.');
            if((float)$d->solicitado>999999999.999)return array('error'=>'La cantidad consolidada supera el rango permitido.');
            $stock=$p->stock_confirmado?max(0,round((float)$p->stock+($entrante[$p->id] ?? 0)-($comprometido[$p->id] ?? 0),3)):0;
            $ajustado=isset($previos[$p->id]) && $previos[$p->id]->ajustado;
            $cantidad=$ajustado?$previos[$p->id]->cantidad:max(0,round($d->solicitado-$stock,3));
            // La compra se valoriza con el precio con IGV (precio_real de la línea guarda el precio usado en la orden).
            $precio=$p->precio_valorado;$importe=round($cantidad*$precio,2);$total+=$importe;
            if($total>9999999999999.99)return array('error'=>'El importe de compra supera el rango permitido.');
            $lineas[]=array('producto_id'=>$p->id,'descripcion'=>$p->descripcion,'unidad'=>$p->unidad,'marca'=>$p->marca,'solicitado'=>$d->solicitado,'stock_disponible'=>$stock,'stock_confirmado'=>$p->stock_confirmado,'cantidad'=>$cantidad,'precio_real'=>$precio,'total'=>$importe,'ajustado'=>$ajustado?1:0);
        }
        $numero=$compra?null:(int)$this->db->query('SELECT COALESCE(MAX(numero),0)+1 AS n FROM almacen_compras WHERE periodo=?',array($periodo))->row()->n;
        $ok=$compra?$this->db->where('id',$compra->id)->update('almacen_compras',array('total'=>$total,'revision'=>$compra->revision+1,'usuario_id'=>$usuario)):$this->db->insert('almacen_compras',array('periodo'=>$periodo,'numero'=>$numero,'total'=>$total,'usuario_id'=>$usuario));
        if(!$ok)return array('error'=>'No se pudo crear la orden de compra.');$id=$compra?$compra->id:$this->db->insert_id();
        $ok=$this->db->where('compra_id',$id)->delete('almacen_compra_detalles') && $this->db->where('compra_id',$id)->delete('almacen_compra_asignaciones');
        foreach($lineas as $linea){if(!$ok)break;$linea['compra_id']=$id;$ok=$this->db->insert('almacen_compra_detalles',$linea);}
        foreach($ids as $aid){if(!$ok)break;$ok=$this->db->insert('almacen_compra_asignaciones',array('compra_id'=>$id,'asignacion_id'=>$aid));}
        return $ok?array('id'=>$id,'nueva'=>!$compra):array('error'=>'No se pudo guardar la consolidación.');
    }
    // $accion: guardar o aprobar (borrador), recibir o reabrir (aprobada). Los permisos por rol se validan en el controlador.
    public function guardar($id,$revision,$cantidades,$proveedor,$observaciones,$usuario,$accion='guardar',$sin_stock_previo=array())
    {
        $o=$this->obtener($id);
        if(!$o)return 'La orden cambió o ya fue recibida. Vuelve a abrirla.';
        $nombre='almacen-compra-'.$o->periodo;
        $q=$this->db->query('SELECT GET_LOCK(?, 5) AS tomado',array($nombre)); $lock=$q?$q->row():null;
        if(!$lock || !$lock->tomado)return 'Hay otra operación de almacén en este mes. Intenta nuevamente.';
        try { return $this->guardar_bloqueado($id,$revision,$cantidades,$proveedor,$observaciones,$usuario,$accion,$sin_stock_previo); }
        finally { $this->db->query('SELECT RELEASE_LOCK(?)',array($nombre)); }
    }
    // Las asignaciones de la orden deben seguir aprobadas, sin despachar y con las mismas cantidades consolidadas.
    private function consolidado_vigente($id,$detalles)
    {
        $incluidas=array_column($this->db->select('asignacion_id AS id')->where('compra_id',$id)->order_by('asignacion_id')->get('almacen_compra_asignaciones')->result(),'id');
        $aprobadas=array();
        if($incluidas){
            $q=$this->db->query("SELECT a.id FROM almacen_asignaciones a JOIN almacen_clientes c ON c.id=a.cliente_id WHERE a.id IN (".implode(',',array_fill(0,count($incluidas),'?')).") AND a.estado='aprobado' AND c.activo=1 AND NOT EXISTS (SELECT 1 FROM almacen_despachos s WHERE s.asignacion_id=a.id) ORDER BY a.id FOR UPDATE",$incluidas);
            if(!$q)return false;
            $aprobadas=array_column($q->result(),'id');
        }
        if($aprobadas!==$incluidas)return false;
        $actuales=$aprobadas?$this->db->select('producto_id,SUM(cantidad) AS solicitado',false)->where_in('asignacion_id',$aprobadas)->where('cantidad >',0)->group_by('producto_id')->order_by('producto_id')->get('almacen_asignacion_detalles')->result():array();
        if(count($actuales)!==count($detalles))return false;
        foreach($actuales as $i=>$actual) if($actual->producto_id!=$detalles[$i]->producto_id || round((float)$actual->solicitado*1000)!==round((float)$detalles[$i]->solicitado*1000)) return false;
        return true;
    }
    private function guardar_bloqueado($id,$revision,$cantidades,$proveedor,$observaciones,$usuario,$accion,$sin_stock_previo=array())
    {
        $this->db->trans_begin();
        $o=$this->db->query('SELECT * FROM almacen_compras WHERE id=? FOR UPDATE',array($id))->row();
        $requerido=in_array($accion,array('guardar','aprobar'),true)?'borrador':'aprobada';
        if(!$o || $o->revision!=$revision){$this->db->trans_rollback();return 'La orden cambió. Vuelve a abrirla.';}
        if($o->estado!==$requerido){$this->db->trans_rollback();return $o->estado==='borrador'?'La orden debe ser aprobada por Administración antes de registrar la recepción.':($o->estado==='aprobada'?'La orden ya fue aprobada; para modificarla, Administración debe reabrirla.':'La orden ya fue recibida.');}
        if($accion==='reabrir'){
            // Una sola orden en borrador por mes.
            if($this->db->query("SELECT 1 FROM almacen_compras WHERE periodo=? AND estado='borrador' AND id<>? LIMIT 1",array($o->periodo,$id))->num_rows()){$this->db->trans_rollback();return 'Ya hay otra orden en borrador este mes. Apruébala antes de reabrir esta.';}
            $ok=$this->db->where('id',$id)->update('almacen_compras',array('estado'=>'borrador','aprobado_por'=>null,'aprobado_en'=>null,'revision'=>$o->revision+1));
            if(!$ok || !$this->db->trans_status()){$this->db->trans_rollback();return 'No se pudo reabrir la orden.';}
            $this->db->trans_commit();return true;
        }
        $recibir=$accion==='recibir';
        $detalles=$this->db->order_by('producto_id')->get_where('almacen_compra_detalles',array('compra_id'=>$id))->result();
        if(($accion==='aprobar' || $recibir) && !$this->consolidado_vigente($id,$detalles)){
            $this->db->trans_rollback();
            return $recibir?'Las asignaciones de esta orden cambiaron. Administración debe reabrirla y actualizar el consolidado.':'Hay cambios en las asignaciones de esta orden. Actualiza el consolidado antes de aprobar.';
        }
        $total=0;$entradas=0;$ok=true;$ids=array();$orden=self::codigo($o);
        foreach($detalles as $d){
            $ids[]=(string)$d->producto_id;
            // Aprobada la orden, las cantidades quedan fijas: Almacén recibe lo aprobado.
            $cantidad=$recibir?(string)$d->cantidad:($cantidades[$d->producto_id]??null);
            if(!is_string($cantidad)||!preg_match('/^\d{1,9}(?:\.\d{1,3})?$/D',$cantidad)){$this->db->trans_rollback();return 'Revisa las cantidades a adquirir.';}
            $importe=round($cantidad*$d->precio_real,2);$total+=$importe;
            if($total>9999999999999.99){$this->db->trans_rollback();return 'El importe supera el rango permitido.';}
            if(!$recibir){
                $ajustado=$d->ajustado || round((float)$cantidad,3)!==round((float)$d->cantidad,3);
                $ok=$ok && $this->db->where('id',$d->id)->update('almacen_compra_detalles',array('cantidad'=>$cantidad,'total'=>$importe,'ajustado'=>$ajustado?1:0));
            }
            $p=null;
            // Quien recibe declara que no había existencias previas: se registra el conteo (0) antes de la entrada.
            if($recibir && isset($sin_stock_previo[$d->producto_id])){
                $p=$this->db->query('SELECT * FROM almacen_productos WHERE id=? FOR UPDATE',array($d->producto_id))->row();
                if($p && !$p->stock_confirmado){
                    if(!$p->activo || $p->unidad==='POR DEFINIR'){$this->db->trans_rollback();return 'Revisa el estado y la unidad de los productos antes de registrar la recepción.';}
                    $ok=$ok && $this->db->insert('almacen_movimientos',array('producto_id'=>$p->id,'tipo'=>'ajuste','cantidad'=>-$p->stock,'stock_anterior'=>$p->stock,'stock_nuevo'=>0,'motivo'=>'Conteo al recibir '.$orden.': sin stock previo','usuario_id'=>$usuario));
                    $ok=$ok && $this->db->where('id',$p->id)->update('almacen_productos',array('stock'=>0,'stock_confirmado'=>1));
                    $p->stock=0; $p->stock_confirmado=1;
                }
            }
            if($recibir && (float)$cantidad>0){
                if(!$p) $p=$this->db->query('SELECT * FROM almacen_productos WHERE id=? FOR UPDATE',array($d->producto_id))->row();
                if(!$p || !$p->activo || $p->unidad!==$d->unidad || $p->unidad==='POR DEFINIR'){$this->db->trans_rollback();return 'Revisa el estado y la unidad de los productos antes de registrar la recepción.';}
                $nuevo=round($p->stock+$cantidad,3);
                if($nuevo>999999999.999){$this->db->trans_rollback();return 'La recepción supera el rango de stock permitido.';}
                // Preserve pending verification: purchased units do not prove the preexisting physical balance.
                $ok=$ok && $this->db->where('id',$p->id)->update('almacen_productos',array('stock'=>$nuevo));
                $ok=$ok && $this->db->insert('almacen_movimientos',array('producto_id'=>$p->id,'tipo'=>'entrada','cantidad'=>$cantidad,'stock_anterior'=>$p->stock,'stock_nuevo'=>$nuevo,'motivo'=>'Recepción '.$orden,'proveedor'=>mb_substr($o->proveedor,0,200),'usuario_id'=>$usuario));$entradas++;
            }
        }
        foreach(array_merge(array_keys($cantidades),array_keys($sin_stock_previo)) as $pid)if(!in_array((string)$pid,$ids,true)){$this->db->trans_rollback();return 'Se recibió un producto ajeno a la orden.';}
        if($recibir && !$entradas){$this->db->trans_rollback();return 'No hay cantidades a adquirir para recibir.';}
        $data=array('revision'=>$o->revision+1,'usuario_id'=>$usuario);
        if(!$recibir) $data=array_merge($data,array('proveedor'=>$proveedor,'observaciones'=>$observaciones,'total'=>$total));
        if($accion==='aprobar') $data=array_merge($data,array('estado'=>'aprobada','aprobado_por'=>$usuario,'aprobado_en'=>date('Y-m-d H:i:s')));
        if($recibir) $data=array_merge($data,array('estado'=>'recibida','recibido_por'=>$usuario,'recibido_en'=>date('Y-m-d H:i:s')));
        $ok=$ok && $this->db->where('id',$id)->update('almacen_compras',$data);
        if(!$ok || !$this->db->trans_status()){$this->db->trans_rollback();return 'No se pudo guardar la orden.';}
        $this->db->trans_commit();return true;
    }
    public function nombre_usuario($id)
    {
        $u=$id?$this->db->select('nombre_usu,apellido_pat')->get_where('Usuarios',array('id_usu'=>$id))->row():null;
        return $u?trim($u->nombre_usu.' '.$u->apellido_pat):'';
    }
}
