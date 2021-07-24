<?phpinsert_lugar
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Tourscix extends REST_Controller {
	
	public function __construct() {
		parent::__construct();
		$this->load->model('General_model');
	}

	public function getCount_get() {
		$count_lugares = $this->General_model->get_data_dynamic('lugar_turisticoaux', "count(*) as count")[0]->count;
		$count_festividades = $this->General_model->get_data_dynamic('festividad', "count(*) as count")[0]->count;
		$total = $count_festividades + $count_lugares;

		$this->response(array('count' => $total));
	}
	public function getAll_get() {
		$lugares = $this->General_model->get_data_dynamic('lugar_turisticoaux', "*", false, array('estado' => "H"));
		$festividades = $this->General_model->get_data_dynamic('festividad', "*", false, array('estado' => "H"));

		$response = array(
			'lugares' => $lugares, 
			'festividades' => $festividades
		);
		$this->response($response);
	}
	public function getFotosLugares_post(){
		$cod_lugar = html_purify($this->input->post('cod_lugar'));
		$limit = html_purify($this->input->post('limit'));
		if ($cod_lugar) {
			if ($limit) {
				$fotos = $this->General_model->get_data_dynamic('foto_atractivo', "*", false, array('cod_lugar' => (int) $cod_lugar), true);
			}else{
				$fotos = $this->General_model->get_data_dynamic('foto_atractivo', "*", false, array('cod_lugar' => (int) $cod_lugar));
			}
			$this->response($fotos);
		}else{
			$this->response("false");
		}
	}
	public function getFotosFestividades_post(){
		$cod_fest = html_purify($this->input->post('cod_fest'));
		$limit = html_purify($this->input->post('limit'));
		if ($cod_fest) {
			if ($limit) {
				$fotos = $this->General_model->get_data_dynamic('foto_fest', "*", false, array('cod_fest' => (int) $cod_fest), true);
			}else{
				$fotos = $this->General_model->get_data_dynamic('foto_fest', "*", false, array('cod_fest' => (int) $cod_fest));
			}
			$this->response($fotos);
		}else{
			$this->response("false");
		}
	}
	public function setValoracion_post(){
		$cod_lugar = html_purify($this->input->post('cod_lugar'));
		$valoracion = html_purify($this->input->post('valoracion'));
		$votantes = html_purify($this->input->post('$votantes'));
		if ($cod_lugar) {
			$response = $this->General_model->edit_dynamic('lugar_turisticoaux', array("cod_lugar" => $cod_lugar), array("Valoracion" => $valoracion, "votantes" => $votantes));
			$this->response($response);
		}else{
			$this->response("false");
		}
	}
	public function insertRecomendacion_post(){
		$asunto = html_purify($this->input->post('asunto'));
		$mensaje = html_purify($this->input->post('mensaje'));
		$nombre = html_purify($this->input->post('nombre'));
		$contacto = html_purify($this->input->post('contacto'));
		if ($asunto && $mensaje && $nombre && $contacto) {
			$data = array(
				'asunto' => $asunto, 
				'mensaje' => $mensaje, 
				'nombre' => $nombre, 
				'telefono' => $contacto
			);
			$response = $this->General_model->insert_dynamic('recomendacion', $data);
			$this->response($response);
		}else{
			$this->response("false");
		}
	}
}
