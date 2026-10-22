<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', dirname(__DIR__,2).'/system/');
require dirname(__DIR__).'/config/database.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $cfg=$db[$active_group]; $cn=new mysqli($cfg['hostname'],$cfg['username'],$cfg['password'],$cfg['database']); $cn->set_charset('utf8mb4');
    foreach(explode(';',file_get_contents(__DIR__.'/20261003_almacen_clientes_asignaciones.sql')) as $sql) if(trim($sql)) $cn->query($sql);
    $clientes=array(
        array('CLI-0001','IPESA PIURA','Grupo 2','400.00'),
        array('CLI-0002','IPESA CAMIONES PIURA','Grupo 2','150.00'),
        array('CLI-0003','IPESA CHICLAYO','Grupo 4','350.00'),
        array('CLI-0004','CGM RENTAL CHICLAYO','Grupo 4','120.00')
    );
    $cn->begin_transaction(); $cantidad=0;
    $find=$cn->prepare('SELECT id FROM almacen_clientes WHERE codigo = ? OR nombre = ?');
    $insert=$cn->prepare('INSERT INTO almacen_clientes (codigo,nombre,grupo,presupuesto) VALUES (?,?,?,?)');
    foreach($clientes as $c) {
        $find->bind_param('ss',$c[0],$c[1]); $find->execute(); if($find->get_result()->num_rows) continue;
        $insert->bind_param('ssss',$c[0],$c[1],$c[2],$c[3]); $insert->execute(); $cantidad++;
    }
    $cn->commit(); echo 'Tablas de clientes, asignaciones y detalles listas. Clientes nuevos del Excel: '.$cantidad.PHP_EOL;
    echo 'No se importaron asignaciones históricas: la plantilla mezcla julio, agosto y septiembre.'.PHP_EOL;
    require __DIR__.'/instalar_flujo_asignaciones.php';
    require __DIR__.'/instalar_compras.php';
    require __DIR__.'/instalar_despachos.php';
} catch(Throwable $e) { if(isset($cn)) $cn->rollback(); fwrite(STDERR,'Instalación fallida: '.$e->getMessage().PHP_EOL); exit(1); }
