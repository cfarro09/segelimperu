<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

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
		$this->load->model('General_model');
		$this->load->model('Admin_model');
		
	}
	public function index(){
		$data['menu_tables'] = $this->General_model->get_tables_active();

		$tables = $this->Admin_model->show_tables(); 
		$data_admin= $this->Admin_model->get_admin_table();
		foreach ($tables as $table) {
			foreach ($table as $key => $value) {
				if ($value != "adminpro_dynamic" && $value != "Usuarios" &&  $value != "Permisos" && $value != "Type_working") {
					$data['data_tables'][$value]['alias'] = $value;
					$data['data_tables'][$value]['status'] = "IN";
				}
			}
		}
		if ($data_admin) {
			foreach ($data_admin as $table) {
				$data['data_tables'][$table->nameTable]['alias'] = $table->aliasTable;
				$data['data_tables'][$table->nameTable]['status'] = $table->statusTable;
			}
		}
		$this->layout->view('admin/index', $data);
	}
	public function update_estado(){
		$name_table=html_purify($this->input->post('name_table'));
		$estado=html_purify($this->input->post('estado'));
		$alias=html_purify($this->input->post('alias_table'));

		if ($name_table && $estado && $alias) {
			$data['data'] = array(
				"aliasTable" => $alias,
				"statusTable" => $estado
			);
			$data['where'] = array(
				"nameTable" => $name_table
			);
			$response= $this->Admin_model->update_state_table($data);
			echo json_encode($response);
		}else{
			echo false;
		}
	}
	public function configurator(){
		$data['menu_tables']  = $this->General_model->get_tables_active();
		
		$data['result'] = $data['menu_tables'];
		$this->layout->view('admin/manage_form', $data);
	}
	public function send_comment(){
		$name_table=html_purify($this->input->post('name_table'));
		$name_column=html_purify($this->input->post('name_column'));
		$type_column=html_purify($this->input->post('type_column'));
		$null_column= html_purify($this->input->post('null_column')) == "YES"? "NULL":"NOT NULL";
		$comment_column=html_purify($this->input->post('comment_column'));
		if ($name_table && $name_column && $type_column && $null_column) {
			$arreglo = [
	          'name_table' => $name_table,
	          'name_column' => $name_column,
	          'type_column' => $type_column,
	          'null_column' => $null_column,
	          'comment_column' => $comment_column
	        ];
			$response = $this->Admin_model->send_comment($arreglo);
			echo json_encode($response);
		}else{
			echo false;
		}
	}
	public function get_referenced_table(){
		$name_table=html_purify($this->input->post('name_table'));
		$name_column=html_purify($this->input->post('name_column'));

		if ($name_table && $name_column) {
			$response = $this->Admin_model->get_referenced_table($name_table, $name_column);
			echo json_encode($response);
		}else{
			echo false;
		}
	}
	public function get_data_dependence(){
		$name_table=html_purify($this->input->post('name_table'));
		$response = $this->Admin_model->get_data_dependence($name_table);
		echo json_encode($response);
	}
}
