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

	public function actas()
	{
		$data['menu_tables'] =  $this->General_model->get_tables_active();
		$this->layout->view('recibo/actas', $data);
	}

	public function certificados()
	{
		$data['menu_tables'] =  $this->General_model->get_tables_active();
		$this->layout->view('recibo/certificados', $data);
	}

	public function getData()
	{
		$data = $this->General_model->get_data_dynamic("receipt", "*");
		die(json_encode(array("data" => $data)));
	}

	public function getDataActa()
	{
		$data = $this->General_model->get_data_dynamic("acta", "*");
		die(json_encode(array("data" => $data)));
	}

	public function getDataCertificado()
	{
		$data = $this->General_model->get_data_dynamic("certificado", "*");
		die(json_encode(array("data" => $data)));
	}


	public function insertar()
	{
		$data = html_purify($this->input->post());
		if ($data["receipt_id"] != "0") {
			$where = array("receipt_id" => $data["receipt_id"]);
			unset($data['receipt_id']);
			$response = $this->General_model->edit_dynamic('receipt', $where, $data);
		} else {
			unset($data['receipt_id']);
			$response = $this->General_model->insert_dynamic("receipt", $data);
		}
		echo json_encode($response);
	}

	public function insertaracta()
	{
		$data = html_purify($this->input->post());
		if ($data["actaid"] != "0") {
			$where = array("actaid" => $data["actaid"]);
			unset($data['actaid']);
			$response = $this->General_model->edit_dynamic('acta', $where, $data);
		} else {
			unset($data['actaid']);
			$response = $this->General_model->insert_dynamic("acta", $data);
		}
		echo json_encode($response);
	}
	public function insertarcertificado()
	{
		$data = html_purify($this->input->post());
		if ($data["certificadoid"] != "0") {
			$where = array("certificadoid" => $data["certificadoid"]);
			unset($data['certificadoid']);
			$response = $this->General_model->edit_dynamic('certificado', $where, $data);
		} else {
			unset($data['certificadoid']);
			$response = $this->General_model->insert_dynamic("certificado", $data);
		}
		echo json_encode($response);
	}

	public function reporte($id)
	{
		$this->load->library('Reporte_recibo');
		$data = $this->General_model->get_data_id_dynamic('receipt', array('receipt_id' => $id))[0];
		$pdf = new Reporte_recibo();
		$pdf->AddPage();
		$pdf->setHeader($id);
		$pdf->setDatosRecibo($data);

		$pdf->Output();
		// $pdf->Output(utf8_decode("reporte_fichas_" . $id . ".pdf"), 'D');
	}
	
	public function reporte_certificado($id)
	{
		$this->load->library('Reporte_certificado');
		$data = $this->General_model->get_data_id_dynamic('certificado', array('certificadoid' => $id))[0];
		$pdf = new Reporte_certificado();
		$pdf->AddPage();
		$pdf->setHeader($id);
		$pdf->setData($data);

		// $pdf->Output();
		$pdf->Output(utf8_decode("Reporte_certificado_" . $id . ".pdf"), 'D');
	}
	
	public function reporte_acta($id)
	{
		$this->load->library('Reporte_conformidad');
		$data = $this->General_model->get_data_id_dynamic('acta', array('actaid' => $id))[0];
		$pdf = new Reporte_conformidad();
		$pdf->AddPage();
		$pdf->setHeader($id);
		$pdf->setData($data);
		$pdf->setFooter($data);

		// $pdf->Output();
		$pdf->Output(utf8_decode("Reporte_conformidad_" . $id . ".pdf"), 'D');
	}
}
