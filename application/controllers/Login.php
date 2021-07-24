<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	 public function __construct() {
		parent::__construct();
		$this->load->library('html');
		$this->load->model('User_model');
		$this->load->model('General_model');
		$this->load->library('form_validation');
	 }
	public function index(){
		$this->logueado();
		$data['tipos_documento'] = $this->General_model->get_tipos_documento();
		$this->load->view('login/index', $data);
	}
	public function access_login(){
		$this->form_validation->set_rules('email_login','email','trim|required');
		$this->form_validation->set_rules('password','password','trim|required');
		if($this->form_validation->run() == false){
            redirect("login", "refresh");
        }else{
			$email_usu=html_purify($this->input->post('email_login'));
			$password=html_purify($this->input->post('password'));
    		$data = [
	            "email_usu" => $email_usu,
	            "password" => $password
	        ];
			$response = $this->User_model->login($data);
			if (isset($response['success']) && $response['success']) {
				$this->session->set_userdata('id_usu', $response['id_usu']);
				$this->session->set_userdata('nombre_usu', $response['nombre_usu']);
				$this->session->set_userdata('email_usu', $email_usu);
				$this->session->set_userdata('tipo_usu', $response['tipo_usu']);

				if ($response['tipo_usu'] == "AD" || $response['tipo_usu'] == "SA" ) {
					$response = $this->User_model->getcount_users_suscribers();
					if ($response) {
						$this->session->set_userdata('count_suscribers', $response[0]->count);
					}
					$this->session->set_userdata('menu_tables', json_encode($this->General_model->get_tables_active()));
				}else{
					$this->load->model('Permisos_model');
					// $this->session->set_userdata('menu_tables', json_encode($this->Permisos_model->get_permisos_type_user($response['tipo_usu'])));
				}
				redirect("/","refresh");
			}else if($response['msg']){
            	$this->session->set_flashdata('email_error', $email_usu);
            	$this->session->set_flashdata('error', $response['msg']);
            	redirect("login");
			}
        }
	}
	public function validate_email(){
		$this->form_validation->set_rules('email_usu','email','trim|required');
		if($this->form_validation->run() == false){
            redirect("login", "refresh");
        }else{
			$email_usu=html_purify($this->input->post('email_usu'));
			if($email_usu){
	    		$data = [
		            "email_usu" => $email_usu
		        ];
				$response = $this->User_model->validate_email($data);
		    	echo json_encode($response);
			}
        }
	}
	public function logout(){
		$this->session->sess_destroy();
		redirect("login");
	}
	public function logueado(){
		if ($this->session->userdata('id_usu')) {
			redirect('Index','refresh');
		}
	}
}
