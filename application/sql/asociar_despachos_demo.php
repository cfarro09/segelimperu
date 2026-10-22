<?php
// Associate only the two fully delivered fixtures created by cargar_demo_almacen.php.
// Existing movements are reused. No product stock or movement is inserted/updated.
if (PHP_SAPI!=='cli') { http_response_code(404); exit; }
define('BASEPATH',dirname(__DIR__,2).'/system/');
require dirname(__DIR__).'/config/database.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $cfg=$db[$active_group]; $cn=new mysqli($cfg['hostname'],$cfg['username'],$cfg['password'],$cfg['database']); $cn->set_charset('utf8mb4');
    $cn->begin_transaction();
    $q=$cn->prepare("SELECT a.*,c.codigo,c.nombre,c.ruc,c.direccion FROM almacen_asignaciones a JOIN almacen_clientes c ON c.id=a.cliente_id WHERE a.estado='aprobado' AND a.observaciones='DEMO: Entrega completa registrada en movimientos de inventario.' AND c.codigo REGEXP '^DEMO-C[0-9]{2}$' ORDER BY a.id FOR UPDATE");
    $q->execute(); $asignaciones=$q->get_result()->fetch_all(MYSQLI_ASSOC); $creados=0;
    foreach ($asignaciones as $a) {
        $id=(int)$a['id'];
        $q=$cn->prepare('SELECT id FROM almacen_despachos WHERE asignacion_id=?'); $q->bind_param('i',$id); $q->execute();
        if ($q->get_result()->num_rows) continue;
        $q=$cn->prepare('SELECT d.*,p.codigo FROM almacen_asignacion_detalles d JOIN almacen_productos p ON p.id=d.producto_id WHERE d.asignacion_id=? ORDER BY d.producto_id'); $q->bind_param('i',$id); $q->execute();
        $detalles=$q->get_result()->fetch_all(MYSQLI_ASSOC); $movs=array(); $fecha='';
        foreach ($detalles as $d) {
            $pid=(int)$d['producto_id']; $motivo='DEMO: entrega '.$a['periodo'].' a '.$a['nombre'].' · Asignación #'.$id;
            $q=$cn->prepare("SELECT * FROM almacen_movimientos WHERE producto_id=? AND tipo='salida' AND motivo=?"); $q->bind_param('is',$pid,$motivo); $q->execute(); $rows=$q->get_result()->fetch_all(MYSQLI_ASSOC);
            if (count($rows)!==1 || (int)round(-$rows[0]['cantidad']*1000)!==(int)round($d['cantidad']*1000) || (int)round(($rows[0]['stock_anterior']-$rows[0]['stock_nuevo'])*1000)!==(int)round($d['cantidad']*1000)) throw new RuntimeException('La entrega DEMO no coincide completamente.');
            $movs[$pid]=$rows[0]; $fecha=max($fecha,$rows[0]['creado_en']);
        }
        if (!$detalles) throw new RuntimeException('Entrega DEMO vacía.');
        $cliente=(int)$a['cliente_id']; $usuario=(int)reset($movs)['usuario_id'];
        $q=$cn->prepare("INSERT INTO almacen_despachos (asignacion_id,cliente_id,cliente_codigo,cliente_nombre,cliente_ruc,cliente_direccion,periodo,usuario_id,creado_en,origen) VALUES (?,?,?,?,?,?,?,?,?,'demo_historico')");
        $q->bind_param('iisssssis',$id,$cliente,$a['codigo'],$a['nombre'],$a['ruc'],$a['direccion'],$a['periodo'],$usuario,$fecha); $q->execute(); $sid=$cn->insert_id;
        foreach ($detalles as $d) {
            $pid=(int)$d['producto_id']; $m=$movs[$pid]; $mid=(int)$m['id'];
            $q=$cn->prepare('INSERT INTO almacen_despacho_detalles (despacho_id,producto_id,movimiento_id,codigo,descripcion,unidad,marca,cantidad,stock_anterior,stock_nuevo) VALUES (?,?,?,?,?,?,?,?,?,?)');
            $q->bind_param('iiisssssss',$sid,$pid,$mid,$d['codigo'],$d['descripcion'],$d['unidad'],$d['marca'],$d['cantidad'],$m['stock_anterior'],$m['stock_nuevo']); $q->execute();
        }
        $creados++;
    }
    $cn->commit(); echo $creados.' entregas DEMO asociadas a sus movimientos existentes; stock sin cambios.'.PHP_EOL;
} catch (Throwable $e) {
    if (isset($cn)) $cn->rollback(); fwrite(STDERR,'No se asociaron entregas DEMO: '.$e->getMessage().PHP_EOL); exit(1);
}
