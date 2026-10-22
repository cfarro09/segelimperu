<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if (!defined('BASEPATH')) define('BASEPATH', dirname(__DIR__,2).'/system/');
require dirname(__DIR__).'/config/database.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $cfg=$db[$active_group]; $cn=new mysqli($cfg['hostname'],$cfg['username'],$cfg['password'],$cfg['database']); $cn->set_charset('utf8mb4');
    $columnas=array(
        'estado'=>"ENUM('borrador','publicado','aprobado','rechazado') NOT NULL DEFAULT 'borrador'",
        'publicado_por'=>'INT UNSIGNED NULL', 'publicado_en'=>'DATETIME NULL',
        'revisado_por'=>'INT UNSIGNED NULL', 'revisado_en'=>'DATETIME NULL',
        'comentario_revision'=>"VARCHAR(1000) NOT NULL DEFAULT ''"
    );
    foreach($columnas as $nombre=>$definicion) {
        if(!$cn->query("SHOW COLUMNS FROM almacen_asignaciones LIKE '".$nombre."'")->num_rows) $cn->query('ALTER TABLE almacen_asignaciones ADD COLUMN '.$nombre.' '.$definicion);
    }
    $cn->query("CREATE TABLE IF NOT EXISTS almacen_asignacion_eventos (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT, asignacion_id INT UNSIGNED NOT NULL,
        estado_anterior VARCHAR(20) NOT NULL, estado_nuevo VARCHAR(20) NOT NULL,
        usuario_id INT UNSIGNED NOT NULL, comentario VARCHAR(1000) NOT NULL DEFAULT '',
        creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY(id), KEY idx_almacen_evento_asignacion(asignacion_id,id),
        CONSTRAINT fk_almacen_evento_asignacion FOREIGN KEY(asignacion_id) REFERENCES almacen_asignaciones(id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo 'Flujo de borrador, publicación y revisión instalado. Registros y estados previos conservados.'.PHP_EOL;
} catch(Throwable $e) { fwrite(STDERR,'No se pudo instalar el flujo: '.$e->getMessage().PHP_EOL); exit(1); }
