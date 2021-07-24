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

		//$data['list_recibos'] = $this->General_model->get_data_dynamic("receipt", "*");

		$this->layout->view('recibo/list', $data);
	}

	public function getData()
	{
		$data = $this->General_model->get_data_dynamic("receipt", "*");
		die(json_encode(array("data" => $data)));
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

	public function reporte($id)
	{
		$this->load->library('Reporte_recibo');
		$pdf = new Reporte_recibo();
		$pdf->AddPage();
		$pdf->setHeader($id);

		$pdf->Output();
		// $pdf->Output(utf8_decode("reporte_fichas_" . $id . ".pdf"), 'D');
	}
}
