<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function zona_horaria_bd()
{
	$CI =& get_instance();
	// Reutiliza la conexión si el controlador ya la abrió; los modelos que la carguen después usan la misma.
	if ( ! isset($CI->db) || ! is_object($CI->db)) $CI->load->database();
	$CI->db->simple_query("SET time_zone = '-05:00'");
}
