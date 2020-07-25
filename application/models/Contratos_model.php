<?php
class Contratos_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}
    function get_personal_last_contr() {
        $this->db->select("p.idpers, p.nombres, p.nro_doc,  c.end_cont end_cont, c.id_cont");
        $this->db->join("Contratos c","idpers", 'inner');
        $this->db->where('p.estado_pers != "IN"'); 
        $this->db->where('c.end_cont = (select max(end_cont) from Contratos where idpers=p.idpers)'); 
        $this->db->group_by('p.idpers'); 
        $query = $this->db->get("Personal p");
        var_dump($query->num_rows());die;
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function get_history_empl($idpers) {
        $this->db->select("p.idpers, p.nombres, c.time_meses, c.time_dias,c.start_cont, c.end_cont, c.tipo_con, c.empresa_cont");
        $this->db->join("Personal p","idpers", 'inner');
        $this->db->where("idpers",$idpers); 
        $query = $this->db->get("Contratos c");
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function desafiliar_personal($idpers) {
        $this->db->where(array('idpers' => $idpers));
        $this->db->update("Personal", array("estado_pers" => "IN"));
        if ($this->db->affected_rows() > 0) {
            $response = array(
                "success" => true,
                "msg" => "Se desaflió satisfactoriamente."
            );
        }else{
            $response = array(
                "success" => false,
                "msg" => "Hubo un problema, vuelva a intentarlo."
            );
        }
        return $response;
    }
    function register($data) {
        $this->db->insert("Contratos",$data);
        if ($this->db->affected_rows() > 0) {
            $response = array(
                "success" => true,
                "msg" => "Se renovó el contrato satisfactoriamente."
            );
        }else{
            $response = array(
                "success" => false,
                "msg" => "Hubo un problema, vuelva."
            );
        }
        return $response;
    }
}
