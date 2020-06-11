<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller {

	 public function __construct() {
		parent::__construct();
      	$this->load->model('User_model');
      	$this->load->library('html');
	 }

	public function index(){
		$this->form_validation->set_rules('names_user','names_user','trim|required');
		$this->form_validation->set_rules('apellido_pa','apellido_pa','trim|required');
		$this->form_validation->set_rules('apellido_ma','apellido_ma','trim|required');
		$this->form_validation->set_rules('tipo_doc','tipo_doc','trim|required');
		$this->form_validation->set_rules('nro_doc','nro_doc','trim|required');
		$this->form_validation->set_rules('email_reg','email_reg','trim|required');
		$this->form_validation->set_rules('new_password','new_password','trim|required');
		if(!$this->form_validation->run()){
            redirect("login", "refresh");
        }else{
	    	$nombre_usu = html_purify($this->input->post('names_user'));
	    	$apellido_pa = html_purify($this->input->post('apellido_pa'));
	    	$apellido_ma = html_purify($this->input->post('apellido_ma'));
	    	$tipo_doc = html_purify($this->input->post('tipo_doc'));
	    	$nro_doc = html_purify($this->input->post('nro_doc'));
	    	$email_usu = html_purify($this->input->post('email_reg'));
	    	$password = html_purify($this->input->post('new_password'));
			$this->load->library('encryption');
    		$data = [
	            "nombre_usu" => $nombre_usu,
	            "apellido_pat" => $apellido_pa,
	            "apellido_mat" => $apellido_ma,
	            "id_tipo_doc" => $tipo_doc,
	            "nro_documento" => $nro_doc,
	            "email_usu" => $email_usu,
	            "password" => $this->encryption->encrypt($password),
	            "intentos_usu" => 0,
	            "estado_usu" => "PG",//AC activo, IN inactivo, PG PROGRESO
	            "tipo_usu" => "US" //SA super admin, AC admin comun US
	        ];
	    	$response = $this->User_model->register($data);
	    	echo json_encode($response);
        }
    }
}


