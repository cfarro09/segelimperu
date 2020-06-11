<?php
class General_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}
	//GET COLUMN'S NAME OF USUARIOS AND DATA
    function get_columns_name($name_table){
    	$sql=" SHOW FULL COLUMNS FROM " . $name_table . ";";
	    $query = $this->db->query($sql);
	    $result = $query->num_rows() >= 1 ? $query->result() : false;
	    return $result;
    }
    function get_columns_only_name($name_table){
    	$sql="Describe " . $name_table . ";";
	    $query = $this->db->query($sql);
	    $result = $query->num_rows() >= 1 ? $query->result() : false;
	    return $result;
    }
    function get_data_dynamic($table, $select, $foranea = false) {
        $this->db->select($select);
        if ($foranea) {
            foreach ($foranea as $fk) {
                $this->db->join($fk['tablefk'] ,$fk['comp'], 'LEFT');
            }
        }
        $query = $this->db->get($table);
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function insert_dynamic($name_table, $data){
        $this->db->insert($name_table,$data);
        if ($this->db->affected_rows() > 0) {
            $response = array(
                "success" => true,
                "msg" => "Se registró satisfactoriamente."
            );
        }else{
            $response = array(
                "success" => false,
                "msg" => "Hubo un problema, vuelva."
            );
        }
        return $response;
    }
    function delete_dynamic($name_table, $where){
        if (!$this->db->delete($name_table, $where)) {
            $error = $this->db->error();
            if ($error['code']==1451) {
                $response = array(
                    "success" => false,
                    "msg" => "El registro que está intentando eliminar está siendo usado por otra tabla. Elimine aquel registro y despues intente eliminar el actual registro."
                );
            }else{
                $response = array(
                    "success" => false,
                    "msg" => "Error desconocido, vuelva a intentarlo."
                );
            }
        }else{
            if ($this->db->affected_rows() > 0) {
                $response = array(
                    "success" => true,
                    "msg" => "Se eliminó satisfactoriamente."
                );
            }else{
                $response = array(
                    "success" => false,
                    "msg" => $this->db->error()
                );
            }
        }
        return $response;
    }
    function get_data_id_dynamic($name_table, $where){
        $this->db->select('*');
        $this->db->where($where);
        $query = $this->db->get($name_table);
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function edit_dynamic($name_table, $where, $data){
        $this->db->where($where);
        $this->db->update($name_table, $data);
        if ($this->db->affected_rows() > 0) {
            $response = array(
                "success" => true,
                "msg" => "Se editó satisfactoriamente."
            );
        }else{
            $response = array(
                "success" => false,
                "msg" => "Hubo un problema, vuelva a intentarlo."
            );
        }
        return $response;
    }
    function get_tables_active(){
        $this->db->select("*");
        $this->db->where(["statusTable" => "AC"]);
        $query = $this->db->get("adminpro_dynamic");
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function get_tipos_documento(){
        $this->db->select("*");
        $query = $this->db->get("Tipos_documentos");
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function get_personal(){
        $this->db->select('nombres, idpers');
        $query = $this->db->get("Personal");
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
}
