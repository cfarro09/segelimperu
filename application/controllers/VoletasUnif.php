<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class VoletasUnif extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('id_usu')) { //isLogin
			redirect("login", "refresh");
		}
		$this->load->library('html');
		$this->load->library('layout', 'layout');
		$this->load->model('VoletasUnif_model');
		$this->load->model('General_model');
	}
	public function register(){
		$data['menu_tables'] =  $this->General_model->get_tables_active();
		$data['personal'] = $this->General_model->get_data_dynamic('Personal', '*');

		$this->layout->view('voletasUnif/register', $data);
	}
	public function listar(){
		$data['menu_tables'] =  $this->General_model->get_tables_active();

		$data['list_voletas'] = $this->VoletasUnif_model->get_personal_with_vol_unif();
		$this->layout->view('voletasUnif/list', $data);
	}
	public function register_voleta(){
		$idpers = html_purify($this->input->post('idpers'));
		$fecha = html_purify($this->input->post('fecha'));
		$apellido_pat = html_purify($this->input->post('apellido_pat'));
		$apellido_mat = html_purify($this->input->post('apellido_mat'));
		$nombres = html_purify($this->input->post('nombres'));
		$dni = html_purify($this->input->post('dni'));
		$nro_voleta = html_purify($this->input->post('nro_voleta'));
		$observacion = html_purify($this->input->post('observacion'));
		$detalle_voleta = html_purify($this->input->post('data_extra'));

		if ($apellido_pat && $apellido_mat && $nombres && $dni && $fecha && $nro_voleta) {
			$send_voleta = array(
				'fecha' => $fecha, 
				'nro_voleta' => (int) $nro_voleta,
				'nombres' => $nombres,
				'apellido_mat' => $apellido_mat,
				'apellido_pat' => $apellido_pat,
				'dni' => $dni,
				'observacion' => $observacion
			);
			$response =  $this->VoletasUnif_model->register_voleta($send_voleta);
			if ($detalle_voleta) {
				if (isset($response['success']) && $response['success']) {
					$detalle_voleta = json_decode($detalle_voleta);
					$response_1 =  $this->VoletasUnif_model->register_detalle_voleta($detalle_voleta);
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
	public function edit_vol_nro_vol(){
		$data = html_purify($this->input->post());
		$where = array('nro_voleta' => $data['nro_voleta']);
		unset($data['nro_voleta']);
		$response = $this->General_model->edit_dynamic('user_vol_unif', $where, $data);
		echo json_encode($response); 
	}
	public function edit_detalle_id(){
		$data = html_purify($this->input->post());
		$where = array('id_detalle' => $data['id_detalle']);
		unset($data['id_detalle']);
		$response = $this->General_model->edit_dynamic('detalle_vol_unif', $where, $data);
		echo json_encode($response); 
	}
	public function edit_detalle_insert(){
		$data = html_purify($this->input->post());
		unset($data['id_detalle']);
		$response = $this->General_model->insert_dynamic('detalle_vol_unif', $data);
		echo json_encode($response); 
	}
	public function edit_vol_del_detalle(){
		$id_detalle = html_purify($this->input->post('id_detalle'));
		if ($id_detalle) {
			$where = array('id_detalle' => $id_detalle);
			$response = $this->General_model->delete_dynamic('detalle_vol_unif', $where);
			echo json_encode($response); 
		}else{
			echo json_encode(false);
		}
	}
	public function edit_vol($value = false){
		if ($value) {
			$data['menu_tables'] =  $this->General_model->get_tables_active();
			$data['voleta'] =  $this->VoletasUnif_model->get_vol_by_id($value);
			$data['detalle_voleta'] =  $this->VoletasUnif_model->get_detalle_vol_by_id($value);

			$this->layout->view('voletasUnif/edit', $data);
		}else{
			redirect('voletasUnif/listar','refresh');
		}
	}
	 public function pdf_resumen($dni = false){
	 	if ($dni) {
			$boletas = $this->General_model->get_data_id_dynamic('user_vol_unif', array('dni' => $dni));
			$boleta = $boletas[0];
			$detalles_total =  $this->VoletasUnif_model->get_detalle_vol_by_dni($dni);
			$full_name = $boleta->apellido_pat." ".$boleta->apellido_mat." ".$boleta->nombres;
			$full_name = iconv('UTF-8', 'windows-1252', $full_name);
			$name_pdf = "resumen_$boleta->dni";
			$this->load->library('fpdf_gen');
			//cabecera
			$this->fpdf->AliasNbPages();

			$this->fpdf->SetFont('Arial', 'B', 22);
			$this->fpdf->SetTextColor(0, 0,0 );

			$this->fpdf->Cell(0,15,utf8_decode('Resumen Asignaci¨®n de Uniforme'),0,0,'C');
			$this->fpdf->SetFont('Arial', 'B', 12 );
			$this->fpdf->Ln(18);

			$this->fpdf->Cell(30,6,"Nombre: ",0,0,'L');
			$this->fpdf->Cell(30,6,$full_name,0,0,'L');
			$this->fpdf->Ln();
			$this->fpdf->Cell(30,6,"DNI: ",0,0,'L');
			$this->fpdf->Cell(30,6,$boleta->dni,0,0,'L');
			$this->fpdf->Ln();
			$header = ['DETALLE','COLOR','TALLA','CANTIDAD','ESTADO', 'N BOL'];
		    // Datos
		    $fill = false;
		    $pre_vol = false;
		    $w = array(40, 30, 30, 30, 30, 15);
		    if($boletas){
		        foreach($boletas as $boleta){
		            $this->fpdf->SetFont('Arial', 'B', 12 );
						$this->fpdf->Cell(0,15,"NRO BOLETA: $boleta->nro_voleta",0,0,'C');
			    		$this->fpdf->Ln();
			    		$this->fpdf->SetFillColor(255,0,0);
					    $this->fpdf->SetTextColor(255);
					    $this->fpdf->SetDrawColor(128,0,0);
					    $this->fpdf->SetLineWidth(.3);
					    $this->fpdf->SetFont('','B');
					    // Cabecera
					    for($i=0;$i<count($header);$i++)
					        $this->fpdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
					    $this->fpdf->Ln();
					    // Restauracion de colores y fuentes
					    $this->fpdf->SetFillColor(224,235,255);
					    $this->fpdf->SetTextColor(0);
					    $this->fpdf->SetFont('');
					    $detalleBoleta = $this->General_model->get_data_id_dynamic('detalle_vol_unif', array('nro_voleta' => $boleta->nro_voleta));
					    if($detalleBoleta){
					        foreach($detalleBoleta as $row){
					            $this->fpdf->Cell($w[0],6,$row->detalle,'LR',0,'L',$fill);
            			        $this->fpdf->Cell($w[1],6,$row->color,'LR',0,'L',$fill);
            			        $this->fpdf->Cell($w[2],6,$row->talla,'LR',0,'L',$fill);
            			        $this->fpdf->Cell($w[3],6,number_format($row->cantidad),'LR',0,'R',$fill);
            			        $this->fpdf->Cell($w[4],6,$row->estado,'LR',0,'L',$fill);
            			        $this->fpdf->Cell($w[5],6,$row->nro_voleta,'LR',0,'L',$fill);
            			        $this->fpdf->Ln();
            			        $fill = !$fill;
					        }   
					    }
			
		        }
		    }
			    $this->fpdf->Cell(array_sum($w),0,'','T');
				$this->fpdf->Output(utf8_decode($name_pdf.".pdf"), 'D');
	 	}else{
			redirect('voletasUnif/listar','refresh');
	 	}
	}

	public function get_personal_data(){
		$id_personal = html_purify($this->input->post('persona_id'));
		$where = array('idPersonal' => $id_personal);
		$response = $this->General_model->get_data_id_dynamic('Personal',$where);
		echo json_encode($response);
	}


}

