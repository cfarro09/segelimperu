<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Contratos extends CI_Controller {

 	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('id_usu')) { //isLogin
			redirect("login", "refresh");
		}
		if ($this->session->userdata('tipo_usu') != "SA" ) {
			redirect("/", "refresh");
		}
		$this->load->library('html');
		$this->load->library('layout', 'layout');
		$this->load->model('Contratos_model');
		$this->load->model('General_model');
		
	}
	public function index(){
		$data['menu_tables'] = $this->General_model->get_tables_active();

		$data['personal']= $this->Contratos_model->get_personal_last_contr();
		$this->layout->view('contratos/index', $data);
	}
	public function renovar(){
		$data = html_purify($this->input->post());
		$response = $this->Contratos_model->register($data);
		echo json_encode($response);
	}
	public function get_history_empl(){
		$id_usu = html_purify($this->input->post("id_usu"));
		if ($id_usu) {
			$response = $this->Contratos_model->get_history_empl($id_usu);
			echo json_encode($response);
		}else{
			echo false;
		}
	}
	public function desafiliar_personal(){
		$id_usu = html_purify($this->input->post("id_usu"));
		if ($id_usu) {
			$response = $this->Contratos_model->desafiliar_personal($id_usu);
			echo json_encode($response);
		}else{
			echo false;
		}
	}
}
