<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH.'libraries/Almacen_acceso.php';

class Acceso_almacen
{
    public function verificar()
    {
        $ci =& get_instance();
        $rol = $ci->session->userdata('tipo_usu');
        if ($ci->session->userdata('id_usu') && Almacen_acceso::es_operativo($rol)
            && !Almacen_acceso::permite($rol, $ci->router->fetch_class(), $ci->router->fetch_method())) {
            show_error('Tu rol no tiene permiso para este módulo o acción.', 403);
            exit;
        }
    }
}
