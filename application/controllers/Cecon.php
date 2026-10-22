<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// CECON: tareas registradas por los supervisores y su descargo (solo lectura).
class Cecon extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id_usu')) { redirect('login'); exit; }
        if (!in_array($this->session->userdata('tipo_usu'), array('SA', 'AD'), true)) {
            show_error('Tu rol no tiene permiso para este módulo.', 403); exit;
        }
        $this->load->library('layout');
        $this->load->model('Cecon_model');
        $this->load->model('General_model');
        $this->db->db_debug = false;
    }

    public function index()
    {
        if (!$this->Cecon_model->disponible()) { show_error('CECON aún no está disponible: faltan las tablas de tareas.', 503); exit; }
        $vista = $this->input->get('vista');
        $vista = is_string($vista) && isset(Cecon_model::vistas()[$vista]) ? $vista : 'todas';
        $this->layout->view('cecon/index', array(
            'menu_tables' => $this->General_model->get_tables_active(),
            'tareas'      => $this->Cecon_model->listar($vista),
            'conteos'     => $this->Cecon_model->conteos(),
            'vista'       => $vista,
        ));
    }

    // Detalle de una tarea en JSON, para la ventana de detalle. Las coordenadas y las fotos no viajan en el listado.
    public function detalle($id = '')
    {
        $json = function ($datos, $codigo = 200) {
            $this->output->set_status_header($codigo)->set_content_type('application/json', 'utf-8')
                ->set_header('Cache-Control: no-store')->set_output(json_encode($datos, JSON_UNESCAPED_UNICODE));
        };
        if (!ctype_digit((string) $id)) { $json(array('error' => 'Tarea inválida.'), 400); return; }
        $t = $this->Cecon_model->detalle((int) $id);
        if (!$t) { $json(array('error' => 'La tarea no existe.'), 404); return; }
        $estado = Cecon_model::ESTADOS[$t->status] ?? array($t->status, 'badge-secondary');
        $lat = is_numeric($t->latitude) ? (float) $t->latitude : null;
        $lng = is_numeric($t->longitude) ? (float) $t->longitude : null;
        $json(array(
            'codigo'     => $t->ticket_code,
            'estado'     => array('etiqueta' => $estado[0], 'clase' => $estado[1]),
            'categoria'  => (string) $t->categoria,
            'cliente'    => (string) $t->cliente,
            'area'       => (string) $t->area,
            'supervisor' => (string) $t->supervisor,
            'registro'   => Cecon_model::fecha($t->created_at),
            'comentario' => trim((string) $t->supervisor_comment),
            'ubicacion'  => $lat !== null && $lng !== null ? array('texto' => number_format($lat, 5, '.', '') . ', ' . number_format($lng, 5, '.', ''),
                'mapa' => 'https://www.google.com/maps?q=' . number_format($lat, 6, '.', '') . ',' . number_format($lng, 6, '.', '')) : null,
            'fotos_campo' => $t->fotos_campo,
            'cierre'     => $t->closed_at ? Cecon_model::fecha($t->closed_at) : '',
            'descargo'   => $t->descargo === null ? null : array(
                'comentario' => trim((string) $t->descargo), 'por' => (string) $t->descargo_por, 'area' => (string) $t->descargo_area,
                'hora' => Cecon_model::fecha($t->descargo_en), 'fotos' => $t->fotos_descargo),
        ));
    }
}
