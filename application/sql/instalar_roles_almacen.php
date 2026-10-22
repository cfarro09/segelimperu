<?php
// CLI only. Copies the existing encrypted credential without decrypting or displaying it.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', dirname(__DIR__, 2).'/system/');
define('APPPATH', dirname(__DIR__).'/');
require APPPATH.'config/database.php';
require APPPATH.'libraries/Almacen_acceso.php';
$opciones = getopt('', array('crear-genericos', 'clave-de:'));
$crear = isset($opciones['crear-genericos']);
$referencia = $opciones['clave-de'] ?? '';
if ($crear && (!is_string($referencia) || !filter_var($referencia, FILTER_VALIDATE_EMAIL))) {
    fwrite(STDERR, "Indica --clave-de=correo para crear las cuentas genéricas.\n"); exit(1);
}
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $cfg = $db[$active_group];
    $cn = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database']);
    $cn->set_charset('utf8mb4');
    $cn->begin_transaction();
    $roles = Almacen_acceso::roles();
    $buscar_rol = $cn->prepare('SELECT id_work, name_work FROM Type_working WHERE work_char = ? FOR UPDATE');
    $insertar_rol = $cn->prepare('INSERT INTO Type_working (work_char, name_work) VALUES (?, ?)');
    foreach ($roles as $codigo => $nombre) {
        $buscar_rol->bind_param('s', $codigo); $buscar_rol->execute();
        $actual = $buscar_rol->get_result()->fetch_assoc();
        if ($actual && mb_strtolower($actual['name_work']) !== mb_strtolower($nombre)) {
            throw new RuntimeException('El código '.$codigo.' pertenece a otro rol.');
        }
        if (!$actual) {
            $insertar_rol->bind_param('ss', $codigo, $nombre); $insertar_rol->execute();
        }
    }
    $resultados = array();
    if ($crear) {
        $buscar = $cn->prepare('SELECT id_tipo_doc, password FROM Usuarios WHERE email_usu = ? FOR UPDATE');
        $buscar->bind_param('s', $referencia); $buscar->execute();
        $referencias = $buscar->get_result();
        if ($referencias->num_rows !== 1) throw new RuntimeException('La cuenta de referencia debe existir y ser única.');
        $origen = $referencias->fetch_assoc();
        if (!$origen['password']) throw new RuntimeException('La cuenta de referencia no tiene una clave configurada.');
        $buscar_usuario = $cn->prepare('SELECT id_usu, tipo_usu FROM Usuarios WHERE email_usu = ? FOR UPDATE');
        $insertar_usuario = $cn->prepare("INSERT INTO Usuarios (nombre_usu, apellido_pat, apellido_mat, id_tipo_doc, nro_documento, tipo_usu, password, email_usu, intentos_usu, estado_usu) VALUES (?, 'Segelim', 'General', ?, ?, ?, ?, ?, 0, 'AC')");
        foreach ($roles as $codigo => $nombre) {
            $correo = ($codigo === 'SP' ? 'supervisor' : 'almacen').'@segelimperu.test';
            $buscar_usuario->bind_param('s', $correo); $buscar_usuario->execute();
            $existentes = $buscar_usuario->get_result();
            if ($existentes->num_rows > 1) throw new RuntimeException('El correo genérico está duplicado.');
            $existente = $existentes->fetch_assoc();
            if ($existente && $existente['tipo_usu'] !== $codigo) throw new RuntimeException('El correo genérico pertenece a otro rol.');
            if (!$existente) {
                $documento = $codigo === 'SP' ? 'GEN-SUPERVISOR' : 'GEN-ALMACEN';
                $insertar_usuario->bind_param('sissss', $nombre, $origen['id_tipo_doc'], $documento, $codigo, $origen['password'], $correo);
                $insertar_usuario->execute();
            }
            $resultados[] = $correo.' · '.$nombre.' · '.($existente ? 'ya existente, sin cambios' : 'creado y activo');
        }
    }
    $cn->commit();
    echo "Roles Supervisor (SP) y Almacén (AL) disponibles.\n";
    foreach ($resultados as $resultado) echo $resultado.PHP_EOL;
} catch (Throwable $e) {
    if (isset($cn)) $cn->rollback();
    fwrite(STDERR, 'No se pudo completar la instalación: '.$e->getMessage().PHP_EOL);
    exit(1);
}
