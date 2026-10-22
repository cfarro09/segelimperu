<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
if (!defined('BASEPATH')) define('BASEPATH', dirname(__DIR__,2).'/system/');
require dirname(__DIR__).'/config/database.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $cfg=$db[$active_group];
    $cn=new mysqli($cfg['hostname'],$cfg['username'],$cfg['password'],$cfg['database']);
    $cn->set_charset('utf8mb4');
    foreach (explode(';',file_get_contents(__DIR__.'/20261003_almacen_despachos.sql')) as $sql) if (trim($sql)) $cn->query($sql);
    echo 'Tablas de despachos y guías de salida listas.'.PHP_EOL;
} catch (Throwable $e) {
    fwrite(STDERR,'No se pudieron instalar las tablas de despacho.'.PHP_EOL); exit(1);
}
