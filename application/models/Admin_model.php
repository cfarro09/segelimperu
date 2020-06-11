<?php
class Admin_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}
    function update_state_table($data){
        $this->db->where($data['where']);
        $query = $this->db->update("adminpro_dynamic", $data['data']);
        if($this->db->affected_rows() == 0){
            $query = $this->db->insert("adminpro_dynamic",array_merge($data['data'], $data['where']));
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
    function send_comment($arreglo){
        $sql="ALTER TABLE ". $arreglo['name_table']." modify ".  $arreglo['name_column']. " " . $arreglo['type_column']. " " .$arreglo['null_column']. " comment '" . $arreglo['comment_column'] . "'" ;
        $query = $this->db->query($sql);
        return $query;
    }
    function get_admin_table(){
        $this->db->select("*");
        $query = $this->db->get("adminpro_dynamic");
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function get_data_dependence($table) {
        $this->db->select('*');
        $query = $this->db->get($table);
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function get_referenced_table($table, $column) {
        $this->db->select('REFERENCED_TABLE_NAME');
        $this->db->where('TABLE_NAME', $table);
        $this->db->where('REFERENCED_COLUMN_NAME', $column);
        $query = $this->db->get('INFORMATION_SCHEMA.KEY_COLUMN_USAGE');
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function show_tables(){
        $sql="SHOW TABLES";
        $query = $this->db->query($sql);
        $result = $query->num_rows() >= 1 ? $query->result() : false;
        return $result;
    }
}
