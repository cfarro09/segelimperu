<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller {

	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('id_usu')) { //isLogin
			redirect("login", "refresh");
		}
		$this->load->library('html');
		$this->load->library('layout');
	}

	public function index(){
		require_once APPPATH.'libraries/Almacen_acceso.php';
		$rol = $this->session->userdata('tipo_usu');
		if (Almacen_acceso::es_operativo($rol)) {
			redirect(Almacen_acceso::inicio($rol));
			return;
		}
		if ($this->session->userdata('tipo_usu') == "RC") {
			redirect("recibo/listar", "refresh");
		}

		if ($this->session->userdata('tipo_usu') == "AD" || $this->session->userdata('tipo_usu') == "SA" ) {
			$data['menu_tables']  = json_decode($this->session->userdata('menu_tables'));
		}else{
			$this->load->model('Permisos_model');
			$data['menu_tables'] = $this->Permisos_model->get_permisos_type_user($this->session->userdata('tipo_usu'));
		}
		
		$data['es_admin'] = in_array($this->session->userdata('tipo_usu'), array('AD', 'SA'), true);
		if ($data['es_admin']) $data['indicadores'] = $this->indicadores_almacen();
		$this->layout->view('home/index', $data);
	}

	// Lo que está esperando acción en almacén, para la portada.
	private function indicadores_almacen()
	{
		$this->load->database();
		$db = $this->db;
		$revisar = $db->select('periodo, COUNT(*) AS n', false)->where('estado', 'publicado')->group_by('periodo')->order_by('periodo', 'DESC')->get('almacen_asignaciones')->result();
		$aprobar = $db->select('periodo, COUNT(*) AS n', false)->where('estado', 'borrador')->group_by('periodo')->order_by('periodo', 'DESC')->get('almacen_compras')->result();
		$pendientes = $db->query("SELECT SUM(estado <> 'atendido') AS abiertos, SUM(estado <> 'atendido' AND prioridad = 'urgente') AS urgentes FROM almacen_pendientes")->row();
		$cargos = $db->query('SELECT SUM(archivo IS NULL) AS por_subir, SUM(archivo IS NOT NULL AND verificado_en IS NULL) AS por_verificar FROM almacen_cargos')->row();
		return array(
			'revisar' => array_sum(array_column($revisar, 'n')), 'revisar_periodo' => $revisar ? $revisar[0]->periodo : date('Y-m'),
			'aprobar' => array_sum(array_column($aprobar, 'n')), 'aprobar_periodo' => $aprobar ? $aprobar[0]->periodo : date('Y-m'),
			'pendientes' => (int) $pendientes->abiertos, 'urgentes' => (int) $pendientes->urgentes,
			'cargos_subir' => (int) $cargos->por_subir, 'cargos_verificar' => (int) $cargos->por_verificar,
		);
	}

}
