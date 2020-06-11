<?php
class VoletasUnif_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	function register_voleta($data) {
		$this->db->insert("user_vol_unif",$data);
		if ($this->db->affected_rows() > 0) {
			$response = array(
				"success" => true,
				"msg" => "Se agrego la voleta satisfactoriamente."
			);
		}else{
			$response = array(
				"success" => false,
				"msg" => "Hubo un problema, vuelva intentarlo."
			);
		}
		return $response;
	}
	function register_detalle_voleta($array) {
		$this->db->insert_batch("detalle_vol_unif",$array);
		if ($this->db->affected_rows() > 0) {
			$response = array(
				"success" => true,
				"msg" => "Se agrego la voleta y su detalle satisfactoriamente."
			);
		}else{
			$response = array(
				"success" => false,
				"msg" => "Hubo un problema, vuelva intentarlo."
			);
		}
		return $response;
	}
	function get_personal_with_vol_unif() {
		$this->db->select("*");
		$query = $this->db->get("user_vol_unif");
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	function get_vol_by_dni($dni) {
		$this->db->select("*");
		$this->db->where('dni', $dni);
		$this->db->limit(1);
		$query = $this->db->get("user_vol_unif");
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	function get_vol_by_id($nro_voleta) {
		$this->db->select("*");
		$this->db->where('nro_voleta', $nro_voleta);
		$query = $this->db->get("user_vol_unif");
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	function get_detalle_vol_by_id($nro_voleta) {
		$this->db->select("*");
		$this->db->where('nro_voleta', $nro_voleta);
		$query = $this->db->get("detalle_vol_unif");
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
	function get_detalle_vol_by_dni($dni) {
		$this->db->select("d.detalle, d.color, d.talla, d.cantidad, d.estado, d.nro_voleta");
		$this->db->where('v.dni', $dni);
		$this->db->join('user_vol_unif v' ,'nro_voleta', 'LEFT');
		$this->db->order_by("d.nro_voleta", "asc");
		$query = $this->db->get("detalle_vol_unif d");
		return ($query->num_rows() >= 1) ? $query->result() : false;
	}
}

// select * from detalle_vol_unif left join user_vol_unif on user_vol_unif.nro_voleta = detalle_vol_unif.nro_voleta where user_vol_unif.dni = '73147683'