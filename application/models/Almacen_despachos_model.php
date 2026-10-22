<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Almacen_despachos_model extends CI_Model
{
    private $lock='';
    public function __construct() { parent::__construct(); $this->load->database(); }
    public function obtener($id) { return $this->db->get_where('almacen_despachos',array('id'=>$id))->row(); }
    public function de_asignacion($id) { return $this->db->get_where('almacen_despachos',array('asignacion_id'=>$id))->row(); }
    public function detalles($id) { return $this->db->order_by('id')->get_where('almacen_despacho_detalles',array('despacho_id'=>$id))->result(); }
    // Guía de traslado - almacén: serie propia del sistema (0002) para no cruzarse con el talonario impreso (0001).
    const SERIE_GUIA='0002';
    public static function numero($d) { return self::SERIE_GUIA.'-'.str_pad($d->id,6,'0',STR_PAD_LEFT); }
    public function del_mes($periodo)
    {
        $registros=array();
        foreach ($this->db->get_where('almacen_despachos',array('periodo'=>$periodo))->result() as $d) $registros[$d->asignacion_id]=$d;
        return $registros;
    }
    public function pendientes($periodo)
    {
        return $this->db->join('almacen_clientes c','c.id=a.cliente_id')->join('almacen_despachos s','s.asignacion_id=a.id','left')
            ->where('a.periodo',$periodo)->where('a.estado','aprobado')->where('c.activo',1)->where('s.id IS NULL',null,false)->count_all_results('almacen_asignaciones a');
    }
    private function milesimas($cantidad) { return (int)round((float)$cantidad*1000); }
    private function problema($a,$c,$d,$p)
    {
        if (!$p || !$p->activo) return 'Producto inactivo';
        if ($p->unidad==='POR DEFINIR' || $p->unidad!==$d->unidad) return 'Unidad modificada o sin definir';
        if (!$p->stock_confirmado) return 'Conteo pendiente';
        if ($this->milesimas($d->cantidad)<=0) return 'Cantidad inválida';
        if ($this->milesimas($p->stock)<$this->milesimas($d->cantidad)) return 'Stock insuficiente';
        return '';
    }
    public function preparar($id)
    {
        $a=$this->db->get_where('almacen_asignaciones',array('id'=>$id))->row();
        if (!$a) return null;
        $c=$this->db->get_where('almacen_clientes',array('id'=>$a->cliente_id))->row();
        $s=$this->de_asignacion($id); $errores=array(); $lineas=array();
        if ($a->estado!=='aprobado') $errores[]='La asignación debe estar aprobada.';
        if (!$c || !$c->activo) $errores[]='El cliente está inactivo.';
        $generales=$errores;
        foreach ($this->db->where('cantidad >',0)->order_by('producto_id')->get_where('almacen_asignacion_detalles',array('asignacion_id'=>$id))->result() as $d) {
            $p=$this->db->get_where('almacen_productos',array('id'=>$d->producto_id))->row();
            $d->stock=$p && $p->stock_confirmado?$p->stock:null;
            $d->problema=$this->problema($a,$c,$d,$p);
            $d->faltante=$d->stock===null?null:max(0,($this->milesimas($d->cantidad)-$this->milesimas($d->stock))/1000);
            if ($d->problema!=='') $errores[]=$d->descripcion.': '.$d->problema.'.';
            $lineas[]=$d;
        }
        if (!$lineas) { $errores[]='La asignación no contiene productos.'; $generales[]='La asignación no contiene productos.'; }
        return array('asignacion'=>$a,'cliente'=>$c,'despacho'=>$s,'detalles'=>$lineas,'errores'=>$errores,'errores_generales'=>$generales,'puede_despachar'=>!$s && !$errores);
    }
    public function despachar($id,$revision,$usuario)
    {
        $referencia=$this->db->get_where('almacen_asignaciones',array('id'=>$id))->row();
        if (!$referencia) return array('errores'=>array('La asignación no existe.'));
        // Share the monthly lock with consolidation and receipt: neither can use
        // an old demand snapshot while a delivery is being committed.
        $nombre='almacen-compra-'.$referencia->periodo;
        $q=$this->db->query('SELECT GET_LOCK(?, 5) AS tomado',array($nombre));
        $tomado=$q?$q->row():null;
        if (!$tomado || !$tomado->tomado) return array('errores'=>array('Hay otra operación de almacén en este mes. Intenta nuevamente.'));
        $this->lock=$nombre;
        // Approval remains intact for budgets. A unique delivery records the physical exit.
        $this->db->trans_begin();
        $q=$this->db->query('SELECT * FROM almacen_asignaciones WHERE id=? FOR UPDATE',array($id));
        $a=$q?$q->row():null;
        if (!$a) return $this->fallar('La asignación no existe o no se pudo consultar.');
        $s=$this->de_asignacion($id);
        // A retry after a lost response returns the original guide, without new movements.
        if ($s) { $this->db->trans_rollback(); $this->liberar(); return array('id'=>(int)$s->id,'existente'=>true); }
        if ($a->estado!=='aprobado' || (int)$a->revision!==$revision) return $this->fallar('La asignación cambió o no está aprobada. Vuelve a abrirla.');
        $q=$this->db->query('SELECT * FROM almacen_clientes WHERE id=? FOR UPDATE',array($a->cliente_id));
        $c=$q?$q->row():null;
        if (!$c || !$c->activo) return $this->fallar('El cliente está inactivo.');
        $q=$this->db->query('SELECT * FROM almacen_asignacion_detalles WHERE asignacion_id=? AND cantidad>0 ORDER BY producto_id FOR UPDATE',array($id));
        $detalles=$q?$q->result():array();
        if (!$detalles) return $this->fallar('La asignación no contiene productos.');
        $productos=array(); $errores=array();
        // Lock every product in the same order used by purchase receipts/consolidation.
        // Validate the complete delivery before writing any exit.
        foreach ($detalles as $d) {
            $q=$this->db->query('SELECT * FROM almacen_productos WHERE id=? FOR UPDATE',array($d->producto_id));
            if (!$q) return $this->fallar('No se pudo verificar el stock. Vuelve a intentar.');
            $p=$q->row(); $problema=$this->problema($a,$c,$d,$p);
            if ($problema!=='') $errores[]=$d->descripcion.': '.$problema.'.';
            $productos[$d->producto_id]=$p;
        }
        if ($errores) { $this->db->trans_rollback(); $this->liberar(); return array('errores'=>$errores); }
        $fecha=(new DateTimeImmutable('now',new DateTimeZone('America/Lima')))->format('Y-m-d H:i:s');
        $ok=$this->db->insert('almacen_despachos',array('asignacion_id'=>$id,'cliente_id'=>$c->id,'cliente_codigo'=>$c->codigo,'cliente_nombre'=>$c->nombre,'cliente_ruc'=>$c->ruc,'cliente_direccion'=>$c->direccion,'periodo'=>$a->periodo,'usuario_id'=>$usuario,'creado_en'=>$fecha));
        $sid=$this->db->insert_id();
        foreach ($detalles as $d) {
            if (!$ok) break;
            $p=$productos[$d->producto_id];
            $nuevo=($this->milesimas($p->stock)-$this->milesimas($d->cantidad))/1000;
            $ok=$this->db->where('id',$p->id)->update('almacen_productos',array('stock'=>$nuevo)) &&
                $this->db->insert('almacen_movimientos',array('producto_id'=>$p->id,'tipo'=>'salida','cantidad'=>-$this->milesimas($d->cantidad)/1000,'stock_anterior'=>$p->stock,'stock_nuevo'=>$nuevo,'motivo'=>'Despacho #'.$sid.' · Asignación #'.$id.' · Cliente '.$c->codigo,'usuario_id'=>$usuario));
            $mid=$this->db->insert_id();
            if ($ok) $ok=$this->db->insert('almacen_despacho_detalles',array('despacho_id'=>$sid,'producto_id'=>$p->id,'movimiento_id'=>$mid,'codigo'=>$p->codigo,'descripcion'=>$d->descripcion,'unidad'=>$d->unidad,'marca'=>$d->marca,'cantidad'=>$d->cantidad,'stock_anterior'=>$p->stock,'stock_nuevo'=>$nuevo));
        }
        if (!$ok || !$this->db->trans_status()) return $this->fallar('No se pudo registrar el despacho. No se descontó stock.');
        $this->db->trans_commit(); $this->liberar(); return array('id'=>(int)$sid,'existente'=>false);
    }
    private function liberar() { if ($this->lock!=='') { $this->db->query('SELECT RELEASE_LOCK(?)',array($this->lock)); $this->lock=''; } }
    private function fallar($mensaje) { $this->db->trans_rollback(); $this->liberar(); return array('errores'=>array($mensaje)); }
}
