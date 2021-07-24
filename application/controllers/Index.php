<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('id_usu')) { //isLogin
			redirect("login", "refresh");
		}
		$this->load->library('html');
		$this->load->library('layout');
	}

	public function index(){
		if ($this->session->userdata('tipo_usu') == "AD" || $this->session->userdata('tipo_usu') == "SA" ) {
			$data['menu_tables']  = json_decode($this->session->userdata('menu_tables'));
		}else{
			$this->load->model('Permisos_model');
			$data['menu_tables'] = $this->Permisos_model->get_permisos_type_user($this->session->userdata('tipo_usu'));
		}
		
		$this->layout->view('home/index', $data);
	}

}
