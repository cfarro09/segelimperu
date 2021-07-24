<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Recibo extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('id_usu')) { //isLogin
			redirect("login", "refresh");
		}
		$this->load->library('html');
		$this->load->library('layout', 'layout');
		$this->load->model('General_model');
	}

	public function listar()
	{
		$data['menu_tables'] =  $this->General_model->get_tables_active();

		$data['list_recibos'] = $this->General_model->get_data_dynamic("receipt", "*");

		$this->layout->view('recibo/list', $data);
	}

	public function insertar()
	{
		$data = html_purify($this->input->post());
		if ($data["receipt_id"] != "0") {
			// die(json_encode($data));
		
			$where = array("receipt_id" => $data["receipt_id"]);
			unset($data['receipt_id']);
			$response = $this->General_model->edit_dynamic('receipt', $where, $data);
		} else {
			unset($data['receipt_id']);
			$response = $this->General_model->insert_dynamic("receipt", $data);
		}

		echo json_encode($response);

	}


    public function register_boleta(){
		$idpers = html_purify($this->input->post('idpers'));
		$fecha = html_purify($this->input->post('fecha'));
		$apellido_pat = html_purify($this->input->post('apellido_pat'));
		$apellido_mat = html_purify($this->input->post('apellido_mat'));
		$nombres = html_purify($this->input->post('nombres'));
		$dni = html_purify($this->input->post('dni'));
		$nro_boleta = html_purify($this->input->post('nro_boleta'));
		$observacion = html_purify($this->input->post('observacion'));
		$detalle_boleta = html_purify($this->input->post('data_extra'));

		if (($apellido_pat && $apellido_mat && $nombres && $dni && $fecha && $nro_boleta)   || ($apellido_pat == "ANULADO" || $dni == 0)) {
			$send_boleta = array(
				'fecha' => $fecha, 
				'nro_boleta' => (int) $nro_boleta,
				'nombres' => $nombres,
				'apellido_mat' => $apellido_mat,
				'apellido_pat' => $apellido_pat,
				'dni' => $dni == "0" ? "ANULADO" : $dni,
				'observacion' => $observacion
			);
			$response =  $this->BoletasUnif_model->register_boleta($send_boleta);
			if ($detalle_boleta) {
				if (isset($response['success']) && $response['success']) {
					$detalle_boleta = json_decode($detalle_boleta);
					$response_1 =  $this->BoletasUnif_model->register_detalle_boleta($detalle_boleta);
					echo json_encode($response_1);
				}else{
					echo json_encode("false");
				}
			}else{
				echo json_encode($response);
			}
		}else{
			echo json_encode("false");
		}
	}
}
