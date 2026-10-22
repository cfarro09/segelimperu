<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Usuarios del aplicativo móvil (tabla app_users, propiedad del API). El API no tiene endpoint para crearlos, así que
// la web inserta con el mismo formato que su seed: correo en minúsculas, bcrypt de costo 10 y fechas en UTC.
// Reglas del app: un supervisor debe estar en el área OPE y vinculado a un usuario SP activo de la plataforma
// (así ve sus clientes); un usuario de área nunca debe estar en OPE (la app lo trataría como supervisor).
class App_usuarios_model extends CI_Model
{
    const ROLES = array('ADMIN' => 'Administrador', 'SUPERVISOR' => 'Supervisor', 'AREA_USER' => 'Usuario de área');
    const CREABLES = array('SUPERVISOR', 'AREA_USER');
    const AREA_SUPERVISOR = 'OPE';
    const VISTAS = array('todos' => 'Todos', 'activos' => 'Activos', 'inactivos' => 'Inactivos', 'alertas' => 'Con alertas');
    const BCRYPT_COSTO = 10;

    public function __construct() { parent::__construct(); $this->load->database(); }

    public function disponible()
    {
        foreach (array('app_users', 'app_roles', 'app_areas', 'app_refresh_sessions', 'Usuarios') as $tabla) {
            if (!$this->db->table_exists($tabla)) return false;
        }
        return true;
    }

    public function areas()
    {
        return $this->db->select('id, code, spanish_name')->where('is_active', 1)->order_by('spanish_name')->get('app_areas')->result();
    }

    public function area_supervisor()
    {
        return $this->db->select('id, code, spanish_name')->where('code', self::AREA_SUPERVISOR)->where('is_active', 1)->get('app_areas')->row();
    }

    public function listar()
    {
        $sql = "SELECT u.id, u.fullname, u.email, r.name AS rol, u.area_id, a.code AS area_codigo, a.spanish_name AS area, u.is_active, u.created_at,
                       u.legacy_user_id, TRIM(CONCAT(COALESCE(l.nombre_usu,''), ' ', COALESCE(l.apellido_pat,''))) AS legacy_nombre, l.tipo_usu AS legacy_tipo, l.estado_usu AS legacy_estado,
                       (SELECT MAX(s.last_used_at) FROM app_refresh_sessions s WHERE s.user_id = u.id) AS ultima,
                       (SELECT COUNT(*) FROM app_refresh_sessions s WHERE s.user_id = u.id AND s.status = 'ACTIVE' AND s.fcm_token IS NOT NULL AND s.fcm_token <> '') AS con_push
                  FROM app_users u
                  JOIN app_roles r ON r.id = u.role_id
                  LEFT JOIN app_areas a ON a.id = u.area_id
                  LEFT JOIN Usuarios l ON l.id_usu = u.legacy_user_id
                 ORDER BY u.is_active DESC, u.fullname, u.id";
        $usuarios = $this->db->query($sql)->result();
        foreach ($usuarios as $u) {
            $u->is_active = (int) $u->is_active === 1;
            $u->con_push = (int) $u->con_push > 0;
            $u->legacy_nombre = trim((string) $u->legacy_nombre);
            $u->alertas = $this->alertas($u);
        }
        return $usuarios;
    }

    // Problemas que hacen que el usuario no funcione como se espera en la app móvil (solo para usuarios activos).
    public function alertas($u)
    {
        $a = array();
        if (!$u->is_active) return $a;
        if ($u->rol === 'SUPERVISOR') {
            if ($u->area_codigo !== self::AREA_SUPERVISOR) $a[] = 'Sin el área OPE: la app lo trata como usuario de área y no como supervisor.';
            if ($u->legacy_user_id === null) $a[] = 'Sin usuario de la plataforma vinculado: no verá sus clientes.';
            elseif ($u->legacy_tipo !== 'SP' || $u->legacy_estado !== 'AC') $a[] = 'Su usuario de la plataforma no es un supervisor activo: no verá sus clientes.';
        } elseif ($u->rol === 'AREA_USER') {
            if ($u->area_id === null) $a[] = 'Sin área: no verá ninguna tarea.';
            elseif ($u->area_codigo === self::AREA_SUPERVISOR) $a[] = 'En el área OPE la app lo trata como supervisor.';
        }
        return $a;
    }

