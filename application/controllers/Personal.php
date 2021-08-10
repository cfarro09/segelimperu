<?php



defined('BASEPATH') or exit('No direct script access allowed');



class Personal extends CI_Controller

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



		$data['listpersonal'] = $this->General_model->get_data_dynamic("Personal", "*");

		$this->layout->view('personal/list', $data);

	}



	public function generar_ficha1($pdf, $inf, $con, $hij, $emp)

	{

		$pdf->AddPage();

		$pdf->setHeader();

		$pdf->setDatosFicha(

			'RH-'.$inf->cod_reporte,

			$inf->nrofile,

			date('Y')

		);

		// $pdf->setDatosFicha('RH-01', '000012', '2019');

		// $pdf->setInstrucciones();



		if(empty($inf->rutafoto) || !isset($inf->rutafoto)){

			$inf->rutafoto = base_url() . 'imagenes/default.png';

		}



		$pdf->setDatosGenerale(

			$inf->apellido_pat,

			$inf->apellido_mat,

			$inf->nombres,

			$inf->rutafoto,

			$inf->nrodocumento,

			$inf->edad,

			$inf->nacionalidad,

			$inf->sexo,

			$inf->correo,

			$inf->fecha_nacimiento,

			$inf->departamento,

			$inf->provincia,

			$inf->distrito

		);



		$pdf->setNivelEducativo(

			$inf->educacionprimaria,

			$inf->educacionsecundaria,

			$inf->educacionsuperior,

			$inf->educacioncarrera,

			$inf->educaciontitulo

		);



		// Pagina 2

		$pdf->AddPage();

		$pdf->setExperienciaLaboral($inf->empresas);

		$pdf->setDatosConyugue($inf->conyuge);

		$pdf->setDatosHijos($inf->hijos);



		// Pagina 3

		$pdf->AddPage();

		$pdf->setInformacionAdicional(

			$inf->antecedentes,

			$inf->sindicato,

			$inf->emergencias,

			$inf->nroemergencias,

			$inf->direccion,

			$inf->padrenombrecompleto,

			$inf->padreocupacion,

			$inf->madrenombrecompleto,

			$inf->madreocupacion

		);

		$pdf->setUbicacionDomicilio(

			$inf->direccionactual,

			$inf->telefonoactual,

			$inf->distritoactual,

			$inf->provinciaactual,

			$inf->departamentoactual,

			$inf->rutamapa

		);



		// Pagina 4f

		$pdf->AddPage();

		$pdf->setMedidasIndumentaria($inf->tallacamisa, $inf->tallapantalon, $inf->tallacalzado);

		$pdf->setFooter($inf->rutafirmahuella, date("d/m/Y", strtotime($inf->fecha_ingreso)));

	}



	public function generar_ficha2($pdf, $inf)

	{

		$pdf->AddPage();

		$pdf->setHeader();

		$pdf->setFicha2Header();



		$pdf->setBodyFicha2(

			$inf->nombres . ' ' . $inf->apellido_pat . ' ' . $inf->apellido_mat,

			$inf->edad,

			$inf->tipodocumento,

			$inf->nrodocumento,

			$inf->direccionactual,

			$inf->distritoactual,

			$inf->provinciaactual,

			$inf->departamentoactual,

			$inf->rutafirmahuella,

			date("d/m/Y", strtotime($inf->fecha_ingreso))

		);

	}



	public function generar_ficha4($pdf, $inf)

	{

		$pdf->AddPage();

		$pdf->setHeader();

		$pdf->setFicha4Header();

		$pdf->setBodyFicha4(

			$inf->nombres . ' ' . $inf->apellido_pat . ' ' . $inf->apellido_mat,

			$inf->edad,

			$inf->tipodocumento,

			$inf->nrodocumento,

			$inf->nacionalidad,

			$inf->rutafirmahuella,

			date("d/m/Y", strtotime($inf->fecha_ingreso))

		);

	}



	public function generar_ficha3($pdf, $inf)

	{

		$pdf->AddPage();

		$pdf->setFicha3Header(

			$inf->tipodocumento,

			$inf->nrodocumento,

			$inf->edad,

			$inf->telefonoactual,

			$inf->nombres . ' ' . $inf->apellido_pat . ' ' . $inf->apellido_mat

		);

		$pdf->setBodyFicha3($inf->rutafirmahuella, date("d/m/Y", strtotime($inf->fecha_ingreso)));

	}



	public function generar_fotos($pdf,$inf)

	{

		// Hojas extra

		$img_extra = json_decode($inf->rutasextras);

		if (!empty($img_extra)) {

			foreach ($img_extra as $ruta) {

				$pdf->setImagenPagina($ruta);

			}

		}

	}



	public function registrar()

	{

		$data['menu_tables'] =  $this->General_model->get_tables_active();

		$this->layout->view('personal/register', $data);

	}

	public function editar($id = false)

	{

		$data['menu_tables'] =  $this->General_model->get_tables_active();



		$data['r'] = $this->General_model->get_data_id_dynamic('Personal', array('idPersonal' => $id))[0];

		$this->layout->view('personal/editar', $data);

	}

	public function loadimage()

	{

		$directorio = "./imagenes/";

		$fecha = date('YmdH_i_s');

		$rutafoto = "";

		$data = $_POST["image"];

		$image_array_1 = explode(";", $data);

		$image_array_2 = explode(",", $image_array_1[1]);

		$data = base64_decode($image_array_2[1]);

		$imageName = $fecha . '.png';

		file_put_contents($directorio . $imageName, $data);

		$rutafoto = base_url() . 'imagenes/' . $imageName;

		echo json_encode(array('rutafoto' => $rutafoto));

	}



	public function loadimage1()

	{

		$directorio = "./imagenes/";

		if (!is_dir($directorio)) {

			mkdir($directorio, 0755, true);

		}

		$fecha = date('YmdHis');



		$rutafoto = "";

		$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

		$ext = strtolower($ext);

		$archivo = $fecha . "." . $ext;

		$destino =  $directorio . $archivo;

		if (move_uploaded_file($_FILES['file']['tmp_name'], $destino)) {

			$rutafoto = base_url() . 'imagenes/' . $archivo;

		}

		echo json_encode(array('rutafoto' => $rutafoto));

	}

	public function loadmultiimagenes()

	{

		$directorio = "./imagenes/";

		if (!is_dir($directorio)) {

			mkdir($directorio, 0755, true);

		}

		$fecha = date('YmdHis');

		$ix = 0;

		$arrrutas = array();

		

		for ($i = 0; $i < count($_FILES['files']['name']); $i++) {

			$ext = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);

			$ext = strtolower($ext);

			$archivo = $fecha . $ix . "." . $ext;

			$destino =  $directorio . $archivo;

			if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $destino)){

				

				array_push($arrrutas, base_url() . 'imagenes/' . $archivo);

			}

			$ix++;

		}

		echo json_encode($arrrutas);

	}



	public function insertar()

	{

		$data = html_purify($this->input->post());

		if (isset($data["idPersonal"]) && $data["idPersonal"]) {

			$where = array("idPersonal" => $data["idPersonal"]);

			unset($data['idPersonal']);

			$response = $this->General_model->edit_dynamic('Personal', $where, $data);

		} else {

			$response = $this->General_model->insert_dynamic("Personal", $data);

		}

		echo json_encode($response);

	}



	public function reportefichas($id = false)

	{

		$infoPersonal = $this->General_model->get_data_id_dynamic('Personal', array('idPersonal' => $id))[0];



		$propiedades = $this->General_model->get_data_id_dynamic('propiedades', array('llave' => 'reportepersonal'))[0];

		

		$cod_reporte = (int) $propiedades->valor + 1;



		$infoPersonal->cod_reporte = $cod_reporte;

		$conyuge = json_decode($infoPersonal->conyuge);

		$hijos = json_decode($infoPersonal->hijos);

		$empresas = json_decode($infoPersonal->empresas);



		$this->load->library('reporte_personal');

		$pdf = new reporte_personal();



		// Ficha Datos Generales

		$this->generar_ficha1($pdf, $infoPersonal, $conyuge, $hijos, $empresas);

		

		// Dni Escaneado

		if(!empty($infoPersonal->rutaimagendni)){

			$pdf->setImagenPagina($infoPersonal->rutaimagendni);

		}



		//Declaracion Jurada Domicilio

		$this->generar_ficha2($pdf, $infoPersonal); 



		// Recibo Servicios

		if(!empty($infoPersonal->rutaimagenreciboservicios)){

			$pdf->setImagenPagina($infoPersonal->rutaimagenreciboservicios);

		}



		// Antecedentes

		if(!empty($infoPersonal->rutaimagenantecedentes)){

			$pdf->setImagenPagina($infoPersonal->rutaimagenantecedentes);

		}



		// Declaracion de Salud

		$this->generar_ficha3($pdf, $infoPersonal);



		// Declaracion Jurada de Confidencialidad

		$this->generar_ficha4($pdf, $infoPersonal);



		// Generar fotos extra

		$this->generar_fotos($pdf, $infoPersonal);



		// Actualizamos el numero de reporte

		$this->General_model->edit_dynamic('propiedades', array('llave' => 'reportepersonal'), array('valor' => $cod_reporte));



		// $pdf->Output();

		$nn = "FICHA PERSONAL - " . $infoPersonal->nombres . " " .$infoPersonal->apellido_pat . " " .$infoPersonal->apellido_mat;
		
		$pdf->Output(utf8_decode($nn . ".pdf"), 'D');

	}

}