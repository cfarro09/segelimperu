<?php
// Only executable from CLI; credentials remain in the existing application configuration.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', dirname(__DIR__, 2).'/system/');
require dirname(__DIR__).'/config/database.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $cfg = $db[$active_group];
    $cn = new mysqli($cfg['hostname'], $cfg['username'], $cfg['password'], $cfg['database']);
    $cn->set_charset('utf8mb4');
    $sql = file_get_contents(__DIR__.'/20261003_almacen_productos.sql');
    foreach (explode(';', $sql) as $statement) if (trim($statement)) $cn->query($statement);
    $rows = require dirname(__DIR__).'/data/almacen_productos_iniciales.php';
    if (!is_array($rows)) throw new RuntimeException('Catálogo inicial inválido.');
    $cn->begin_transaction();
    $find = $cn->prepare('SELECT id FROM almacen_productos WHERE codigo = ?');
    $insert = $cn->prepare('INSERT INTO almacen_productos (codigo, descripcion, categoria, marca, unidad, precio_valorado, precio_real) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $count = 0;
    foreach ($rows as $p) {
        $find->bind_param('s', $p['codigo']); $find->execute();
        if ($find->get_result()->num_rows) continue;
        $insert->bind_param('sssssss', $p['codigo'], $p['descripcion'], $p['categoria'], $p['marca'], $p['unidad'], $p['precio_valorado'], $p['precio_valorado']);
        $insert->execute(); $count++;
    }
    $cn->query('UPDATE almacen_productos SET precio_real = precio_valorado WHERE precio_real IS NULL');
    $actualizados = $cn->affected_rows;
    $cn->commit();
    echo 'Tablas de productos y movimientos listas. Productos nuevos: '.$count.PHP_EOL;
    echo 'Precios reales completados con el precio valorado: '.$actualizados.PHP_EOL;
    echo 'El stock requiere conteo físico.'.PHP_EOL;
} catch (Throwable $e) {
    if (isset($cn)) $cn->rollback();
    fwrite(STDERR, 'No se pudo instalar el módulo: '.$e->getMessage().PHP_EOL);
    exit(1);
}
