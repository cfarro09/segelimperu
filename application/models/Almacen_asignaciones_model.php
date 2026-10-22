<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Almacen_asignaciones_model extends CI_Model
{
    public function __construct() { parent::__construct(); $this->load->database(); }
    public function obtener($id) { return $this->db->get_where('almacen_asignaciones', array('id' => $id))->row(); }
    public function por_cliente_periodo($cliente_id, $periodo) { return $this->db->get_where('almacen_asignaciones', array('cliente_id' => $cliente_id, 'periodo' => $periodo))->row(); }
    public function detalles($id) { return $this->db->order_by("CASE categoria WHEN 'MATERIALES' THEN 0 WHEN 'IMPLEMENTOS' THEN 1 ELSE 2 END", 'ASC', false)->order_by('categoria')->order_by('descripcion')->order_by('producto_id')->get_where('almacen_asignacion_detalles', array('asignacion_id' => $id))->result(); }
    public function saldo_anterior($cliente_id, $periodo)
    {
        $c = $this->db->get_where('almacen_clientes', array('id' => $cliente_id))->row();
        if (!$c) return 0;
        $sum = $this->db->select('COALESCE(SUM(presupuesto - total), 0) AS saldo', false)->where('cliente_id', $cliente_id)->where('periodo <', $periodo)->where('estado','aprobado')->get('almacen_asignaciones')->row();
        return round((float) $c->saldo_inicial + (float) $sum->saldo, 2);
    }
    private function filtro($cliente, $periodo, $estado = '', $supervisor_id = null)
    {
        if ($cliente) $this->db->where('a.cliente_id', $cliente);
        if ($supervisor_id !== null) $this->db->where('a.cliente_id IN (SELECT id FROM almacen_clientes WHERE supervisor_id = '.(int) $supervisor_id.')', null, false);
        if ($periodo !== '') $this->db->where('a.periodo', $periodo);
        if($estado==='revision') $this->db->where_in('a.estado',array('publicado','aprobado','rechazado'));
        elseif ($estado !== '') $this->db->where('a.estado',$estado);
    }
    public function total($cliente, $periodo, $estado = '') { $this->filtro($cliente, $periodo, $estado); return $this->db->count_all_results('almacen_asignaciones a'); }
    public function listar($cliente, $periodo, $limite = null, $offset = 0, $estado = '', $supervisor_id = null)
    {
        $this->filtro($cliente, $periodo, $estado, $supervisor_id);
        if($limite !== null) $this->db->limit($limite, $offset);
        return $this->db->select('a.*, c.nombre, c.grupo')->join('almacen_clientes c', 'c.id = a.cliente_id')->order_by('a.periodo', 'DESC')->order_by('a.id', 'DESC')->get('almacen_asignaciones a')->result();
    }
    public function guardar($id, $revision, $cliente_id, $periodo, $presupuesto, $cantidades, $observaciones, $usuario, $publicar = false)
    {
        $this->db->trans_begin();
        // Serialize each client's saves, including inserts for a month not created yet.
        $cliente = $this->db->query('SELECT * FROM almacen_clientes WHERE id = ? FOR UPDATE', array($cliente_id))->row();
        if (!$cliente || !$cliente->activo) { $this->db->trans_rollback(); return array('error' => 'El cliente no existe o está inactivo.'); }
        $anterior = null; $previos = array();
        if ($id) {
            $anterior = $this->db->query('SELECT * FROM almacen_asignaciones WHERE id = ? FOR UPDATE', array($id))->row();
            if (!$anterior || $anterior->cliente_id != $cliente_id || $anterior->periodo !== $periodo) { $this->db->trans_rollback(); return array('error' => 'El cliente y período de una asignación existente no pueden cambiar.'); }
            if ($anterior->revision != $revision) { $this->db->trans_rollback(); return array('error' => 'Otra sesión modificó esta asignación. Vuelve a abrirla antes de guardar.'); }
            if (!in_array($anterior->estado,array('borrador','rechazado'),true)) { $this->db->trans_rollback(); return array('error'=>'La asignación publicada o aprobada no se puede modificar.'); }
            foreach ($this->detalles($id) as $d) $previos[$d->producto_id] = $d;
        } elseif ($this->db->get_where('almacen_asignaciones', array('cliente_id' => $cliente_id, 'periodo' => $periodo))->num_rows()) {
            $this->db->trans_rollback(); return array('error' => 'Este cliente ya tiene asignación en ese mes. Ábrela desde el listado para editarla.');
        }
        $lineas = array(); $centimos = 0;
        foreach ($cantidades as $producto_id => $cantidad) {
            if (!preg_match('/^\d{1,9}(?:\.\d{1,3})?$/D', (string) $cantidad) || !ctype_digit((string) $producto_id) || (int) $producto_id < 1) { $this->db->trans_rollback(); return array('error' => 'Hay cantidades o productos inválidos.'); }
            if ((float) $cantidad == 0) continue;
            $p = $this->db->get_where('almacen_productos', array('id' => $producto_id))->row();
            if (!$p || (!$p->activo && !isset($previos[$producto_id]))) { $this->db->trans_rollback(); return array('error' => 'Un producto no está disponible. Revisa el catálogo.'); }
            if($publicar && $p->unidad==='POR DEFINIR') { $this->db->trans_rollback();return array('error'=>'Define la unidad del producto '.$p->descripcion.' antes de publicar la asignación.'); }
            $base = isset($previos[$producto_id]) ? $previos[$producto_id] : $p;
            $valor = (float) $cantidad * (float) $base->precio_valorado;
            if ($valor > 9999999999999.99) { $this->db->trans_rollback(); return array('error' => 'El importe supera el rango permitido.'); }
            $importe = (int) round($valor * 100);
            $centimos += $importe;
            if ($importe > 999999999999999 || $centimos > 999999999999999) { $this->db->trans_rollback(); return array('error' => 'El importe supera el rango permitido.'); }
            $lineas[] = array('producto_id' => $producto_id, 'descripcion' => $base->descripcion, 'categoria' => $base->categoria, 'unidad' => $base->unidad, 'marca' => $base->marca, 'cantidad' => $cantidad, 'precio_valorado' => $base->precio_valorado, 'precio_real' => $base->precio_real === null ? $base->precio_valorado : $base->precio_real, 'total' => $importe / 100);
        }
        if ($publicar && !$lineas) { $this->db->trans_rollback(); return array('error'=>'Agrega al menos un producto con cantidad mayor a cero antes de publicar.'); }
        $presupuesto = $anterior ? $anterior->presupuesto : $cliente->presupuesto;
        $estado = $publicar ? 'publicado' : 'borrador';
        $data = array('cliente_id' => $cliente_id, 'periodo' => $periodo, 'presupuesto' => $presupuesto, 'total' => $centimos / 100, 'observaciones' => $observaciones, 'usuario_id' => $usuario, 'revision' => $anterior ? $anterior->revision + 1 : 1, 'estado'=>$estado);
        if ($publicar) $data = array_merge($data,array('publicado_por'=>$usuario,'publicado_en'=>date('Y-m-d H:i:s'),'revisado_por'=>null,'revisado_en'=>null,'comentario_revision'=>''));
        $ok = $id ? $this->db->where('id', $id)->update('almacen_asignaciones', $data) : $this->db->insert('almacen_asignaciones', $data);
        if (!$id && $ok) $id = $this->db->insert_id();
        if ($ok) $ok = $this->db->where('asignacion_id', $id)->delete('almacen_asignacion_detalles');
        foreach ($lineas as $linea) {
            if (!$ok) break;
            $linea['asignacion_id'] = $id; $ok = $this->db->insert('almacen_asignacion_detalles', $linea);
        }
        if ($ok && (!$anterior || $anterior->estado !== $estado)) $ok = $this->db->insert('almacen_asignacion_eventos',array('asignacion_id'=>$id,'estado_anterior'=>$anterior?$anterior->estado:'','estado_nuevo'=>$estado,'usuario_id'=>$usuario,'comentario'=>''));
        if (!$ok || !$this->db->trans_status()) { $this->db->trans_rollback(); return array('error' => 'No se pudo guardar la asignación.'); }
        $this->db->trans_commit(); return array('id' => $id);
    }

    public function restriccion_ajuste($a)
    {
        if (!in_array($a->estado, array('publicado', 'aprobado'), true)) return 'Solo se ajustan asignaciones pendientes de revisión o aprobadas.';
        $cliente = $this->db->get_where('almacen_clientes', array('id'=>$a->cliente_id))->row();
        if (!$cliente || !$cliente->activo) return 'El cliente está inactivo.';
        if ($this->db->get_where('almacen_despachos', array('asignacion_id'=>$a->id))->num_rows()) return 'La asignación ya fue despachada.';
        $recibida = $this->db->select('c.id')->join('almacen_compras c', 'c.id=l.compra_id')
            ->where('l.asignacion_id', $a->id)->where_in('c.estado', array('aprobada', 'recibida'))->get('almacen_compra_asignaciones l')->row();
        return $recibida ? 'La asignación forma parte de una orden de compra aprobada o recibida.' : '';
    }

    public function ajustar_cantidades($id, $revision, $cantidades, $usuario)
    {
        $referencia = $this->obtener($id);
        if (!$referencia) return array('error'=>'La asignación no existe.');
        // Share the monthly lock with consolidation, receipt and dispatch.
        $nombre = 'almacen-compra-'.$referencia->periodo;
        $q = $this->db->query('SELECT GET_LOCK(?, 5) AS tomado', array($nombre));
        if (!$q || !$q->row()->tomado) return array('error'=>'Hay otra operación de almacén en este mes. Intenta nuevamente.');
        try {
            $this->db->trans_begin();
            // Match the supervisor save's client/assignment lock order.
            $q = $this->db->query('SELECT id FROM almacen_clientes WHERE id=? FOR UPDATE', array($referencia->cliente_id));
            if (!$q) { $this->db->trans_rollback(); return array('error'=>'No se pudo verificar el cliente.'); }
            $q = $this->db->query('SELECT * FROM almacen_asignaciones WHERE id=? FOR UPDATE', array($id));
            $a = $q ? $q->row() : null;
            if (!$a || $a->revision != $revision || $a->cliente_id != $referencia->cliente_id || $a->periodo !== $referencia->periodo) {
                $this->db->trans_rollback(); return array('error'=>'Otra sesión modificó esta asignación. Vuelve a abrirla antes de guardar.');
            }
            $error = $this->restriccion_ajuste($a);
            if ($error) { $this->db->trans_rollback(); return array('error'=>$error); }
            $q = $this->db->query('SELECT * FROM almacen_asignacion_detalles WHERE asignacion_id=? ORDER BY producto_id FOR UPDATE', array($id));
            if (!$q) { $this->db->trans_rollback(); return array('error'=>'No se pudieron verificar los productos.'); }
            $detalles = $q->result(); $lineas = array(); $centimos = 0; $positivas = 0;
            if (!is_array($cantidades) || count($cantidades) !== count($detalles) || !$detalles) {
                $this->db->trans_rollback(); return array('error'=>'Los productos de la asignación cambiaron. Vuelve a abrirla.');
            }
            foreach ($detalles as $d) {
                $cantidad = $cantidades[$d->producto_id] ?? null;
                if (!is_string($cantidad) || !preg_match('/^\d{1,9}(?:\.\d{1,3})?$/D', $cantidad)) {
                    $this->db->trans_rollback(); return array('error'=>'Revisa las cantidades: usa valores positivos o cero, con hasta 3 decimales.');
                }
                $milesimas = (int)round((float)$cantidad * 1000);
                $precio_centimos = (int)round((float)$d->precio_valorado * 100);
                // Exact cent rounding, with a bound before the integer multiplication.
                if ($precio_centimos > 0 && $milesimas > intdiv(999999999999999499, $precio_centimos)) {
                    $this->db->trans_rollback(); return array('error'=>'El importe supera el rango permitido.');
                }
                $importe = intdiv($milesimas * $precio_centimos + 500, 1000);
                $centimos += $importe; $positivas += $milesimas > 0 ? 1 : 0;
                if ($importe > 999999999999999 || $centimos > 999999999999999) {
                    $this->db->trans_rollback(); return array('error'=>'El importe supera el rango permitido.');
                }
                $lineas[] = array('detalle'=>$d, 'cantidad'=>$cantidad, 'total'=>sprintf('%d.%02d', intdiv($importe,100), $importe%100));
            }
            if (!$positivas) { $this->db->trans_rollback(); return array('error'=>'Conserva al menos un producto con cantidad mayor a cero.'); }
            $ok = true; $cambios = 0;
            foreach ($lineas as $linea) {
                $d = $linea['detalle'];
                if (round((float)$d->cantidad*1000) === round((float)$linea['cantidad']*1000)) continue;
                $cambios++;
                // Keep zero rows and their original snapshots so quantities can be restored.
                $ok = $ok && $this->db->where('id', $d->id)->update('almacen_asignacion_detalles', array('cantidad'=>$linea['cantidad'], 'total'=>$linea['total']));
                $comentario = 'Ajuste de cantidades: '.$d->descripcion.' (#'.$d->producto_id.') · '.$d->cantidad.' → '.number_format((float)$linea['cantidad'], 3, '.', '').' '.$d->unidad;
                if ($ok) $ok = $this->db->insert('almacen_asignacion_eventos', array('asignacion_id'=>$id, 'estado_anterior'=>$a->estado, 'estado_nuevo'=>$a->estado, 'usuario_id'=>$usuario, 'comentario'=>$comentario));
            }
            if ($ok && $cambios) $ok = $this->db->where('id', $id)->update('almacen_asignaciones', array('total'=>sprintf('%d.%02d', intdiv($centimos,100), $centimos%100), 'revision'=>$a->revision+1));
            if (!$ok || !$this->db->trans_status()) { $this->db->trans_rollback(); return array('error'=>'No se pudieron guardar las cantidades.'); }
            $this->db->trans_commit(); return array('id'=>$id, 'cambios'=>$cambios);
        } finally {
            $this->db->query('SELECT RELEASE_LOCK(?)', array($nombre));
        }
    }

    public function revisar($id, $revision, $decision, $comentario, $usuario)
    {
        if(!in_array($decision,array('aprobado','rechazado'),true) || !is_string($comentario) || mb_strlen($comentario)>1000 || ($decision==='rechazado' && trim($comentario)==='')) return 'Indica una decisión válida y el motivo del rechazo.';
        $this->db->trans_begin();
        $a=$this->db->query('SELECT * FROM almacen_asignaciones WHERE id = ? FOR UPDATE',array($id))->row();
        if(!$a || $a->estado!=='publicado' || $a->revision!=$revision) { $this->db->trans_rollback(); return 'La asignación ya fue revisada o cambió. Actualiza la bandeja.'; }
        $ok=$this->db->where('id',$id)->update('almacen_asignaciones',array('estado'=>$decision,'revision'=>$a->revision+1,'revisado_por'=>$usuario,'revisado_en'=>date('Y-m-d H:i:s'),'comentario_revision'=>trim($comentario)));
        if($ok) $ok=$this->db->insert('almacen_asignacion_eventos',array('asignacion_id'=>$id,'estado_anterior'=>'publicado','estado_nuevo'=>$decision,'usuario_id'=>$usuario,'comentario'=>trim($comentario)));
        if(!$ok || !$this->db->trans_status()) { $this->db->trans_rollback(); return 'No se pudo registrar la revisión.'; }
        $this->db->trans_commit(); return true;
    }
    public function eventos($id) { return $this->db->order_by('id','DESC')->get_where('almacen_asignacion_eventos',array('asignacion_id'=>$id))->result(); }
}
