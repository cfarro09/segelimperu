<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Almacen_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function filtrar_productos($buscar, $estado)
    {
        if ($buscar !== '') {
            $this->db->group_start()->like('descripcion', $buscar)->or_like('codigo', $buscar)
                ->or_like('marca', $buscar)->or_like('categoria', $buscar)->group_end();
        }
        if ($estado === '0' || $estado === '1') $this->db->where('activo', $estado);
    }

    public function total_productos($buscar = '', $estado = '')
    {
        $this->filtrar_productos($buscar, $estado);
        return $this->db->count_all_results('almacen_productos');
    }

    public function productos($buscar = '', $estado = '', $limite = null, $offset = 0)
    {
        $this->filtrar_productos($buscar, $estado);
        if ($limite !== null) $this->db->limit((int) $limite, max(0, (int) $offset));
        $this->seleccionar_valores_stock();
        return $this->db->order_by('descripcion')->order_by('unidad')->order_by('id')->get('almacen_productos')->result();
    }

    private function valor_stock_sql($precio)
    {
        return 'CASE WHEN stock_confirmado = 1 THEN ROUND(stock * '.$precio.', 2) ELSE NULL END';
    }

    private function seleccionar_valores_stock()
    {
        $this->db->select('*, '.$this->valor_stock_sql('COALESCE(precio_real, precio_valorado)').' AS valor_stock_real, '.$this->valor_stock_sql('precio_valorado').' AS valor_stock_valorado', false);
    }

    public function resumen_stock()
    {
        return $this->db->select('COALESCE(SUM('.$this->valor_stock_sql('COALESCE(precio_real, precio_valorado)').'), 0) AS valor_real, COALESCE(SUM('.$this->valor_stock_sql('precio_valorado').'), 0) AS valor_valorado, COALESCE(SUM(CASE WHEN stock_confirmado = 0 THEN 1 ELSE 0 END), 0) AS pendientes', false)->get('almacen_productos')->row();
    }

    public function producto($id)
    {
        $this->seleccionar_valores_stock();
        return $this->db->get_where('almacen_productos', array('id' => $id))->row();
    }

    public function producto_por_codigo($codigo)
    {
        $this->seleccionar_valores_stock();
        return $this->db->get_where('almacen_productos',array('codigo'=>$codigo))->row();
    }

    public function movimientos($id)
    {
        return $this->db->order_by('id', 'DESC')->get_where('almacen_movimientos', array('producto_id' => $id))->result();
    }

    const CATEGORIAS = array('MATERIALES' => 'Materiales', 'IMPLEMENTOS' => 'Implementos', 'VENENOS' => 'Venenos');
    public function guardar($id, $data, $stock, $usuario)
    {
        if (!isset($data['precio_real']) || $data['precio_real'] === '') $data['precio_real'] = $data['precio_valorado'];
        $this->db->trans_begin();
        if ($id) {
            $actual = $this->db->query('SELECT * FROM almacen_productos WHERE id = ? FOR UPDATE', array($id))->row();
            if (!$actual) { $this->db->trans_rollback(); return 'El producto ya no existe.'; }
            if ($actual->stock > 0 && $actual->unidad !== $data['unidad']) {
                $this->db->trans_rollback(); return 'No puedes cambiar la unidad de un producto con stock. Registra la presentación nueva como otro producto.';
            }
        }
        $duplicate = $this->db->where('codigo', $data['codigo']);
        if ($id) $duplicate->where('id !=', $id);
        if ($duplicate->count_all_results('almacen_productos')) {
            $this->db->trans_rollback(); return 'Ya existe un producto con ese código.';
        }
        // Stock only changes through an audited movement, never through editing the catalog.
        if ($id) {
            $ok = $this->db->where('id', $id)->update('almacen_productos', $data);
        } else {
            $data['stock'] = $stock;
            $data['stock_confirmado'] = 1;
            $ok = $this->db->insert('almacen_productos', $data);
            $id = $this->db->insert_id();
            if ($ok) $ok = $this->db->insert('almacen_movimientos', array(
                'producto_id' => $id, 'tipo' => 'inicial', 'cantidad' => $stock,
                'stock_anterior' => 0, 'stock_nuevo' => $stock,
                'motivo' => 'Stock inicial al registrar el producto', 'usuario_id' => $usuario
            ));
        }
        if (!$ok || !$this->db->trans_status()) {
            $this->db->trans_rollback(); return 'No se pudo guardar el producto. Verifica el código e inténtalo de nuevo.';
        }
        $this->db->trans_commit();
        return true;
    }

    public function mover($id, $tipo, $cantidad, $motivo, $usuario, $proveedor = '', $factura = '')
    {
        if (!in_array($tipo, array('entrada', 'salida', 'ajuste'), true) || !is_numeric($cantidad) || $cantidad < 0 || ($tipo !== 'ajuste' && $cantidad == 0)) return 'Movimiento inválido.';
        $this->db->trans_begin();
        $p = $this->db->query('SELECT * FROM almacen_productos WHERE id = ? FOR UPDATE', array($id))->row();
        if (!$p || !$p->activo) { $this->db->trans_rollback(); return 'El producto no existe o está inactivo.'; }
        if ($p->unidad === 'POR DEFINIR') { $this->db->trans_rollback(); return 'Define la unidad del producto antes de registrar su inventario.'; }
        if (!$p->stock_confirmado && $tipo !== 'ajuste') {
            $this->db->trans_rollback(); return 'Primero registra el conteo físico mediante un ajuste de inventario.';
        }
        // Compute with integer thousandths to avoid floating point drift.
        $anterior = (int) round($p->stock * 1000);
        $milesimas = (int) round($cantidad * 1000);
        $nuevo = $tipo === 'ajuste' ? $milesimas : $anterior + ($tipo === 'entrada' ? $milesimas : -$milesimas);
        if ($nuevo < 0 || $nuevo > 999999999999) { $this->db->trans_rollback(); return 'El movimiento supera el stock disponible o el rango permitido.'; }
        $ok = $this->db->where('id', $id)->update('almacen_productos', array('stock' => $nuevo / 1000, 'stock_confirmado' => 1));
        if ($ok) $ok = $this->db->insert('almacen_movimientos', array(
            'producto_id' => $id, 'tipo' => $tipo, 'cantidad' => ($nuevo - $anterior) / 1000,
            'stock_anterior' => $p->stock, 'stock_nuevo' => $nuevo / 1000,
            'motivo' => $motivo, 'proveedor' => $tipo === 'entrada' ? $proveedor : '', 'factura' => $tipo === 'entrada' ? $factura : '', 'usuario_id' => $usuario
        ));
        if (!$ok || !$this->db->trans_status()) { $this->db->trans_rollback(); return 'No se pudo registrar el movimiento.'; }
        $this->db->trans_commit();
        return true;
    }
}
