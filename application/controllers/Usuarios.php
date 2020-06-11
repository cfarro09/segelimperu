<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('id_usu')) { //isLogin
			redirect("login", "refresh");
		}
		$this->load->library('layout', 'layout');
		$this->load->library('html');
		$this->load->model('User_model');
		$this->load->model('Permisos_model');
		$this->load->model('General_model');
	}
	public function index(){
		$data['menu_tables'] = $this->menu_tables = $this->General_model->get_tables_active();

		$data['users_suscribers'] = $this->User_model->get_all_users();
		$this->layout->view('usuarios/usuarios', $data);
	}
	public function usuarios_reg(){
		$data['menu_tables'] = $this->menu_tables = $this->General_model->get_tables_active();
		
		$data['categories'] = $this->menu_tables = $this->Permisos_model->get_categories();
		$data['users_suscribers'] = $this->User_model->get_users_suscribers();
		$this->layout->view('usuarios/usuarios_reg', $data);
	}
	public function action_suscribers(){
		$action = html_purify($this->input->post('action'));
		$id_usu = html_purify($this->input->post('id_usu'));
		$tipo_usu = html_purify($this->input->post('tipo_usu'));
		if ($action && $id_usu && $tipo_usu) {
			$data = array(
				"estado_usu" => $action,
				"tipo_usu" => $tipo_usu
			);
			$response = $this->User_model->action_user($id_usu, $data);
			$datacount = $this->User_model->getcount_users_suscribers();
			if ($datacount) {
				$this->session->set_userdata('count_suscribers', $datacount[0]->count);
			}
			echo json_encode($response);
		}
	}
	public function edit_usuarios(){
		$id_usu = html_purify($this->input->post('id_usu'));
		if ($id_usu) {
			$data['menu_tables'] = $this->menu_tables = $this->General_model->get_tables_active();

			$data['tipos_documento'] = $this->General_model->get_tipos_documento();
			$data['categories'] = $this->Permisos_model->get_categories();

			$data['user'] = $this->User_model->get_user($id_usu)[0];
			$this->layout->view('usuarios/edit_usuario', $data);
		}
	}
	public function send_update_user(){
		$id_usu = html_purify($this->input->post('id_usu'));
		$data = html_purify($this->input->post());
		unset($data['id_usu']);
		if (isset($data['password'])) {
			$this->load->library('encryption');
			$data['password'] = $this->encryption->encrypt($data['password']);
		}
		$response = $this->User_model->action_user($id_usu, $data);
		$datacount = $this->User_model->getcount_users_suscribers();
		if ($datacount) {
			$this->session->set_userdata('count_suscribers', $datacount[0]->count);
		}
		echo json_encode($response);
	}
}
