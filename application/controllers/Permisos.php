<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Permisos extends CI_Controller {

 	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('id_usu')) { //isLogin
			redirect("login", "refresh");
		}
		if ($this->session->userdata('tipo_usu') != "SA" && $this->session->userdata('tipo_usu') != "AD") {
			redirect("/", "refresh");
		}
		$this->load->library('html');
		$this->load->library('layout', 'layout');
		$this->load->model('Permisos_model');
		$this->load->model('General_model');
	}
	public function index(){
		$data['menu_tables'] = $this->General_model->get_tables_active();
		
		$data['categories']= $this->Permisos_model->get_categories();
		$this->layout->view('permisos/index', $data);
	}
	public function get_permisos_user(){
		$category = html_purify($this->input->post('category'));
		if ($category) {
			$tables = $this->General_model->get_tables_active(); 
			$permisos = $this->Permisos_model->get_permisos_user(array('id_work' => $category));
            if ($tables) {
	            foreach ($tables as $table){
					if ($table->nameTable != "adminpro_dynamic" && $table->nameTable != "Usuarios" && $table->nameTable != "Type_working" && $table->nameTable != "Permisos") {
						$data_permisos[$table->nameTable]['lectura'] = 0;
						$data_permisos[$table->nameTable]['escritura'] = 0;
					}
				}
            }
			if ($permisos) {
				foreach ($permisos as $permiso) {
					$data_permisos[$permiso->nameTable]['lectura'] = $permiso->lectura;
					$data_permisos[$permiso->nameTable]['escritura'] = $permiso->escritura;
				}
			}
			echo json_encode($data_permisos);
		}
	}
	public function manage_state_permiso(){
		$id_work = html_purify($this->input->post('id_work'));
		$nameTable = html_purify($this->input->post('nameTable'));
		$type = html_purify($this->input->post('type'));
		$view = html_purify($this->input->post('view'));
		if ($id_work && $nameTable && $type) {
			$data['data'] = array(
				$type => (int) $view
			);
			$data['where'] = array(
				"id_work" => $id_work,
				"nameTable" => $nameTable
			);
			$permisos = $this->Permisos_model->update_permiso($data);
			echo json_encode($permisos);
		}
	}
}
