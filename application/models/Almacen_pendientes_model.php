<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** Pendientes de almacén: pedidos de uniformes, materiales, implementos, recojos y otros, con su seguimiento. */
class Almacen_pendientes_model extends CI_Model
{
    const TIPOS = array('uniformes' => 'Uniformes', 'materiales' => 'Materiales', 'implementos' => 'Implementos y equipos', 'recojo' => 'Recojo en unidad', 'otros' => 'Otros');
    const ESTADOS = array('pendiente' => 'Pendiente', 'en_proceso' => 'En proceso', 'atendido' => 'Atendido');
    const VISTAS = array('abiertos' => 'Abiertos', 'atendidos' => 'Atendidos', 'todos' => 'Todos');

    public function __construct() { parent::__construct(); $this->load->database(); }

    private function base($creado_por = null)
    {
        $this->db->select("p.*, TRIM(CONCAT(COALESCE(u.nombre_usu,''), ' ', COALESCE(u.apellido_pat,''))) AS atendido_por_nombre", false)
            ->from('almacen_pendientes p')->join('Usuarios u', 'u.id_usu = p.atendido_por', 'left');
        // Un supervisor solo ve los pendientes que registró.
        if ($creado_por !== null) $this->db->where('p.creado_por', (int) $creado_por);
    }
    public function listar($vista, $creado_por = null)
    {
        $this->base($creado_por);
        if ($vista === 'abiertos') $this->db->where_in('p.estado', array('pendiente', 'en_proceso'));
        elseif ($vista === 'atendidos') $this->db->where('p.estado', 'atendido');
        // Abiertos: lo más antiguo primero (lo que más espera); el resto, lo más reciente primero.
        $this->db->order_by('p.fecha', $vista === 'abiertos' ? 'ASC' : 'DESC')->order_by('p.id', $vista === 'abiertos' ? 'ASC' : 'DESC');
        return $this->db->get()->result();
    }
    public function obtener($id) { $this->base(); return $this->db->where('p.id', (int) $id)->get()->row(); }
    public function conteos($creado_por = null)
    {
        if ($creado_por !== null) $this->db->where('creado_por', (int) $creado_por);
        $filas = $this->db->select('estado, COUNT(*) AS n')->group_by('estado')->get('almacen_pendientes')->result();
        $c = array('pendiente' => 0, 'en_proceso' => 0, 'atendido' => 0);
        foreach ($filas as $f) $c[$f->estado] = (int) $f->n;
        return array('abiertos' => $c['pendiente'] + $c['en_proceso'], 'atendidos' => $c['atendido'], 'todos' => array_sum($c), 'urgentes' => $this->urgentes_abiertos($creado_por));
    }
    private function urgentes_abiertos($creado_por)
    {
        if ($creado_por !== null) $this->db->where('creado_por', (int) $creado_por);
        return $this->db->where('prioridad', 'urgente')->where_in('estado', array('pendiente', 'en_proceso'))->count_all_results('almacen_pendientes');
    }
    // Sugerencias para "Enviado por": nombres ya usados.
    public function solicitantes()
    {
        return array_column($this->db->distinct()->select('solicitante')->where('solicitante <>', '')->order_by('solicitante')->get('almacen_pendientes')->result_array(), 'solicitante');
    }
    // Si la sede escrita coincide con un cliente, se enlaza; si no, queda como sede libre (p. ej. "Almacén Borgoño").
    public function cliente_por_nombre($sede)
    {
        $c = $this->db->select('id, nombre')->where('nombre', $sede)->get('almacen_clientes')->row();
        return $c ? (int) $c->id : null;
    }
    public function guardar($id, $data)
    {
        $ok = $id ? $this->db->where('id', (int) $id)->update('almacen_pendientes', $data) : $this->db->insert('almacen_pendientes', $data);
        return $ok ? ($id ? (int) $id : (int) $this->db->insert_id()) : false;
    }
    public function atender($id, $usuario, $observacion)
    {
        $p = $this->obtener($id);
        if (!$p || $p->estado === 'atendido') return false;
        $data = array('estado' => 'atendido', 'atendido_por' => (int) $usuario, 'atendido_en' => date('Y-m-d H:i:s'));
        if ($observacion !== '') $data['observaciones'] = mb_substr(trim($p->observaciones.($p->observaciones !== '' ? ' · ' : '').$observacion), 0, 500);
        return $this->db->where('id', (int) $id)->where('estado <>', 'atendido')->update('almacen_pendientes', $data) && $this->db->affected_rows() === 1;
    }
    public static function dias_abierto($p)
    {
        if ($p->estado === 'atendido') return null;
        return (int) floor((strtotime(date('Y-m-d')) - strtotime($p->fecha)) / 86400);
    }
}
