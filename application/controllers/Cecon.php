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
}
