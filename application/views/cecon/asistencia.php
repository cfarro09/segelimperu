<?php defined('BASEPATH') OR exit('No direct script access allowed');
$dias = array('domingo','lunes','martes','miércoles','jueves','viernes','sábado');
$f = DateTime::createFromFormat('!Y-m-d', $fecha);
$fecha_texto = $dias[(int)$f->format('w')].' '.$f->format('d/m/Y');
$es_hoy = $fecha === $hoy;
$url = function($vista) use ($fecha, $hoy) { return site_url('cecon/asistencia').'?vista='.urlencode($vista).($fecha !== $hoy ? '&fecha='.urlencode($fecha) : ''); };
?>
<div class="content"><div class="container-fluid">
<?php $this->load->view('almacen/cabecera', array('cabecera_titulo'=>'Monitor de asistencia', 'cabecera_solo_lectura'=>true)); ?>

<div class="card-box">
    <form class="cecon-barra" method="get" action="<?= site_url('cecon/asistencia') ?>">
        <input type="hidden" name="vista" value="<?= html_escape($vista) ?>">
        <label for="cecon-fecha" class="mb-0 font-weight-bold">Día</label>
        <input type="date" id="cecon-fecha" name="fecha" class="form-control form-control-sm" value="<?= html_escape($fecha) ?>" max="<?= html_escape($hoy) ?>" required>
        <button type="submit" class="btn btn-sm btn-primary">Ver día</button>
        <?php if(!$es_hoy): ?><a class="btn btn-sm btn-light" href="<?= html_escape(site_url('cecon/asistencia').'?vista='.urlencode($vista)) ?>">Ir a hoy</a><?php endif; ?>
        <span class="cecon-barra-estado"><?= html_escape(ucfirst($fecha_texto)) ?> · <?= $es_hoy ? 'datos al '.html_escape((new DateTime('now', new DateTimeZone('America/Lima')))->format('H:i')).' (hora de Lima)' : 'día anterior' ?></span>
        <label class="cecon-auto mb-0"><input type="checkbox" data-cecon-auto<?= $es_hoy ? '' : ' disabled' ?>> Actualizar cada 60 s</label>
    </form>

    <?php if(!$es_hoy): ?><div class="alert alert-info mt-2 mb-0" role="status">Está viendo un día anterior. Una asistencia puede llegar con retraso si el supervisor la registró sin conexión; en ese caso se cuenta en el día en que se registró.</div><?php endif; ?>

    <div class="cecon-kpis" role="group" aria-label="Resumen del día">
        <div class="cecon-kpi"><span class="cecon-kpi-valor"><?= (int)$resumen['total'] ?></span><span class="cecon-kpi-etiqueta">Clientes activos</span><small><?= (int)$resumen['sin_supervisor'] ?> sin supervisor asignado</small></div>
        <div class="cecon-kpi cecon-kpi-ok"><span class="cecon-kpi-valor"><?= (int)$resumen['con'] ?></span><span class="cecon-kpi-etiqueta">Con asistencia</span>
            <div class="progress" role="progressbar" aria-label="Clientes con asistencia" aria-valuenow="<?= (int)$resumen['porcentaje'] ?>" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar bg-success" style="width: <?= (int)$resumen['porcentaje'] ?>%"></div></div>
            <small><?= (int)$resumen['porcentaje'] ?> % · <?= (int)$resumen['operarios'] ?> operario<?= $resumen['operarios']===1?'':'s' ?></small></div>
        <div class="cecon-kpi <?= $resumen['sin']?'cecon-kpi-alerta':'cecon-kpi-ok' ?>"><span class="cecon-kpi-valor"><?= (int)$resumen['sin'] ?></span><span class="cecon-kpi-etiqueta">Sin asistencia</span><small>clientes por registrar</small></div>
        <div class="cecon-kpi <?= $resumen['supervisores_sin_registrar']?'cecon-kpi-alerta':'cecon-kpi-ok' ?>"><span class="cecon-kpi-valor"><?= (int)$resumen['supervisores_sin_registrar'] ?></span><span class="cecon-kpi-etiqueta">Supervisores sin registrar nada</span><small>de <?= (int)$resumen['supervisores'] ?> supervisores</small></div>
    </div>

    <div class="pendientes-barra">
        <nav class="pendientes-vistas" aria-label="Cambiar de vista">
            <?php foreach(Cecon_asistencia_model::VISTAS as $clave=>$texto): $actual=$vista===$clave; ?>
            <a class="btn btn-sm <?= $actual?'btn-primary':'btn-light' ?>" href="<?= html_escape($url($clave)) ?>"<?= $actual?' aria-current="page"':'' ?>><?= html_escape($texto) ?> <span class="badge <?= $actual?'badge-light':($clave==='sin'&&$conteos['sin']?'badge-danger':'badge-secondary') ?>"><?= (int)$conteos[$clave] ?></span></a>
            <?php endforeach; ?>
        </nav>
    </div>

    <?php if($vista==='sin' && !$resumen['sin'] && $resumen['total']): ?><div class="alert alert-success" role="status">Todos los clientes activos tienen asistencia registrada este día.</div><?php endif; ?>

    <?php if($vista==='supervisores'): ?>
    <div class="table-responsive"><table data-almacen-tabla data-filtros-columna class="table table-striped cecon-tabla"><thead><tr>
        <th data-filtro="lista">Supervisor</th><th>Clientes asignados</th><th>Con asistencia</th><th>Sin asistencia</th><th>Cumplimiento</th><th>Operarios</th><th>Último registro</th><th data-filtro="lista">Estado</th>
    </tr></thead><tbody>
    <?php foreach($supervisores as $s): ?>
    <tr class="<?= $s->asignado && $s->con===0 ? 'cecon-fila-alerta' : '' ?>">
        <td><?= html_escape($s->nombre) ?></td>
        <td class="text-right"><?= (int)$s->clientes ?></td>
        <td class="text-right"><?= (int)$s->con ?></td>
        <td class="text-right"><?= (int)$s->sin ?></td>
        <td data-order="<?= (int)$s->porcentaje ?>"><div class="cecon-progreso"><div class="progress"><div class="progress-bar <?= $s->con===0?'bg-danger':($s->sin?'bg-warning':'bg-success') ?>" style="width: <?= (int)$s->porcentaje ?>%"></div></div><span><?= (int)$s->porcentaje ?> %</span></div></td>
        <td class="text-right"><?= (int)$s->operarios ?></td>
        <td data-order="<?= html_escape((string)$s->ultimo) ?>"><?= $s->ultimo ? html_escape(Cecon_model::hora($s->ultimo)) : '—' ?></td>
        <td><span class="badge <?= html_escape($s->estado[1]) ?>"><?= html_escape($s->estado[0]) ?></span></td>
    </tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <?php else: ?>
    <div class="table-responsive"><table data-almacen-tabla data-filtros-columna class="table table-striped cecon-tabla"><thead><tr>
        <th>Código</th><th>Cliente</th><th data-filtro="lista">Supervisor</th><th>Servicio</th><th data-filtro="lista">Estado</th><th>Operarios</th><th>Registros</th><th>Primer registro</th><th>Último registro</th><th>Acciones</th>
    </tr></thead><tbody>
    <?php foreach($clientes as $c):
        if($c->registrada) $estado = array('Registrada', 'badge-success'); elseif(!$c->con_supervisor) $estado = array('Sin supervisor', 'badge-secondary'); else $estado = array('Sin registrar', 'badge-danger');
        $dias_txt = mb_strtolower(trim($c->dias_servicio)); $dias_txt = mb_strtoupper(mb_substr($dias_txt, 0, 1)).mb_substr($dias_txt, 1);
        $horario = trim($c->hora_ingreso.($c->hora_ingreso!==''&&$c->hora_salida!==''?' – ':'').$c->hora_salida); ?>
    <tr class="<?= !$c->registrada ? 'cecon-fila-alerta' : '' ?>">
        <td><?= html_escape($c->codigo) ?></td>
        <td><?= html_escape($c->nombre) ?></td>
        <td><?= $c->con_supervisor ? html_escape($c->supervisor!=='' ? $c->supervisor : 'Supervisor #'.$c->supervisor_id) : '—' ?></td>
        <td class="pendiente-detalle"><?= trim($c->dias_servicio)!=='' ? html_escape($dias_txt) : '<span class="text-muted">Días sin definir</span>' ?><?php if($horario!==''): ?><div class="small text-muted"><?= html_escape($horario) ?></div><?php endif; ?></td>
        <td><span class="badge <?= html_escape($estado[1]) ?>"><?= html_escape($estado[0]) ?></span></td>
        <td class="text-right"><?= $c->registrada ? (int)$c->operarios : '—' ?></td>
        <td class="text-right"><?= $c->registrada ? (int)$c->envios : '—' ?></td>
        <td data-order="<?= html_escape((string)$c->primero) ?>"><?= $c->primero ? html_escape(Cecon_model::hora($c->primero)) : '—' ?></td>
        <td data-order="<?= html_escape((string)$c->ultimo) ?>"><?= $c->ultimo ? html_escape(Cecon_model::hora($c->ultimo)) : '—' ?></td>
        <td class="almacen-celda-acciones"><?php if($c->registrada): ?><div class="almacen-acciones-iconos">
            <button type="button" class="btn btn-light" data-cecon-asistencia data-id="<?= (int)$c->id ?>" data-codigo="<?= html_escape($c->codigo) ?>" data-nombre="<?= html_escape($c->nombre) ?>" data-almacen-tooltip title="Ver asistencia registrada" aria-label="Ver la asistencia registrada de <?= html_escape($c->nombre) ?>" aria-haspopup="dialog"><i class="mdi mdi-eye" aria-hidden="true"></i></button>
        </div><?php else: ?><span class="text-muted" aria-hidden="true">—</span><span class="sr-only">Sin asistencia registrada</span><?php endif; ?></td>
    </tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <?php endif; ?>
