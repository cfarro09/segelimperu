<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['post_controller_constructor'][] = array(
    'class' => 'Acceso_almacen',
    'function' => 'verificar',
    'filename' => 'Acceso_almacen.php',
    'filepath' => 'hooks'
);

// Sesión de MySQL en hora de Lima (Perú no usa horario de verano), para NOW() y los TIMESTAMP.
$hook['post_controller_constructor'][] = array(
	'class'    => '',
	'function' => 'zona_horaria_bd',
	'filename' => 'Zona_horaria.php',
	'filepath' => 'hooks'
);

