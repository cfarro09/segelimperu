<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/** Cargos de guías: foto/PDF de la guía firmada y sellada por el cliente, y su verificación. */
class Almacen_cargos_model extends CI_Model
{
    const VISTAS = array('pendientes' => 'Pendientes de subir', 'por_verificar' => 'Por verificar', 'verificados' => 'Verificados', 'todos' => 'Todos');
    const TIPOS = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'application/pdf' => 'pdf');

    public function __construct() { parent::__construct(); $this->load->database(); }
    // Fuera de la carpeta pública: los archivos solo se entregan por el controlador, con sesión y permisos.
    public static function carpeta() { return dirname(FCPATH, 2).'/storage/cargos/'; }

    private function base($supervisor_id = null)
    {
        $this->db->select("k.*, c.codigo AS cliente_codigo, TRIM(CONCAT(COALESCE(sp.nombre_usu,''),' ',COALESCE(sp.apellido_pat,''))) AS supervisor,
            TRIM(CONCAT(COALESCE(us.nombre_usu,''),' ',COALESCE(us.apellido_pat,''))) AS subido_por_nombre,
            TRIM(CONCAT(COALESCE(uv.nombre_usu,''),' ',COALESCE(uv.apellido_pat,''))) AS verificado_por_nombre", false)
            ->from('almacen_cargos k')->join('almacen_clientes c', 'c.id = k.cliente_id', 'left')
            ->join('Usuarios sp', 'sp.id_usu = c.supervisor_id', 'left')->join('Usuarios us', 'us.id_usu = k.subido_por', 'left')->join('Usuarios uv', 'uv.id_usu = k.verificado_por', 'left');
        // Un supervisor ve los cargos de sus clientes y las guías manuales que registró.
        if ($supervisor_id !== null) $this->db->group_start()->where('c.supervisor_id', (int) $supervisor_id)->or_where('k.creado_por', (int) $supervisor_id)->group_end();
    }
    private function filtro_vista($vista)
    {
        if ($vista === 'pendientes') $this->db->where('k.archivo IS NULL', null, false);
        elseif ($vista === 'por_verificar') $this->db->where('k.archivo IS NOT NULL', null, false)->where('k.verificado_en IS NULL', null, false);
        elseif ($vista === 'verificados') $this->db->where('k.verificado_en IS NOT NULL', null, false);
    }
    public function listar($vista, $periodo, $supervisor_id = null)
    {
        $this->base($supervisor_id); $this->filtro_vista($vista);
        if ($periodo !== '') $this->db->where("DATE_FORMAT(k.fecha_guia,'%Y-%m') =", $periodo);
        return $this->db->order_by('k.fecha_guia', $vista === 'pendientes' ? 'ASC' : 'DESC')->order_by('k.id', 'DESC')->get()->result();
    }
    public function conteos($periodo, $supervisor_id = null)
    {
        $c = array();
        foreach (array_keys(self::VISTAS) as $vista) {
            $this->base($supervisor_id); $this->filtro_vista($vista);
            if ($periodo !== '') $this->db->where("DATE_FORMAT(k.fecha_guia,'%Y-%m') =", $periodo);
            $c[$vista] = $this->db->count_all_results();
        }
        return $c;
    }
    public function obtener($id) { $this->base(); return $this->db->where('k.id', (int) $id)->get()->row(); }
    public function visible($cargo, $supervisor_id)
    {
        if (!$cargo) return false;
        if ($supervisor_id === null) return true;
        if ((int) $cargo->creado_por === (int) $supervisor_id) return true;
        $c = $cargo->cliente_id ? $this->db->select('supervisor_id')->get_where('almacen_clientes', array('id' => $cargo->cliente_id))->row() : null;
        return $c && (int) $c->supervisor_id === (int) $supervisor_id;
    }
    public function guia_existe($numero) { return $this->db->where('numero_guia', $numero)->count_all_results('almacen_cargos') > 0; }
    public function cliente_por_nombre($sede)
    {
        $c = $this->db->select('id')->where('nombre', $sede)->get('almacen_clientes')->row();
        return $c ? (int) $c->id : null;
    }
    public function registrar_manual($data) { return $this->db->insert('almacen_cargos', $data) ? (int) $this->db->insert_id() : false; }
    // Guarda el archivo subido; devuelve el nombre del archivo anterior para borrarlo del disco.
    public function adjuntar($id, $archivo, $tipo, $usuario)
    {
        $this->db->trans_begin();
        $k = $this->db->query('SELECT * FROM almacen_cargos WHERE id = ? FOR UPDATE', array((int) $id))->row();
        if (!$k || $k->verificado_en) { $this->db->trans_rollback(); return array('error' => $k ? 'El cargo ya fue verificado; no se puede reemplazar.' : 'El cargo no existe.'); }
        $ok = $this->db->where('id', (int) $id)->update('almacen_cargos', array('archivo' => $archivo, 'archivo_tipo' => $tipo, 'subido_por' => (int) $usuario, 'subido_en' => date('Y-m-d H:i:s')));
        if (!$ok || !$this->db->trans_status()) { $this->db->trans_rollback(); return array('error' => 'No se pudo guardar el cargo.'); }
        $this->db->trans_commit(); return array('anterior' => $k->archivo);
    }
    public function verificar($id, $usuario, $verificar)
    {
        $this->db->where('id', (int) $id)->where('archivo IS NOT NULL', null, false);
        $this->db->where($verificar ? 'verificado_en IS NULL' : 'verificado_en IS NOT NULL', null, false);
        $ok = $this->db->update('almacen_cargos', $verificar ? array('verificado_por' => (int) $usuario, 'verificado_en' => date('Y-m-d H:i:s')) : array('verificado_por' => null, 'verificado_en' => null));
        return $ok && $this->db->affected_rows() === 1;
    }
    public static function dias_sin_cargo($k)
    {
        if ($k->archivo) return null;
        return (int) floor((strtotime(date('Y-m-d')) - strtotime($k->fecha_guia)) / 86400);
    }
}