</div>
</div></div>
<div class="modal fade cecon-modal cecon-modal-chico" id="cecon-asistencia" tabindex="-1" role="dialog" aria-labelledby="cecon-asistencia-titulo" aria-hidden="true">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <div class="modal-header">
            <div class="cecon-cabecera"><span class="cecon-codigo" data-cec="codigo"></span><h5 class="modal-title mb-0" id="cecon-asistencia-titulo" data-cec="cliente"></h5></div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <p role="status" aria-live="polite" class="cecon-estado-carga" data-cec="carga">Cargando asistencia…</p>
            <div class="alert alert-danger" role="alert" data-cec="error" hidden></div>
            <div data-cec="contenido" hidden>
                <p class="cecon-resumen">
                    <span><i class="mdi mdi-account" aria-hidden="true"></i> <strong>Supervisor:</strong> <span data-cec="supervisor"></span></span>
                    <span><i class="mdi mdi-calendar" aria-hidden="true"></i> <strong>Día:</strong> <span data-cec="fecha"></span></span>
                </p>
                <div class="table-responsive"><table class="table table-sm table-striped cecon-operarios mb-1" data-cec="tabla">
                    <caption class="sr-only">Operarios registrados</caption>
                    <thead><tr><th scope="col" class="text-right">N.º</th><th scope="col">Operario</th><th scope="col" data-cec="col-envio">Envío</th><th scope="col">Registrado</th></tr></thead>
                    <tbody data-cec="filas"></tbody>
                </table></div>
                <p class="cecon-vacio" data-cec="vacio" hidden>No hay asistencia registrada para este día.</p>
                <p class="small text-muted mb-0"><span data-cec="total"></span><span data-cec="anulados"></span></p>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button></div>
    </div></div>
