<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// CECON: consulta de solo lectura de las tareas (tickets) que los supervisores registran en la app móvil
// y de su descargo. Las tablas app_* pertenecen al API; aquí nunca se escribe en ellas.
class Cecon_model extends CI_Model
{
    // Estados del API (app_tickets.status): etiqueta en español y clase de la insignia.
    const ESTADOS = array(
        'PENDING'     => array('Pendiente', 'badge-warning'),
        'IN_PROGRESS' => array('En proceso', 'badge-info'),
        'DISCHARGED'  => array('Con descargo', 'badge-primary'),
        'COMPLETED'   => array('Completada', 'badge-success'),
        'REJECTED'    => array('Rechazada', 'badge-danger'),
    );
    const LIMITE = 2000;

    public function __construct() { parent::__construct(); $this->load->database(); }

    public static function vistas()
    {
        $vistas = array('todas' => 'Todas');
        foreach (self::ESTADOS as $codigo => $estado) $vistas[$codigo] = $estado[0];
        return $vistas;
    }

    public function disponible()
    {
        foreach (array('app_tickets', 'app_discharges', 'app_users', 'app_areas', 'app_request_categories', 'app_evidences') as $tabla) {
            if (!$this->db->table_exists($tabla)) return false;
        }
        return true;
    }

    private function base($columnas)
    {
        $this->db->select($columnas, false)
            ->from('app_tickets t')
            ->join('app_request_categories c', 'c.id = t.category_id', 'left')
            ->join('app_areas a', 'a.id = t.current_area_id', 'left')
            ->join('app_users s', 's.id = t.supervisor_id', 'left')
            ->join('almacen_clientes cl', 'cl.id = t.client_id', 'left')
            ->join('app_discharges d', 'd.ticket_id = t.id', 'left')
            ->join('app_users du', 'du.id = d.area_user_id', 'left')
            ->join('app_areas dua', 'dua.id = du.area_id', 'left');
    }

    public function listar($vista)
    {
        $this->base("t.id, t.ticket_code, t.status, t.supervisor_comment, t.created_at, t.closed_at, c.name AS categoria, a.spanish_name AS area, s.fullname AS supervisor, cl.nombre AS cliente, d.discharge_comment AS descargo, d.created_at AS descargo_en, du.fullname AS descargo_por");
        if (isset(self::ESTADOS[$vista])) $this->db->where('t.status', $vista);
        return $this->db->order_by('t.created_at', 'DESC')->order_by('t.id', 'DESC')->limit(self::LIMITE)->get()->result();
    }

    // Detalle de una tarea con sus fotos. Las coordenadas y las URL de las fotos solo se leen aquí, bajo demanda.
    public function detalle($id)
    {
        $this->base("t.id, t.ticket_code, t.status, t.supervisor_comment, t.latitude, t.longitude, t.created_at, t.closed_at, c.name AS categoria, a.spanish_name AS area, s.fullname AS supervisor, cl.nombre AS cliente, d.discharge_comment AS descargo, d.created_at AS descargo_en, du.fullname AS descargo_por, dua.spanish_name AS descargo_area");
        $t = $this->db->where('t.id', (int) $id)->get()->row();
        if (!$t) return null;
        $t->fotos_campo = array();
        $t->fotos_descargo = array();
        $fotos = $this->db->select('evidence_type, evidence_url')->where('ticket_id', (int) $id)->order_by('created_at')->order_by('id')->get('app_evidences')->result();
        foreach ($fotos as $f) {
            $url = self::url_publica($f->evidence_url);
            if ($url === null) continue;
            if ($f->evidence_type === 'AREA_DISCHARGE') $t->fotos_descargo[] = $url; else $t->fotos_campo[] = $url;
        }
        return $t;
    }

    public function conteos()
    {
        $c = array('todas' => 0);
        foreach (self::ESTADOS as $codigo => $estado) $c[$codigo] = 0;
        foreach ($this->db->select('status, COUNT(*) AS n')->group_by('status')->get('app_tickets')->result() as $f) {
            if (isset($c[$f->status])) $c[$f->status] = (int) $f->n;
            $c['todas'] += (int) $f->n;
        }
        return $c;
    }

    // Solo se muestran fotos servidas por https; cualquier otro valor (vacío, javascript:, data:) se descarta.
    public static function url_publica($url)
    {
        $url = trim((string) $url);
        return preg_match('#^https://[^\s"\'<>]+$#i', $url) && filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    // El API guarda las fechas en UTC (ISO-8601 con "Z"); la aplicación las muestra en hora de Lima.
    public static function fecha($utc)
    {
        if ($utc === null || $utc === '') return '';
        try { $f = new DateTime($utc, new DateTimeZone('UTC')); } catch (Exception $e) { return ''; }
        return $f->setTimezone(new DateTimeZone('America/Lima'))->format('d/m/Y H:i');
    }
}
