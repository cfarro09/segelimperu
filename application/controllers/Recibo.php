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
		$data['personal'] = $this->General_model->get_data_dynamic('Personal', 'idPersonal, nombres, apellido_pat, apellido_mat, nrodocumento, telefonoactual');

		//$data['list_recibos'] = $this->General_model->get_data_dynamic("receipt", "*");

		$this->layout->view('recibo/list', $data);
	}

	public function actas()
	{
		if ($this->session->userdata('tipo_usu') == "RC") {
			redirect("recibo/listar", "refresh");
		}

		$data['menu_tables'] =  $this->General_model->get_tables_active();
		$this->layout->view('recibo/actas', $data);
	}

	public function certificados()
	{
		if ($this->session->userdata('tipo_usu') == "RC") {
			redirect("recibo/listar", "refresh");
		}

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
		unset($data['receipt_status']);
		unset($data['status_observation']);
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

	public function cambiar_estado()
	{
		$allowed_users = array('admin@segelim.com', 'fdcarlosd1@gmail.com');
		if (!in_array(strtolower($this->session->userdata('email_usu')), $allowed_users)) {
			echo json_encode(array("success" => false, "msg" => "No tiene permisos para cambiar el estado del recibo."));
			return;
		}

		$data = html_purify($this->input->post());
		$allowed_status = array('PENDIENTE', 'APROBADO', 'RECHAZADO');
		$receipt_id = isset($data['receipt_id']) ? $data['receipt_id'] : '';
		$receipt_status = isset($data['receipt_status']) ? strtoupper($data['receipt_status']) : '';
		$status_observation = isset($data['status_observation']) ? $data['status_observation'] : '';

		if (!$receipt_id || !in_array($receipt_status, $allowed_status)) {
			echo json_encode(array("success" => false, "msg" => "Datos de estado inválidos."));
			return;
		}

		$this->db->where(array("receipt_id" => $receipt_id));
		$this->db->update('receipt', array(
			"receipt_status" => $receipt_status,
			"status_observation" => $status_observation
		));

		if ($this->db->error()['code']) {
			echo json_encode(array("success" => false, "msg" => $this->db->error()));
			return;
		}

		echo json_encode(array("success" => true, "msg" => "Se actualizó el estado del recibo."));
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
	
	public function reporte_acta($id, $empresa = 'segelim')
	{
		$this->load->library('Reporte_conformidad');
		$data = $this->General_model->get_data_id_dynamic('acta', array('actaid' => $id))[0];
		$pdf = new Reporte_conformidad();
		$soluciones = $empresa === 'soluciones';
		$pdf->setSoluciones($soluciones);
		$pdf->AddPage();
		$pdf->setHeader($id);
		$pdf->setData($data);
		$pdf->setFooter($data);

		// $pdf->Output();
		$pdf->Output(utf8_decode("Reporte_conformidad_" . ($soluciones ? 'soluciones_' : '') . $id . ".pdf"), 'D');
	}
}
