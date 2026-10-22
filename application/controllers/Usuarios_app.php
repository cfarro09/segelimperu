<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Usuarios del aplicativo móvil: ver, crear y editar lo básico (estado, área y vínculo con la plataforma).
class Usuarios_app extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id_usu')) { redirect('login'); exit; }
        if (!in_array($this->session->userdata('tipo_usu'), array('SA', 'AD'), true)) {
            show_error('Tu rol no tiene permiso para este módulo.', 403); exit;
        }
        $this->load->library('layout');
        $this->load->model('App_usuarios_model');
        $this->load->model('Cecon_model'); // fecha() convierte las fechas UTC del API a hora de Lima
        $this->load->model('General_model');
        $this->db->db_debug = false;
        if (!$this->session->userdata('usuarios_app_token')) $this->session->set_userdata('usuarios_app_token', bin2hex(random_bytes(32)));
    }

    public function index()
    {
        $m = $this->App_usuarios_model;
        if (!$m->disponible()) { show_error('El módulo aún no está disponible: faltan las tablas de usuarios del app.', 503); exit; }
        $vista = $this->input->get('vista');
        $vista = is_string($vista) && isset(App_usuarios_model::VISTAS[$vista]) ? $vista : 'todos';
        $todos = $m->listar();
        $conteos = array('todos' => count($todos), 'activos' => 0, 'inactivos' => 0, 'alertas' => 0);
        foreach ($todos as $u) { $conteos[$u->is_active ? 'activos' : 'inactivos']++; if ($u->alertas) $conteos['alertas']++; }
        $usuarios = array_values(array_filter($todos, function ($u) use ($vista) {
            return $vista === 'todos' || ($vista === 'activos' && $u->is_active) || ($vista === 'inactivos' && !$u->is_active) || ($vista === 'alertas' && $u->alertas);
        }));
        $this->layout->view('usuarios_app/index', array(
            'menu_tables' => $this->General_model->get_tables_active(), 'usuarios' => $usuarios, 'conteos' => $conteos, 'vista' => $vista,
            'areas' => $m->areas(), 'candidatos' => array('SUPERVISOR' => $m->candidatos('SUPERVISOR'), 'AREA_USER' => $m->candidatos('AREA_USER')),
            'token' => $this->session->userdata('usuarios_app_token'),
        ));
    }

    // Crea (id = 0) o edita (id > 0) un usuario. Responde JSON; la contraseña de un usuario nuevo se devuelve una sola vez.
    public function guardar()
    {
        $json = function ($datos, $codigo = 200) {
            $this->output->set_status_header($codigo)->set_content_type('application/json', 'utf-8')
                ->set_header('Cache-Control: no-store')->set_output(json_encode($datos, JSON_UNESCAPED_UNICODE));
        };
        if ($this->input->method() !== 'post') { $json(array('error' => 'Método no permitido.'), 405); return; }
        $token = $this->input->post('token');
        if (!is_string($token) || !hash_equals((string) $this->session->userdata('usuarios_app_token'), $token)) { $json(array('error' => 'La sesión del formulario venció. Actualice la página.'), 403); return; }
        $m = $this->App_usuarios_model;
        $texto = function ($campo) { $v = $this->input->post($campo); return is_string($v) ? trim($v) : ''; };
        $id = $texto('id');
        if ($id === '' || !ctype_digit($id)) { $json(array('error' => 'Usuario inválido.'), 400); return; }
        $id = (int) $id;
        $errores = array();

        if ($id > 0) {
            $actual = $m->obtener($id);
            if (!$actual || !in_array($actual->rol, App_usuarios_model::CREABLES, true)) { $json(array('error' => 'El usuario no existe o no se puede editar aquí.'), 404); return; }
            $rol = $actual->rol; $excluir = $id;
        } else {
            $rol = $texto('rol'); $excluir = 0;
            if (!in_array($rol, App_usuarios_model::CREABLES, true)) $errores['rol'] = 'Elija un rol válido.';
        }

        $nombre = $texto('nombre'); $email = mb_strtolower($texto('email'));
        if ($id === 0) {
            if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) $errores['nombre'] = 'Escriba el nombre completo (entre 2 y 100 caracteres).';
            if ($email === '' || mb_strlen($email) > 150 || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errores['email'] = 'Escriba un correo válido (hasta 150 caracteres).';
            elseif ($m->correo_existe($email)) $errores['email'] = 'Ya existe un usuario del app con este correo.';
        }

        // Área: el supervisor siempre va en OPE; el usuario de área, en cualquier otra área activa.
        $area_id = null;
        if (isset($rol) && in_array($rol, App_usuarios_model::CREABLES, true)) {
            if ($rol === 'SUPERVISOR') {
                $area = $m->area_supervisor();
                if (!$area) $errores['area'] = 'No existe el área OPE activa; no se puede asignar a un supervisor.'; else $area_id = (int) $area->id;
            } else {
                $pedida = $texto('area_id');
                $validas = array(); foreach ($m->areas() as $a) { if ($a->code !== App_usuarios_model::AREA_SUPERVISOR) $validas[(int) $a->id] = true; }
                if ($pedida === '' || !ctype_digit($pedida) || !isset($validas[(int) $pedida])) $errores['area'] = 'Elija el área del usuario (no puede ser Operaciones).';
                else $area_id = (int) $pedida;
            }
            $legacy = $texto('legacy_id'); $legacy_id = null;
            if ($legacy !== '') {
                if (!ctype_digit($legacy) || !$m->candidato_valido((int) $legacy, $rol, $excluir)) $errores['legacy_id'] = $rol === 'SUPERVISOR' ? 'Elija un supervisor activo de la plataforma que no esté vinculado a otro usuario.' : 'Elija un usuario activo de la plataforma que no esté vinculado a otro usuario.';
                else $legacy_id = (int) $legacy;
            } elseif ($rol === 'SUPERVISOR') $errores['legacy_id'] = 'El supervisor debe estar vinculado a su usuario de la plataforma para ver sus clientes.';
        }
        if ($errores) { $json(array('errores' => $errores), 422); return; }

        if ($id > 0) {
            $r = $m->actualizar($id, array('activo' => $texto('activo') === '1', 'area_id' => $area_id, 'legacy_id' => $legacy_id));
            if (isset($r['error'])) { $json(array('errores' => $r['error'] === 'vinculo' ? array('legacy_id' => 'Ese usuario de la plataforma ya está vinculado a otro usuario del app.') : array(), 'error' => 'No se pudo guardar el usuario.'), $r['error'] === 'vinculo' ? 422 : 500); return; }
            $json(array('ok' => true, 'id' => $id));
            return;
        }
        $clave = App_usuarios_model::generar_clave();
        $r = $m->crear(array('rol' => $rol, 'area_id' => $area_id, 'legacy_id' => $legacy_id, 'nombre' => $nombre, 'email' => $email, 'hash' => App_usuarios_model::hash_clave($clave)));
        if (isset($r['error'])) {
            $errores = $r['error'] === 'correo' ? array('email' => 'Ya existe un usuario del app con este correo.') : ($r['error'] === 'vinculo' ? array('legacy_id' => 'Ese usuario de la plataforma ya está vinculado a otro usuario del app.') : array());
            $json(array('errores' => $errores, 'error' => $errores ? null : 'No se pudo crear el usuario.'), $errores ? 422 : 500);
            return;
        }
        $json(array('ok' => true, 'id' => $r['id'], 'email' => $email, 'clave' => $clave), 201);
    }
}
