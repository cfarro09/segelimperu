<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Almacen_clientes_model extends CI_Model
{
    public $ultimo_id = null;
    const MODALIDADES = array('DIARIO' => 'Diario', 'PART TIME' => 'Part time', 'INTERDIARIO' => 'Interdiario');
    const DIAS = array('lun' => 'Lunes', 'mar' => 'Martes', 'mie' => 'Miércoles', 'jue' => 'Jueves', 'vie' => 'Viernes', 'sab' => 'Sábado', 'dom' => 'Domingo');
    const DIAS_CORTOS = array('lun' => 'Lun', 'mar' => 'Mar', 'mie' => 'Mié', 'jue' => 'Jue', 'vie' => 'Vie', 'sab' => 'Sáb', 'dom' => 'Dom');
    public function __construct() { parent::__construct(); $this->load->database(); }
    private function base()
    {
        $this->db->select("c.*, TRIM(CONCAT(u.nombre_usu, ' ', u.apellido_pat)) AS supervisor", false)
            ->from('almacen_clientes c')->join('Usuarios u', 'u.id_usu = c.supervisor_id', 'left');
    }
    private function filtrar($buscar, $estado, $supervisor_id = null)
    {
        if ($buscar !== '') $this->db->group_start()->like('c.nombre', $buscar)->or_like('c.codigo', $buscar)->or_like('c.grupo', $buscar)->or_like('u.nombre_usu', $buscar)->or_like('u.apellido_pat', $buscar)->group_end();
        if ($estado === '0' || $estado === '1') $this->db->where('c.activo', $estado);
        // Un supervisor solo trabaja con los clientes que tiene asignados.
        if ($supervisor_id !== null) $this->db->where('c.supervisor_id', (int) $supervisor_id);
    }
    public function total($buscar, $estado, $supervisor_id = null) { $this->base(); $this->filtrar($buscar, $estado, $supervisor_id); return $this->db->count_all_results(); }
    public function listar($buscar = '', $estado = '1', $limite = null, $offset = 0, $supervisor_id = null)
    {
        $this->base(); $this->filtrar($buscar, $estado, $supervisor_id);
        if ($limite !== null) $this->db->limit($limite, $offset);
        return $this->con_horarios($this->db->order_by('c.nombre')->order_by('c.id')->get()->result());
    }
    public function obtener($id) { $this->base(); $c = $this->db->where('c.id', $id)->get()->row(); return $c ? $this->con_horarios(array($c))[0] : null; }
    // Agrega a cada cliente sus filas de horario (días como arreglo, horas HH:MM) con una sola consulta.
    private function con_horarios($clientes)
    {
        if (!$clientes) return $clientes;
        $porCliente = array();
        $filas = $this->db->select("cliente_id, dias, TIME_FORMAT(hora_ingreso,'%H:%i') AS entrada, TIME_FORMAT(hora_salida,'%H:%i') AS salida, detalle", false)
            ->where_in('cliente_id', array_map(function ($c) { return $c->id; }, $clientes))->order_by('cliente_id')->order_by('orden')->order_by('id')->get('almacen_cliente_horarios')->result();
        foreach ($filas as $f) { $f->dias = $f->dias === '' ? array() : explode(',', $f->dias); $porCliente[$f->cliente_id][] = $f; }
        foreach ($clientes as $c) $c->horarios = $porCliente[$c->id] ?? array();
        return $clientes;
    }
    // "Lun–Vie 07:00–16:00 · Sáb 07:00–15:00"
    public static function resumen_horarios($horarios)
    {
        $partes = array(); $orden = array_keys(self::DIAS);
        foreach ($horarios as $h) {
            $indices = array_values(array_intersect(array_keys($orden), array_keys(array_intersect($orden, (array) $h->dias))));
            $tramos = array(); $inicio = null;
            foreach ($indices as $i => $d) {
                if ($inicio === null) $inicio = $d;
                if (!isset($indices[$i + 1]) || $indices[$i + 1] !== $d + 1) {
                    $a = self::DIAS_CORTOS[$orden[$inicio]]; $b = self::DIAS_CORTOS[$orden[$d]];
                    $tramos[] = $d - $inicio >= 2 ? $a.'–'.$b : ($d > $inicio ? $a.', '.$b : $a);
                    $inicio = null;
                }
            }
            $partes[] = implode(', ', $tramos).' '.$h->entrada.'–'.$h->salida.($h->detalle !== '' ? ' ('.$h->detalle.')' : '');
        }
        return implode(' · ', $partes);
    }
    public function supervisores()
    {
        return $this->db->select('id_usu, nombre_usu, apellido_pat, estado_usu')->where('tipo_usu', 'SP')->order_by('nombre_usu')->order_by('apellido_pat')->get('Usuarios')->result();
    }
    public function es_supervisor($id_usu) { return $this->db->where(array('id_usu' => $id_usu, 'tipo_usu' => 'SP'))->count_all_results('Usuarios') > 0; }
    public function por_codigo($codigo) { return $this->db->get_where('almacen_clientes',array('codigo'=>$codigo))->row(); }
    // $horarios: filas validadas por el controlador (dias, entrada, salida, detalle); null = no modificar.
    public function guardar($id, $data, $horarios = null)
    {
        $this->db->trans_begin();
        if ($id) {
            $actual = $this->db->query('SELECT * FROM almacen_clientes WHERE id = ? FOR UPDATE', array($id))->row();
            if (!$actual) { $this->db->trans_rollback(); return 'El cliente no existe.'; }
        }
        if (!$data['activo'] && trim($data['documento_baja']) === '') { $this->db->trans_rollback(); return 'Indica la referencia del documento oficial de baja.'; }
        if ($data['codigo'] === '') $data['codigo'] = $id ? $actual->codigo : $this->siguiente_codigo();
        $this->db->where('codigo', $data['codigo']);
        if ($id) $this->db->where('id !=', $id);
        if ($this->db->count_all_results('almacen_clientes')) { $this->db->trans_rollback(); return 'Ya existe un cliente con ese código.'; }
        $ok = $id ? $this->db->where('id', $id)->update('almacen_clientes', $data) : $this->db->insert('almacen_clientes', $data);
        if (!$ok || !$this->db->trans_status()) { $this->db->trans_rollback(); return 'No se pudo guardar el cliente.'; }
        $this->ultimo_id = $id ? $id : (int) $this->db->insert_id();
        if ($horarios !== null) {
            $ok = $this->db->where('cliente_id', $this->ultimo_id)->delete('almacen_cliente_horarios');
            foreach ($horarios as $i => $h) {
                if (!$ok) break;
                $ok = $this->db->insert('almacen_cliente_horarios', array('cliente_id' => $this->ultimo_id, 'dias' => implode(',', $h['dias']), 'hora_ingreso' => $h['entrada'], 'hora_salida' => $h['salida'], 'detalle' => $h['detalle'], 'orden' => $i));
            }
            if (!$ok || !$this->db->trans_status()) { $this->db->trans_rollback(); return 'No se pudieron guardar los horarios.'; }
        }
        $this->db->trans_commit(); return true;
    }
    private function siguiente_codigo()
    {
        $fila = $this->db->query("SELECT MAX(CAST(SUBSTRING(codigo, 5) AS UNSIGNED)) AS n FROM almacen_clientes WHERE codigo REGEXP '^CLI-[0-9]+$' FOR UPDATE")->row();
        return sprintf('CLI-%04d', ((int) $fila->n) + 1);
    }
}