    public function obtener($id)
    {
        return $this->db->query("SELECT u.id, u.is_active, u.area_id, u.legacy_user_id, r.name AS rol FROM app_users u JOIN app_roles r ON r.id = u.role_id WHERE u.id = ?", array((int) $id))->row();
    }

    // Usuarios de la plataforma que se pueden vincular: activos y no vinculados a otro usuario del app.
    // Un supervisor solo puede vincularse a un tipo SP; un usuario de área a cualquier otro tipo.
    public function candidatos($rol, $excluir_usuario = 0)
    {
        $this->db->select("u.id_usu AS id, TRIM(CONCAT(COALESCE(u.nombre_usu,''), ' ', COALESCE(u.apellido_pat,''))) AS nombre, u.email_usu AS email, u.tipo_usu AS tipo", false)
            ->from('Usuarios u')->where('u.estado_usu', 'AC');
        if ($rol === 'SUPERVISOR') $this->db->where('u.tipo_usu', 'SP'); else $this->db->where('u.tipo_usu <>', 'SP');
        $this->db->where('NOT EXISTS (SELECT 1 FROM app_users x WHERE x.legacy_user_id = u.id_usu AND x.id <> ' . (int) $excluir_usuario . ')', null, false);
        return $this->db->order_by('nombre')->order_by('u.id_usu')->get()->result();
    }

    public function candidato_valido($legacy_id, $rol, $excluir_usuario = 0)
    {
        foreach ($this->candidatos($rol, $excluir_usuario) as $c) { if ((int) $c->id === (int) $legacy_id) return true; }
        return false;
    }

    public function correo_existe($email) { return $this->db->where('email', $email)->count_all_results('app_users') > 0; }

    // Contraseña legible sin caracteres ambiguos (sin 0/O, 1/l/I) con al menos una mayúscula, una minúscula y un número.
    public static function generar_clave($largo = 12)
    {
        $grupos = array('ABCDEFGHJKLMNPQRSTUVWXYZ', 'abcdefghijkmnopqrstuvwxyz', '23456789');
        $todo = implode('', $grupos);
        do {
            $clave = '';
            for ($i = 0; $i < $largo; $i++) $clave .= $todo[random_int(0, strlen($todo) - 1)];
            $ok = true;
            foreach ($grupos as $g) { if (strpbrk($clave, $g) === false) $ok = false; }
        } while (!$ok);
        return $clave;
    }

    // bcrypt de costo 10 como el API. PHP emite el prefijo $2y$; se normaliza a $2b$, que la librería bcrypt de Node reconoce siempre.
    public static function hash_clave($clave)
    {
        $hash = password_hash($clave, PASSWORD_BCRYPT, array('cost' => self::BCRYPT_COSTO));
        return strncmp($hash, '$2y$', 4) === 0 ? '$2b$' . substr($hash, 4) : $hash;
    }

    // Devuelve array('id' => n) o array('error' => 'correo'|'vinculo'|'bd').
    public function crear($d)
    {
        $rol = $this->db->get_where('app_roles', array('name' => $d['rol']))->row();
        if (!$rol) return array('error' => 'bd');
        $ok = $this->db->query("INSERT INTO app_users (role_id, area_id, legacy_user_id, fullname, email, password_hash, is_active, created_at, updated_at)
                                VALUES (?, ?, ?, ?, ?, ?, 1, UTC_TIMESTAMP(3), UTC_TIMESTAMP(3))",
            array((int) $rol->id, $d['area_id'], $d['legacy_id'], $d['nombre'], $d['email'], $d['hash']));
        return $ok ? array('id' => (int) $this->db->insert_id()) : array('error' => $this->error_clave());
    }

    public function actualizar($id, $d)
    {
        $ok = $this->db->query("UPDATE app_users SET is_active = ?, area_id = ?, legacy_user_id = ?, updated_at = UTC_TIMESTAMP(3) WHERE id = ?",
            array($d['activo'] ? 1 : 0, $d['area_id'], $d['legacy_id'], (int) $id));
        return $ok ? array('id' => (int) $id) : array('error' => $this->error_clave());
    }

    private function error_clave()
    {
        $e = $this->db->error();
        if ((int) $e['code'] === 1062) return stripos($e['message'], 'legacy_user_id') !== false ? 'vinculo' : 'correo';
        return 'bd';
    }
}