</div>
<?php $this->load->view('almacen/datatables'); ?>
<script>
jQuery(function($) {
    var modal=$('#cecon-asistencia'), raiz=modal[0], url=<?= json_encode(site_url('cecon/asistencia_detalle')) ?>, fecha=<?= json_encode($fecha) ?>, peticion=null;
    function campo(n) { return raiz.querySelector('[data-cec="'+n+'"]'); }
    function texto(n, v) { campo(n).textContent=v || '—'; }
    function mostrar(n, v) { campo(n).hidden=!v; }
    function celda(fila, valor, clase) { var td=document.createElement('td'); td.textContent=valor; if(clase) td.className=clase; fila.appendChild(td); }
    function pintar(d) {
        texto('codigo', d.codigo); texto('cliente', d.cliente); texto('supervisor', d.supervisor);
        var partes=d.fecha.split('-'); texto('fecha', partes[2]+'/'+partes[1]+'/'+partes[0]);
        var varios=d.envios.length>1, cuerpo=campo('filas'); cuerpo.textContent='';
        var n=0;
        d.envios.forEach(function(envio, i) {
            envio.operarios.forEach(function(nombre) {
                var tr=document.createElement('tr'); n++;
                celda(tr, String(n), 'text-right'); celda(tr, nombre);
                if(varios) celda(tr, String(i+1), 'text-center');
                celda(tr, envio.hora);
                cuerpo.appendChild(tr);
            });
        });
        raiz.querySelectorAll('[data-cec="col-envio"]').forEach(function(th) { th.hidden=!varios; });
        mostrar('tabla', n>0); mostrar('vacio', n===0);
        campo('total').textContent=d.operarios+' operario'+(d.operarios===1?'':'s')+(varios?' en '+d.envios.length+' envíos':'');
        campo('anulados').textContent=d.anulados ? ' · '+d.anulados+' registro'+(d.anulados===1?'':'s')+' anulado'+(d.anulados===1?'':'s')+' no se cuenta'+(d.anulados===1?'':'n') : '';
        mostrar('contenido', true);
    }
    $(document).on('click','[data-cecon-asistencia]',function() {
        var b=this; texto('codigo', b.dataset.codigo); texto('cliente', b.dataset.nombre);
        mostrar('contenido', false); mostrar('error', false); mostrar('carga', true);
        if(peticion) peticion.abort();
        modal.modal('show');
        peticion=$.ajax({url:url+'/'+encodeURIComponent(b.dataset.id)+'?fecha='+encodeURIComponent(fecha), dataType:'json', cache:false})
            .done(function(d) { mostrar('carga', false); pintar(d); })
            .fail(function(xhr, estado) {
                if(estado==='abort') return;
                mostrar('carga', false);
                campo('error').textContent=xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'No se pudo cargar la asistencia. Actualice la página e intente nuevamente.';
                mostrar('error', true);
            });
    });
    modal.on('hidden.bs.modal', function() { if(peticion) peticion.abort(); });
});
</script>
<script>
jQuery(function($) {
    var casilla=document.querySelector('[data-cecon-auto]'), clave='cecon-asistencia-auto', temporizador=null;
    if(!casilla) return;
    function guardado() { try { return sessionStorage.getItem(clave)==='1'; } catch(e) { return false; } }
    function guardar(v) { try { sessionStorage.setItem(clave, v?'1':'0'); } catch(e) {} }
    function ajustar() {
        clearTimeout(temporizador);
        if(casilla.checked && !casilla.disabled) temporizador=setTimeout(function() { location.reload(); }, 60000);
    }
    casilla.checked=guardado() && !casilla.disabled;
    casilla.addEventListener('change', function() { guardar(casilla.checked); ajustar(); });
    ajustar();
});
</script>
