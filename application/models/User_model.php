<?php
class User_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
	}
  	function register($arreglo) {
		try {
			$this->db->select("count(id_usu) as count");
            $this->db->where("email_usu",$arreglo['email_usu']);
            $query = $this->db->get('Usuarios');
            if ($query->row()->count >= 1) {
                $response = array(
                	"success" => false,
                	"msg" => "Correo ya registrado"
                );
                return $response;
            }else {
                $this->db->insert("Usuarios", $arreglo);
  	    		$success = ($this->db->affected_rows() != 1) ? false : true;
  	    		$msg= $success? "Se registró": "Hubo un problema, vuelva a intentarlo";
  	    		$response = array(
                	"success" => $success,
                	"msg" => $msg
                );
                return $response;
            }
	    } catch (Exception $e) {
            $response = array(
                	"success" => false,
                	"msg" => $e
                );
			return $response;
        }
    }
    function validate_email_internal($arreglo){
        try {
            $this->db->select("id_usu");
            $this->db->where("email_usu",$arreglo['email_usu']);
            $query = $this->db->get('Usuarios');
            if ($query->num_rows() == 1) {
                $response = array(
                    "success" => true,
                    "id_usu" =>  $query->row()->id_usu
                );
            }else{
                $response = array(
                    "success" => false,
                    "msg" => "Correo no registrado"
                );
            }
            return $response;
        } catch (Exception $e) {
            $response = array(
                    "success" => false,
                    "msg" => $e
                );
            return $response;
        }
    }
    function validate_email($arreglo){
        try {
            $this->db->select("id_usu");
            $this->db->where("email_usu",$arreglo['email_usu']);
            $query = $this->db->get('Usuarios');
            if ($query->num_rows() == 1) {
                $response = array(
                    "success" => true
                );
            }else{
                $response = array(
                    "success" => false,
                    "msg" => "Correo no registrado"
                );
            }
            return $response;
        } catch (Exception $e) {
            $response = array(
                    "success" => false,
                    "msg" => $e
                );
            return $response;
        }
    }
    function login($arreglo){
        try {
            $this->db->select("*");
            $this->db->where("email_usu",$arreglo['email_usu']);
            $query = $this->db->get('Usuarios');
            $this->load->library('encryption');
            if ($query->num_rows()==1) {
                if($query->row()->estado_usu === 'IN'){
                    $response = array(
                        "success" => false,
                        "msg" => "Su usuario se encuentra bloqueado"
                    );
                }else if($query->row()->estado_usu === 'PG'){
                    $response = array(
                        "success" => false,
                        "msg" => "Su usuario aun no ha sido confirmado"
                    );
                }else if($query->row()->estado_usu === 'RJ'){
                    $response = array(
                        "success" => false,
                        "msg" => "Su usuario ha sido rechazado, consulte con el administrador."
                    );
                }else if ($this->encryption->decrypt($query->row()->password) === $arreglo['password']) {
                    $response = array(
                        "success" => true,
                        "id_usu" => $query->row()->id_usu,
                        "nombre_usu" => $query->row()->nombre_usu,
                        "tipo_usu" => $query->row()->tipo_usu
                    );
                    $this->db->where('id_usu', $query->row()->id_usu);
                    $query = $this->db->update('Usuarios', array('intentos_usu' => 0));
                }else{
                    $intentos = $query->row()->intentos_usu + 0;//cambiar de 0 a 1 
                    if ($intentos<3) {
                        $this->db->where('id_usu', $query->row()->id_usu);
                        $query = $this->db->update('Usuarios', array('intentos_usu' => $intentos));
                        $response = array(
                            "success" => false,
                            "msg" => "Password incorrecta. Tiene ".(3-$intentos)." intentos."
                        );
                    }else
{                        $this->db->where('id_usu', $query->row()->id_usu);
                        $now = date("Y-m-d H:i:s");
                        $query = $this->db->update('Usuarios', array('estado_usu' => "IN", 'fechablock_usu' => $now ));
                        $response = array(
                            "success" => false,
                            "msg" => "Su usuario ha sido bloqueado."
                        );
                    }
                }
            }else{
                $response = array(
                    "success" => false,
                    "msg" => "Usuario o contraseña errada"
                );
            }
            return $response;
        } catch (Exception $e) {
            $response = array(
                "success" => false,
                "msg" => $e
            );
        }
        return $response;
    }
      function get_users_suscribers(){
        $this->db->select("id_usu, nombre_usu, apellido_pat, apellido_mat, id_tipo_doc, nro_documento, email_usu");
        $this->db->where("estado_usu","PG");
        $query = $this->db->get('Usuarios');
        return ($query->num_rows() >= 1) ? $query->result() : false;

    }
    function get_all_users(){
        $this->db->select("id_usu, , tipo_usu,nombre_usu, apellido_pat, apellido_mat, name_documento, nro_documento, email_usu, estado_usu");
        $this->db->where('tipo_usu !="SA"');
        $this->db->join('Tipos_documentos', 'Tipos_documentos.id_type = Usuarios.id_tipo_doc', 'left');

        $query = $this->db->get('Usuarios');
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function getcount_users_suscribers(){
        $this->db->select("count(*) count");
        $this->db->where("estado_usu","PG");
        $query = $this->db->get('Usuarios');
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
    function action_user($id, $data){
        $this->db->where(array("id_usu" => $id));
        $this->db->update("Usuarios", $data);
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
        return $response;
    }
    function get_user($id){
        $this->db->select("*");
        $this->db->where("id_usu",$id);
        $query = $this->db->get('Usuarios');
        return ($query->num_rows() >= 1) ? $query->result() : false;
    }
}