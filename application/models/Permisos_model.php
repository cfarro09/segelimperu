<?php
class Permisos_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}
    function get_categories(){
        $this->db->select("*");
        $query = $this->db->get("Type_working");
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function get_permisos_user($where){
        $this->db->select("*");
        $this->db->where($where);
        $query = $this->db->get("Permisos");
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function get_permisos_type_user($work_char){
        $this->db->select("p.nameTable, a.aliasTable, p.escritura, p.lectura");
        $this->db->where('id_work = (select id_work from Type_working where work_char = "'.$work_char.'")');
        $this->db->join('adminpro_dynamic a' ,'nameTable', 'LEFT');

        $query = $this->db->get("Permisos p");
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function update_permiso($data){
        $this->db->where($data['where']);
        $query = $this->db->update("Permisos", $data['data']);
        if($this->db->affected_rows() == 0){
            $query = $this->db->insert("Permisos",array_merge($data['data'], $data['where']));
        }
        if ($query) {
             if ($this->db->affected_rows() > 0) {
                $response = array(
                    "success" => true,
                    "msg" => "Se actualizó satisfactoriamente."
                );
            }else{
                $response = array(
                    "success" => false,
                    "msg" => "Hubo un problema, vuelva a intentarlo."
                );
            }
        }
        return $response;
    }
}
