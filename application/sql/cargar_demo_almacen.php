<?php
// Uses the production models. CLI only; --verificar runs the full cycle and rolls it back.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
define('BASEPATH', dirname(__DIR__, 2).'/system/');
define('APPPATH', dirname(__DIR__).'/');
define('ENVIRONMENT', 'testing');
date_default_timezone_set('America/Lima');
function log_message($nivel, $mensaje) {}
function is_php($version) { return version_compare(PHP_VERSION, $version, '>='); }
function show_error($mensaje, $codigo=500) { throw new RuntimeException('Error de conexión con la base de datos.'); }
class CI_Model {
    public $db, $load;
    public function __construct() {
        $this->db = $GLOBALS['connection'];
        $this->load = new class { public function database() {} };
    }
}
require APPPATH.'config/database.php';
require BASEPATH.'database/DB.php';
foreach (array('Almacen_model','Almacen_clientes_model','Almacen_asignaciones_model','Almacen_compras_model') as $modelo) require APPPATH.'models/'.$modelo.'.php';
function demo_ok($valor, $mensaje) {
    if (!$valor) throw new RuntimeException($mensaje);
}
function demo_resultado($resultado, $mensaje) {
    demo_ok($resultado === true || (is_array($resultado) && isset($resultado['id'])), $mensaje);
    return is_array($resultado) ? (int)$resultado['id'] : true;
}
function demo_huella_real($db) {
    $consultas = array(
        'productos' => "SELECT * FROM almacen_productos WHERE codigo NOT LIKE 'DEMO-%' ORDER BY id",
        'clientes' => "SELECT * FROM almacen_clientes WHERE codigo NOT LIKE 'DEMO-%' ORDER BY id",
        'movimientos' => "SELECT m.* FROM almacen_movimientos m JOIN almacen_productos p ON p.id=m.producto_id WHERE p.codigo NOT LIKE 'DEMO-%' ORDER BY m.id",
        'asignaciones' => "SELECT a.* FROM almacen_asignaciones a JOIN almacen_clientes c ON c.id=a.cliente_id WHERE c.codigo NOT LIKE 'DEMO-%' ORDER BY a.id",
        'detalles' => "SELECT d.* FROM almacen_asignacion_detalles d JOIN almacen_asignaciones a ON a.id=d.asignacion_id JOIN almacen_clientes c ON c.id=a.cliente_id WHERE c.codigo NOT LIKE 'DEMO-%' ORDER BY d.id",
        'eventos' => "SELECT e.* FROM almacen_asignacion_eventos e JOIN almacen_asignaciones a ON a.id=e.asignacion_id JOIN almacen_clientes c ON c.id=a.cliente_id WHERE c.codigo NOT LIKE 'DEMO-%' ORDER BY e.id",
        'compras' => "SELECT * FROM almacen_compras WHERE observaciones NOT LIKE 'DEMO:%' ORDER BY id",
        'compra_detalles' => "SELECT d.* FROM almacen_compra_detalles d JOIN almacen_compras o ON o.id=d.compra_id WHERE o.observaciones NOT LIKE 'DEMO:%' ORDER BY d.id",
        'compra_asignaciones' => "SELECT l.* FROM almacen_compra_asignaciones l JOIN almacen_compras o ON o.id=l.compra_id WHERE o.observaciones NOT LIKE 'DEMO:%' ORDER BY l.compra_id,l.asignacion_id"
    );
    $huellas = array();
    foreach ($consultas as $nombre=>$sql) {
        $q = $db->query($sql); demo_ok($q !== false, 'No se pudo comprobar '.$nombre.'.');
        $huellas[$nombre] = hash('sha256', json_encode($q->result_array()));
    }
    return $huellas;
}
function demo_publicar($asignaciones, $cliente, $periodo, $lineas, $supervisor, $almacen, $estado, $nota) {
    $id = demo_resultado($asignaciones->guardar(0, 0, $cliente->id, $periodo, $cliente->presupuesto, $lineas, 'DEMO: '.$nota, $supervisor), 'No se pudo crear el borrador de demostración.');
    if ($estado !== 'borrador') {
        demo_resultado($asignaciones->guardar($id, 1, $cliente->id, $periodo, $cliente->presupuesto, $lineas, 'DEMO: '.$nota, $supervisor, true), 'No se pudo enviar la asignación de demostración.');
        if (in_array($estado, array('aprobado','rechazado'), true)) {
            $comentario = 'DEMO: cantidades aprobadas.';
            if ($estado === 'rechazado') $comentario = $cliente->codigo === 'DEMO-C07'
                ? 'DEMO: reducir bolsas de 12 a 3 PAQ.'
                : 'DEMO: reducir cantidades; la solicitud supera el presupuesto de S/ 150.00.';
            demo_resultado($asignaciones->revisar($id, 2, $estado, $comentario, $almacen), 'No se pudo revisar la asignación de demostración.');
        }
    }
    return $id;
}
function demo_compra_exclusiva($compras, $id) {
    foreach ($compras->asignaciones($id) as $a) demo_ok(strpos($a->nombre, 'DEMO · ') === 0, 'La orden incluiría asignaciones reales.');
    foreach ($compras->detalles($id) as $d) demo_ok(strpos($d->descripcion, 'DEMO · ') === 0, 'La orden incluiría productos reales.');
}
$verificar = in_array('--verificar', $argv, true);
$periodo = date('Y-m');
$anterior = date('Y-m', strtotime(date('Y-m-01').' -1 month'));
$transaccion = false;
try {
    $cfg = $db[$active_group]; $cfg['db_debug'] = false;
    $connection = DB($cfg, true);
    $lock = $connection->query("SELECT GET_LOCK('almacen-carga-demo', 5) AS tomado");
    demo_ok($lock && $lock->row()->tomado, 'Otra carga de demostración está en curso.');
    if ($connection->like('codigo', 'DEMO-', 'after')->count_all_results('almacen_productos')
        || $connection->like('codigo', 'DEMO-', 'after')->count_all_results('almacen_clientes')) {
        echo "Ya hay datos DEMO. Se conservan tus cambios; no se duplicó ni modificó ningún registro.\n";
        $connection->query("SELECT RELEASE_LOCK('almacen-carga-demo')"); exit;
    }
    $usuarios = $connection->where_in('email_usu', array('supervisor@segelimperu.test','almacen@segelimperu.test'))->get('Usuarios')->result();
    $roles = array();
    foreach ($usuarios as $u) if ($u->estado_usu === 'AC') $roles[$u->tipo_usu] = (int)$u->id_usu;
    demo_ok(isset($roles['SP'], $roles['AL']), 'Primero crea y habilita las cuentas genéricas Supervisor y Almacén.');
    $supervisor = $roles['SP']; $almacen = $roles['AL'];
    $inventario = new Almacen_model(); $clientes = new Almacen_clientes_model();
    $asignaciones = new Almacen_asignaciones_model(); $compras = new Almacen_compras_model();
    demo_ok($connection->trans_begin(), 'No se pudo iniciar la transacción.'); $transaccion = true;
    $huella = demo_huella_real($connection);
    foreach (array($anterior, $periodo) as $mes) {
        demo_ok(!$compras->del_mes($mes), 'Ya existe una orden para '.$mes.'. No se mezclaron datos reales con la demostración.');
        demo_ok($asignaciones->total(0, $mes, 'aprobado') === 0, 'Hay asignaciones aprobadas en '.$mes.'. La demostración no las modificará.');
    }
    // Description, category, unit, purchase price, valuation price, initial physical stock.
    $catalogo = array(
        1=>array('Detergente neutro','MATERIALES','LTR','6.50','9.50','2'),
        2=>array('Lejía','MATERIALES','LTR','3.20','4.50','3'),
        3=>array('Desinfectante','MATERIALES','LTR','7.00','10.00','1'),
        4=>array('Limpiavidrios','MATERIALES','LTR','5.50','8.00','0'),
        5=>array('Bolsas negras','MATERIALES','PAQ','8.00','12.00','4'),
        6=>array('Paño de microfibra','MATERIALES','UND','2.50','4.00','6'),
        7=>array('Guantes de nitrilo','MATERIALES','PAR','3.50','5.00','2'),
        8=>array('Alcohol al 70%','MATERIALES','LTR','9.00','13.00','0.500'),
        9=>array('Escoba','IMPLEMENTOS','UND','10.00','15.00','1'),
        10=>array('Recogedor','IMPLEMENTOS','UND','7.00','11.00','0'),
        11=>array('Trapeador','IMPLEMENTOS','UND','12.00','18.00','2'),
        12=>array('Balde de 20 litros','IMPLEMENTOS','UND','14.00','20.00','1')
    );
    $productos = array();
    foreach ($catalogo as $numero=>$p) {
        $codigo = 'DEMO-P'.str_pad($numero, 2, '0', STR_PAD_LEFT);
        $data = array('codigo'=>$codigo,'descripcion'=>'DEMO · '.$p[0],'categoria'=>$p[1],'unidad'=>$p[2],'precio_real'=>$p[3],'precio_valorado'=>$p[4],'marca'=>'DEMO','activo'=>1);
        demo_resultado($inventario->guardar(0, $data, $p[5], $almacen), 'No se pudo crear '.$codigo.'.');
        $productos[$numero] = (int)$connection->get_where('almacen_productos', array('codigo'=>$codigo))->row()->id;
    }
    foreach (array(array(1,'entrada','4','Reposición inicial'),array(1,'salida','1.5','Entrega de limpieza'),array(1,'ajuste','4','Conteo físico'),array(5,'entrada','2','Ingreso de bolsas'),array(5,'salida','1','Entrega de bolsas')) as $m) {
        demo_resultado($inventario->mover($productos[$m[0]], $m[1], $m[2], 'DEMO: '.$m[3], $almacen), 'No se pudo registrar el movimiento DEMO.');
    }
    $sedes = array(
        1=>array('Oficina Norte','320.00','75.00'), 2=>array('Centro Comercial','700.00','0.00'),
        3=>array('Clínica Sur','100.00','-80.00'), 4=>array('Colegio Este','150.00','0.00'),
        5=>array('Sede Central','650.00','100.00'), 6=>array('Taller Oeste','500.00','50.00'),
        7=>array('Agencia Puerto','400.00','0.00'), 8=>array('Hotel Mirador','750.00','0.00')
    );
    $clientes_demo = array();
    foreach ($sedes as $numero=>$s) {
        $codigo = 'DEMO-C'.str_pad($numero, 2, '0', STR_PAD_LEFT);
        $data = array('codigo'=>$codigo,'nombre'=>'DEMO · '.$s[0],'ruc'=>'','grupo'=>'DEMO','supervisor'=>'Supervisor Segelim','contacto'=>'supervisor@segelimperu.test','direccion'=>'DEMO · Dirección de ejemplo','presupuesto'=>$s[1],'saldo_inicial'=>$s[2],'activo'=>1,'documento_baja'=>'');
        demo_resultado($clientes->guardar(0, $data), 'No se pudo crear '.$codigo.'.');
        $clientes_demo[$numero] = $connection->get_where('almacen_clientes', array('codigo'=>$codigo))->row();
    }
    $lineas = function($cantidades) use ($productos) {
        $resultado = array(); foreach ($cantidades as $numero=>$cantidad) $resultado[$productos[$numero]] = (string)$cantidad; return $resultado;
    };
    $historicas = array(
        5=>array(1=>8,2=>6,3=>3,4=>2,5=>6,6=>8,7=>6,8=>'1.5',9=>2,10=>2,11=>2,12=>1),
        6=>array(1=>4,2=>4,3=>2,5=>4,6=>6,7=>4,8=>1,9=>1,11=>1,12=>1)
    );
    $historicos_ids = array();
    foreach ($historicas as $numero=>$cantidades) $historicos_ids[$numero] = demo_publicar($asignaciones, $clientes_demo[$numero], $anterior, $lineas($cantidades), $supervisor, $almacen, 'aprobado', 'Entrega completa registrada en movimientos de inventario.');
    $recibida = demo_resultado($compras->consolidar($anterior, $almacen), 'No se pudo consolidar el ciclo recibido.');
    demo_compra_exclusiva($compras, $recibida);
    $cantidades = array(); foreach ($compras->detalles($recibida) as $d) $cantidades[$d->producto_id] = $d->cantidad;
    $cantidades[$productos[1]] = (string)((float)$cantidades[$productos[1]] + 2);
    $cantidades[$productos[6]] = (string)((float)$cantidades[$productos[6]] + 3);
    demo_resultado($compras->guardar($recibida, 1, $cantidades, 'DEMO · Proveedor de limpieza', 'DEMO: ciclo recibido y entregado.', $almacen, true), 'No se pudo registrar la recepción DEMO.');
    foreach ($historicas as $numero=>$cantidades) foreach ($cantidades as $producto=>$cantidad) {
        demo_resultado($inventario->mover($productos[$producto], 'salida', (string)$cantidad, 'DEMO: entrega '.$anterior.' a '.$clientes_demo[$numero]->nombre.' · Asignación #'.$historicos_ids[$numero], $almacen), 'No se pudo registrar la entrega DEMO.');
    }
    $escenarios = array(
        1=>array('borrador',array(1=>4,2=>2,3=>'2.5',5=>2,6=>3,7=>1,8=>'0.5',9=>1,11=>1)),
        2=>array('publicado',array(1=>8,2=>6,3=>4,4=>3,5=>5,6=>4,7=>2,8=>2,9=>2,10=>1,11=>2,12=>1)),
        3=>array('publicado',array(1=>10,2=>10,3=>6,4=>4,5=>6,6=>6,7=>4,8=>3,9=>2,10=>2,11=>3,12=>2)),
        4=>array('rechazado',array(1=>5,2=>3,3=>8,5=>10,6=>6,10=>2)),
        5=>array('aprobado',array(1=>8,2=>6,3=>3,4=>4,5=>5,6=>6,7=>2,8=>'1.5',9=>2,10=>1,11=>2,12=>1)),
        6=>array('aprobado',array(1=>5,2=>4,3=>3,4=>2,5=>3,6=>5,7=>2,8=>1,9=>1,10=>1,11=>1,12=>1)),
        7=>array('rechazado',array(1=>4,2=>3,3=>2,5=>12,6=>4,7=>2,9=>1,11=>1)),
        8=>array('publicado',array(1=>6,2=>4,3=>4,4=>2,5=>4,6=>8,7=>4,8=>2,9=>2,10=>2,11=>2,12=>1))
    );
    $actuales_ids = array();
    foreach ($escenarios as $numero=>$e) $actuales_ids[$numero] = demo_publicar($asignaciones, $clientes_demo[$numero], $periodo, $lineas($e[1]), $supervisor, $almacen, $e[0], $numero===3 ? 'Ejemplo con exceso de presupuesto y saldo negativo.' : 'Solicitud mensual de demostración.');
    $corregida = $escenarios[7][1]; $corregida[5] = 3;
    demo_resultado($asignaciones->guardar($actuales_ids[7], 3, $clientes_demo[7]->id, $periodo, $clientes_demo[7]->presupuesto, $lineas($corregida), 'DEMO: corregida y reenviada.', $supervisor, true), 'No se pudo reenviar la corrección DEMO.');
    demo_resultado($asignaciones->revisar($actuales_ids[7], 4, 'aprobado', 'DEMO: corrección aprobada.', $almacen), 'No se pudo aprobar la corrección DEMO.');
    $pendiente = demo_resultado($compras->consolidar($periodo, $almacen), 'No se pudo crear el consolidado pendiente.');
    demo_compra_exclusiva($compras, $pendiente);
    $cantidades = array(); foreach ($compras->detalles($pendiente) as $d) $cantidades[$d->producto_id] = $d->cantidad;
    demo_ok($cantidades[$productos[1]] === '15.000', 'El consolidado no descontó correctamente el stock disponible.');
    $cantidades[$productos[1]] = '18'; $cantidades[$productos[5]] = '12';
    demo_resultado($compras->guardar($pendiente, 1, $cantidades, 'DEMO · Proveedor de limpieza', 'DEMO: consolidado pendiente de recepción, con cantidades ajustadas.', $almacen), 'No se pudo guardar el consolidado DEMO.');
    // Validate the whole ledger, not just the last balance.
    $movimientos = 0;
    foreach ($productos as $numero=>$id) {
        $saldo = 0;
        foreach (array_reverse($inventario->movimientos($id)) as $m) {
            demo_ok((int)round($m->stock_anterior*1000) === $saldo, 'El historial de stock contiene un salto.');
            $saldo += (int)round($m->cantidad*1000);
            demo_ok((int)round($m->stock_nuevo*1000) === $saldo, 'Un movimiento no coincide con el stock final.'); $movimientos++;
        }
        $esperado = $numero === 1 ? 2000 : ($numero === 6 ? 3000 : 0);
        demo_ok($saldo === $esperado && (int)round($inventario->producto($id)->stock*1000) === $esperado, 'El inventario no coincide con las entregas.');
    }
    foreach (array('borrador'=>1,'publicado'=>3,'rechazado'=>1,'aprobado'=>3) as $estado=>$cantidad) demo_ok($asignaciones->total(0, $periodo, $estado) === $cantidad, 'Estado inesperado en las asignaciones DEMO.');
    demo_ok(count($compras->asignaciones($recibida)) === 2 && count($compras->asignaciones($pendiente)) === 3, 'El consolidado no incluye las asignaciones previstas.');
    demo_ok($compras->obtener($recibida)->estado === 'recibida' && $compras->obtener($pendiente)->estado === 'borrador', 'Estado inesperado en las compras DEMO.');
    demo_ok(demo_huella_real($connection) === $huella && $connection->trans_status(), 'La comprobación de registros originales no pasó.');
    if ($verificar) demo_ok($connection->trans_rollback(), 'No se pudo revertir la verificación.');
    else demo_ok($connection->trans_commit(), 'No se pudo confirmar la carga.');
    $transaccion = false;
    echo $verificar ? "Verificación completa; datos de demostración revertidos.\n" : "Datos DEMO creados; registros originales conservados.\n";
    echo '12 productos, 8 clientes, 10 asignaciones, 2 órdenes y '.$movimientos." movimientos.\n";
    echo $periodo.": 1 borrador, 3 pendientes, 1 devolución, 3 aprobadas; OC #".$pendiente." pendiente de recepción.\n";
    echo $anterior.': 2 aprobadas, OC #'.$recibida." recibida y entregas registradas.\n";
    if (!$verificar) echo 'Borrador editable: almacen/asignacion/'.$actuales_ids[1].PHP_EOL.'Inventario con historial completo: almacen/inventario/'.$productos[1].PHP_EOL;
    $connection->query("SELECT RELEASE_LOCK('almacen-carga-demo')");
} catch (Throwable $e) {
    if (isset($connection) && $transaccion) $connection->trans_rollback();
    if (isset($connection)) $connection->close(); // Also releases advisory locks on failed runs.
    fwrite(STDERR, 'No se completó la carga DEMO: '.$e->getMessage().PHP_EOL); exit(1);
}
