<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form extends CI_Controller {

 	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('id_usu')) { //isLogin
			redirect("login", "refresh");
		}
		$this->load->library('html');
		$this->load->library('layout', 'layout');
		$this->load->model('General_model'); //this line always should be here in the constructor
	}
	public function create(){
		$data['menu_tables'] = $this->get_menu_dynamic();

		$name_table=html_purify($this->input->post('name_table'));
		$alias_table=html_purify($this->input->post('alias_table'));

		if ($name_table && $alias_table) {
			// $data['nombre_usu'] = $this->session->userdata('nombre_usu');
			$data['name_table'] = $name_table;
			$data['alias_table'] = $alias_table;

			$this->layout->view('form/insert', $data);
		}else{
			redirect('/');
		}
		
	}
	public function get_html_create(){
		$name_table=html_purify($this->input->post('name_table'));
		$create_table = $this->General_model->get_columns_name($name_table);
		$data_comment = array();
		$data_comment['res'] = array();
		$data_comment['name_table'] = $name_table;
		$i = 0;
		foreach ($create_table as $input):
			if ($input->Key != "PRI" && !$input->Default) {
				if($input->Comment){
					if (count((array)json_decode($input->Comment)) > 2) {
						$data_comment['res'][$i] = json_decode($input->Comment);
						$data_comment['res'][$i]->name_input = $input->Field;
					}
				} 
			}
			
		$i++;
		endforeach;
		echo json_encode($data_comment);
	}
	public function get_html_edit(){
		$name_table = html_purify($this->input->post('name_table'));
		$valueid = html_purify($this->input->post('valueid'));
		$nameid = html_purify($this->input->post('nameid'));
		if ($name_table && $valueid && $nameid) {
			$where = array($nameid => $valueid);
			$response1 = $this->General_model->get_data_id_dynamic($name_table, $where);

			$create_table = $this->General_model->get_columns_name($name_table);
			$data_comment = array();
			$i = 0;
			foreach ($create_table as $input):
				if ($input->Key != "PRI" && !$input->Default) {
					if($input->Comment){
						if (count((array)json_decode($input->Comment)) > 2) {
							$data_comment[$i] = json_decode($input->Comment);
							$data_comment[$i]->name_input = $input->Field;
							if ($response1[0]->{$input->Field}) {
								$data_comment[$i]->value_input = $response1[0]->{$input->Field};
							}else{
								$data_comment[$i]->value_input = "";
							}
						}
					} 
				}
				
			$i++;
			endforeach;
			echo json_encode($data_comment);
		}else{
			echo false;
		}
	}
	public function insert_dynamic(){
		$data = $this->input->post();
		$name_table = "";
		if ($data['name_table']) {
			$name_table = $data['name_table'];
			unset($data['name_table']);
		}
		$response = $this->General_model->insert_dynamic($name_table, $data);
		echo json_encode($response);
	}
	public function get_columns_name(){
		$name_table=html_purify($this->input->post('name_table'));
		$response = $this->General_model->get_columns_name($name_table);
		echo json_encode($response);
	}
	public function get_columns_only_name(){
		$name_table=html_purify($this->input->post('name_table'));
		$response = $this->General_model->get_columns_only_name($name_table);
		echo json_encode($response);
	}
	
	public function get_data_table(){
		$name_table=html_purify($this->input->post('name_table'));
		$name_column=html_purify($this->input->post('name_column'));
		$id_column=html_purify($this->input->post('id_column'));
		$select = $id_column . "," . $name_column;
		$response = $this->General_model->get_columns_only_name($name_table);
		foreach ($response as $column) {
			if ($column->Key == "MUL") {
				$select .= ",".$column->Field;
			}
		}
		$response = $this->General_model->get_data_dynamic($name_table, $select);
		echo json_encode($response);
	}
	public function listar(){
		$data['menu_tables'] = $this->get_menu_dynamic();

		$data['escritura'] = true;
		$name_table=html_purify($this->input->post('name_table'));
		$alias_table=html_purify($this->input->post('alias_table'));

		if ($name_table && $alias_table) {
			if ($this->session->userdata('tipo_usu') != "AD" && $this->session->userdata('tipo_usu') != "SA" ) {
				foreach ($data['menu_tables'] as $table) {
					if ($table->nameTable == $name_table) {
						if ($table->escritura == 0) {
							$data['escritura'] = false;
						}
						break;
					}
				}
			}
			$from = 0;
			$joinleft = false;
			$joinfk = 1;
			$data['name_table'] = $name_table;
			$data['alias_table'] = $alias_table;
			$data['data'] = "";
			$data['tittles'] = array();
			$select = "";
			$response = $this->General_model->get_columns_name($name_table);
			if ($response) {
				foreach ($response as $column) {
					if ($column->Comment) {
						$comment = json_decode($column->Comment);
						if (isset($comment->list) && $comment->list) {
							if ($column->Key == "MUL") {
								$data['tittles'][$comment->type->name] = $comment->namelabel;
								$joinleft[$joinfk]['tablefk'] = $comment->type->table . " c".$joinfk;
								$joinleft[$joinfk]['comp'] = "c".$from.'.'.$column->Field.'='."c".$joinfk.'.'.$column->Field;
								$select .= "c".$joinfk.".".$comment->type->name . ","; 
								$joinfk++;
							}else{
								if (isset($comment->namelabel)) {
									$data['tittles'][$column->Field] = $comment->namelabel;
								}else{
									$data['tittles'][$column->Field] = $column->Field;
								}
								$select .= "c".$from.".".$column->Field . ","; 
							}
						}
					}else if($column->Key == "PRI"){
						$data['tittles']['id'] = $column->Field;
						$select .=  "c".$from.".".$column->Field . ",";
					}
				}
				$v = $data['tittles']['id'];
				unset($data['tittles']['id']);
				$data['tittles']['id'] = $v;
			}
			if ($select != "") {			
				$response = $this->General_model->get_data_dynamic($name_table. " c".$from, $select, $joinleft);
				$data['data'] = $response;
			}
			$this->layout->view('form/list', $data);
		}else{
			redirect("/");
		}
	}
	public function edit_form(){
		$data['menu_tables'] = $this->get_menu_dynamic();
		
		$data['name_table']=html_purify($this->input->post('nametable'));
		$data['alias_table']=html_purify($this->input->post('aliastable'));
		$data['valueid']=html_purify($this->input->post('valueid'));
		$data['nameid']=html_purify($this->input->post('nameid'));
		if ($data['name_table'] && $data['valueid'] && $data['nameid']) {
			$this->layout->view('form/edit', $data);
		}else{
			redirect('/');
		}
	}
	public function edit_dynamic(){
		$data = $this->input->post();
		$name_table = $data['name_table'];
		$valueid = $data['valueid'];
		$nameid = $data['nameid'];
		unset($data['name_table']);
		unset($data['valueid']);
		unset($data['nameid']);
		
		$where = array($nameid => $valueid);

		$response = $this->General_model->edit_dynamic($name_table, $where,$data);
		echo json_encode($response);
	}
	public function delete_data(){
		$name_table = html_purify($this->input->post('nametable'));
		$nameid = html_purify($this->input->post('nameid'));
		$valueid = html_purify($this->input->post('valueid'));
		if ($name_table && $nameid && $valueid && $name_table != "" && $nameid != ""  && $valueid != "" ) {
			$where = array($nameid => $valueid);
			$response = $this->General_model->delete_dynamic($name_table, $where);
			echo json_encode($response);
		}else{
			echo false;
		}
	}
	private function get_menu_dynamic(){
		$tipo_usu = $this->session->userdata('tipo_usu');
		if ($tipo_usu == "AD" || $tipo_usu == "SA" ) {
			$menu_tables  = json_decode($this->session->userdata('menu_tables'));
		}else{
			$this->load->model('Permisos_model');
			$menu_tables = $this->Permisos_model->get_permisos_type_user($tipo_usu);
		}
		return $menu_tables;
	}
}
