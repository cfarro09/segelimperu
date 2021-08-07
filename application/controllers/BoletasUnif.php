<?php

defined('BASEPATH') or exit('No direct script access allowed');

class BoletasUnif extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('id_usu')) { //isLogin
			redirect("login", "refresh");
		}
		$this->load->library('html');
		$this->load->library('layout', 'layout');
		$this->load->model('BoletasUnif_model');
		$this->load->model('General_model');
	}
	public function register()
	{
		$data['menu_tables'] =  $this->General_model->get_tables_active();
		$data['personal'] = $this->General_model->get_data_dynamic('Personal', '*');

		$this->layout->view('boletasUnif/register', $data);
	}
	public function listar()
	{
		$data['menu_tables'] =  $this->General_model->get_tables_active();

		$data['list_boletas'] = $this->BoletasUnif_model->get_personal_with_vol_unif();
		$this->layout->view('boletasUnif/list', $data);
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
	public function edit_vol_nro_vol()
	{
		$data = html_purify($this->input->post());
		$where = array('nro_boleta' => $data['nro_boleta']);
		unset($data['nro_boleta']);
		$response = $this->General_model->edit_dynamic('user_vol_unif', $where, $data);
		echo json_encode($response);
	}
	public function edit_detalle_id()
	{
		$data = html_purify($this->input->post());
		$where = array('id_detalle' => $data['id_detalle']);
		unset($data['id_detalle']);
		$response = $this->General_model->edit_dynamic('detalle_vol_unif', $where, $data);
		echo json_encode($response);
	}
	public function edit_detalle_insert()
	{
		$data = html_purify($this->input->post());
		unset($data['id_detalle']);
		$response = $this->General_model->insert_dynamic('detalle_vol_unif', $data);
		echo json_encode($response);
	}
	public function edit_vol_del_detalle()
	{
		$id_detalle = html_purify($this->input->post('id_detalle'));
		if ($id_detalle) {
			$where = array('id_detalle' => $id_detalle);
			$response = $this->General_model->delete_dynamic('detalle_vol_unif', $where);
			echo json_encode($response);
		} else {
			echo json_encode(false);
		}
	}
	public function edit_vol($value = false)
	{
		if ($value) {
			$data['menu_tables'] =  $this->General_model->get_tables_active();
			$data['boleta'] =  $this->BoletasUnif_model->get_vol_by_id($value);
			$data['detalle_boleta'] =  $this->BoletasUnif_model->get_detalle_vol_by_id($value);

			$this->layout->view('boletasUnif/edit', $data);
		} else {
			redirect('boletasUnif/listar', 'refresh');
		}
	}
	public function pdf_resumen($dni = false)
	{
		if ($dni) {
			$boletas = $this->General_model->get_data_id_dynamic('user_vol_unif', array('dni' => $dni), array("id", "desc"));
			$boleta = $boletas[0];

			$full_name = $boleta->apellido_pat . " " . $boleta->apellido_mat . " " . $boleta->nombres;
			$full_name = iconv('UTF-8', 'windows-1252', $full_name);
			$name_pdf = "FICHA PERSONAL - $full_name";
			$this->load->library('fpdf_gen');
			//cabecera
			$this->fpdf->AliasNbPages();

			$this->fpdf->SetFont('Arial', 'B', 15);
			$this->fpdf->SetTextColor(0, 0, 0);

			$this->fpdf->Cell(0, 15, utf8_decode('RESUMEN ASIGNACION DE UNIFORMES'), 0, 0, 'C');
			$this->fpdf->SetFont('Arial', 'B', 8);
			$this->fpdf->Ln(18);

			$this->fpdf->Cell(30, 6, "Nombre: ", 0, 0, 'L');
			$this->fpdf->Cell(30, 6, $full_name, 0, 0, 'L');
			$this->fpdf->Ln();
			$this->fpdf->Cell(30, 6, "DNI: ", 0, 0, 'L');
			$this->fpdf->Cell(30, 6, $boleta->dni, 0, 0, 'L');
			$this->fpdf->Ln();
			$header = ['DETALLE', 'COLOR', 'TALLA', 'CANT', 'ESTADO', 'POSESION', 'COMENTARIO'];
			// Datos
			$fill = false;
			$pre_vol = false;
			$w = array(50, 30, 15, 15, 20, 25, 40);
			if ($boletas) {
				foreach ($boletas as $boleta) {
					$fechaformated = date("d/m/Y", strtotime($boleta->fecha)); 
					$this->fpdf->SetFont('Arial', 'B', 10);
					$this->fpdf->Cell(0, 15, "NRO BOLETA: $boleta->nro_boleta", 0, 0, 'C');
					$this->fpdf->Cell(0, 15, $fechaformated, 0, 0, 'R');
					$this->fpdf->Ln();
					$this->fpdf->SetFillColor(255, 0, 0);
					$this->fpdf->SetTextColor(255);
					$this->fpdf->SetDrawColor(128, 0, 0);
					$this->fpdf->SetLineWidth(.3);
					$this->fpdf->SetFont('', 'B');
					// Cabecera
					$this->fpdf->SetFont('Arial', 'B', 8);
					for ($i = 0; $i < count($header); $i++)
						$this->fpdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
					$this->fpdf->Ln();
					// Restauracion de colores y fuentes
					$this->fpdf->SetFillColor(224, 235, 255);
					$this->fpdf->SetTextColor(0);
					$detalleBoleta = $this->General_model->get_data_id_dynamic('detalle_vol_unif', array('nro_boleta' => $boleta->nro_boleta));
					if ($detalleBoleta) {
						foreach ($detalleBoleta as $row) {
							$this->fpdf->Cell($w[0], 6, strtoupper($row->detalle), 'LR', 0, 'C', $fill);
							$this->fpdf->Cell($w[1], 6, strtoupper($row->color), 'LR', 0, 'C', $fill);
							$this->fpdf->Cell($w[2], 6, strtoupper($row->talla), 'LR', 0, 'C', $fill);
							$this->fpdf->Cell($w[3], 6, number_format($row->cantidad), 'LR', 0, 'C', $fill);
							$this->fpdf->Cell($w[4], 6, strtoupper($row->estado), 'LR', 0, 'C', $fill);
							$this->fpdf->Cell($w[5], 6, strtoupper($row->posesion), 'LR', 0, 'C', $fill);
							$this->fpdf->Cell($w[6], 6, $row->comentario, 'LR', 0, 'C', $fill);
							$this->fpdf->Ln();
							$fill = !$fill;
						}
						$this->fpdf->Cell(array_sum($w), 0, '', 'T');
					}
					$this->fpdf->Ln();
					$this->fpdf->SetFont('Arial', 'B', 8);
					$this->fpdf->Multicell(0, 5, $boleta->observacion, 0,'L', false);
					$this->fpdf->Ln();
				}
			}
			
			$this->fpdf->Output(utf8_decode($name_pdf . ".pdf"), 'D');
		} else {
			redirect('boletasUnif/listar', 'refresh');
		}
	}

	public function get_personal_data()
	{
		$id_personal = html_purify($this->input->post('persona_id'));
		$where = array('idPersonal' => $id_personal);
		$response = $this->General_model->get_data_id_dynamic('Personal', $where);
		echo json_encode($response);
	}
}
