<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content"><div class="container-fluid">
<?php $this->load->view('almacen/cabecera', array('cabecera_titulo'=>'CECON · Tareas', 'cabecera_solo_lectura'=>true)); ?>
<div class="card-box">
    <div class="pendientes-barra">
        <nav class="pendientes-vistas" aria-label="Filtrar por estado">
            <?php foreach(Cecon_model::vistas() as $clave=>$texto): $actual=$vista===$clave; ?>
            <a class="btn btn-sm <?= $actual?'btn-primary':'btn-light' ?>" href="<?= site_url('cecon').'?vista='.urlencode($clave) ?>"<?= $actual?' aria-current="page"':'' ?>><?= html_escape($texto) ?> <span class="badge <?= $actual?'badge-light':'badge-secondary' ?>"><?= (int)$conteos[$clave] ?></span></a>
            <?php endforeach; ?>
        </nav>
    </div>
    <?php if(count($tareas)>=Cecon_model::LIMITE): ?><div class="alert alert-warning" role="status">Se muestran las <?= (int)Cecon_model::LIMITE ?> tareas más recientes de esta vista.</div><?php endif; ?>
    <div class="table-responsive"><table data-almacen-tabla data-filtros-columna class="table table-striped cecon-tabla"><thead><tr>
        <th>Código</th><th>Registro</th><th data-filtro="lista">Cliente</th><th data-filtro="lista">Categoría</th><th data-filtro="lista">Área actual</th><th data-filtro="lista">Supervisor</th><th data-filtro="lista">Estado</th><th>Comentario del supervisor</th><th>Descargo</th><th>Cierre</th><th>Acciones</th>
    </tr></thead><tbody>
    <?php foreach($tareas as $t) $this->load->view('cecon/fila', array('t'=>$t)); ?>
    </tbody></table></div>
</div></div></div>
<div class="modal fade cecon-modal" id="cecon-detalle" tabindex="-1" role="dialog" aria-labelledby="cecon-detalle-codigo" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document"><div class="modal-content">
        <div class="modal-header">
            <div class="cecon-cabecera"><span class="cecon-codigo" id="cecon-detalle-codigo"></span><span class="badge" data-cecon="estado"></span></div>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body">
            <p class="cecon-estado-carga" role="status" aria-live="polite" data-cecon="carga">Cargando detalle…</p>
            <div class="alert alert-danger" role="alert" data-cecon="error" hidden></div>
            <div data-cecon="contenido" hidden>
                <p class="cecon-categoria" data-cecon="categoria"></p>
                <h4 class="cecon-titulo" data-cecon="cliente"></h4>
                <p class="cecon-area"><i class="mdi mdi-swap-horizontal" aria-hidden="true"></i> Área actual: <strong data-cecon="area"></strong></p>
                <div class="row">
                    <div class="col-lg-6"><section class="cecon-tarjeta" aria-labelledby="cecon-campo-titulo">
                        <header><h5 id="cecon-campo-titulo"><i class="mdi mdi-camera" aria-hidden="true"></i> Evidencia de campo (supervisor)</h5><span class="cecon-paso">1. Inspección en campo</span></header>
                        <div class="cecon-fotos" data-cecon="fotos-campo"></div>
                        <ul class="cecon-datos">
                            <li><i class="mdi mdi-map-marker" aria-hidden="true"></i> <strong>Ubicación GPS:</strong> <span data-cecon="ubicacion"></span></li>
                            <li><i class="mdi mdi-clock" aria-hidden="true"></i> <strong>Hora de registro:</strong> <span data-cecon="registro"></span></li>
                            <li><i class="mdi mdi-account" aria-hidden="true"></i> <strong>Supervisor:</strong> <span data-cecon="supervisor"></span></li>
                        </ul>
                        <div class="cecon-nota"><strong>Observación en sitio</strong><p data-cecon="comentario"></p></div>
                    </section></div>
                    <div class="col-lg-6"><section class="cecon-tarjeta" aria-labelledby="cecon-descargo-titulo">
                        <header><h5 id="cecon-descargo-titulo"><i class="mdi mdi-check-circle-outline" aria-hidden="true"></i> Descargo del área responsable</h5><span class="cecon-paso">2. Contra-evidencia</span></header>
                        <p class="cecon-vacio" data-cecon="sin-descargo" hidden>Aún no hay descargo registrado para esta tarea.</p>
                        <div data-cecon="descargo" hidden>
                            <div class="cecon-fotos" data-cecon="fotos-descargo"></div>
                            <ul class="cecon-datos">
                                <li><i class="mdi mdi-account" aria-hidden="true"></i> <strong>Responsable:</strong> <span data-cecon="descargo-por"></span></li>
                                <li><i class="mdi mdi-clock" aria-hidden="true"></i> <strong>Hora de descargo:</strong> <span data-cecon="descargo-hora"></span></li>
                                <li data-cecon="cierre-fila"><i class="mdi mdi-flag-checkered" aria-hidden="true"></i> <strong>Cierre:</strong> <span data-cecon="cierre"></span></li>
                            </ul>
                            <div class="cecon-nota"><strong>Nota de solución del área</strong><p data-cecon="descargo-comentario"></p></div>
                        </div>
                    </section></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
            <button type="button" class="btn btn-success" data-cecon="aprobar" hidden><i class="mdi mdi-check" aria-hidden="true"></i> Aprobar descargo y certificar ticket</button>
        </div>
    </div></div>
</div>
<?php $this->load->view('almacen/datatables'); ?>
<script>
jQuery(function($) {
    var modal=$('#cecon-detalle'), raiz=modal[0], urlDetalle=<?= json_encode(site_url('cecon/detalle')) ?>, peticion=null;
    function campo(nombre) { return raiz.querySelector('[data-cecon="'+nombre+'"]'); }
    function texto(nombre, valor) { campo(nombre).textContent=valor || '—'; }
    function fotos(nombre, urls, descripcion) {
        var contenedor=campo(nombre); contenedor.textContent=''; contenedor.dataset.n=Math.min(urls.length, 3);
        if(!urls.length) { var vacio=document.createElement('p'); vacio.className='cecon-vacio'; vacio.textContent='Sin fotografías.'; contenedor.appendChild(vacio); return; }
        urls.forEach(function(url, i) {
            var enlace=document.createElement('a'), imagen=document.createElement('img');
            enlace.href=url; enlace.target='_blank'; enlace.rel='noopener noreferrer'; enlace.title='Abrir la fotografía en una pestaña nueva';
            imagen.src=url; imagen.loading='lazy'; imagen.referrerPolicy='no-referrer'; imagen.alt=descripcion+' '+(i+1)+' de '+urls.length;
            enlace.appendChild(imagen); contenedor.appendChild(enlace);
        });
    }
    function mostrar(nombre, visible) { campo(nombre).hidden=!visible; }
    function pintar(d) {
        texto('categoria', d.categoria); texto('cliente', d.cliente || 'Sin cliente asignado'); texto('area', d.area);
        texto('registro', d.registro); texto('supervisor', d.supervisor); texto('comentario', d.comentario);
        var estado=campo('estado'); estado.textContent=d.estado.etiqueta; estado.className='badge '+d.estado.clase;
        var ubicacion=campo('ubicacion'); ubicacion.textContent='';
        if(d.ubicacion) {
            ubicacion.appendChild(document.createTextNode(d.ubicacion.texto+' · '));
            var mapa=document.createElement('a'); mapa.href=d.ubicacion.mapa; mapa.target='_blank'; mapa.rel='noopener noreferrer'; mapa.textContent='Ver en el mapa'; ubicacion.appendChild(mapa);
        } else ubicacion.textContent='No disponible';
        fotos('fotos-campo', d.fotos_campo, 'Fotografía de campo');
        mostrar('sin-descargo', !d.descargo); mostrar('descargo', !!d.descargo);
        if(d.descargo) {
            texto('descargo-por', [d.descargo.por, d.descargo.area].filter(Boolean).join(' · ')); texto('descargo-hora', d.descargo.hora);
            texto('descargo-comentario', d.descargo.comentario); texto('cierre', d.cierre); mostrar('cierre-fila', !!d.cierre);
            fotos('fotos-descargo', d.descargo.fotos, 'Fotografía del descargo');
        }
        // Solo hay algo que aprobar cuando el área ya registró su descargo.
        mostrar('aprobar', d.estado.codigo==='DISCHARGED');
        mostrar('contenido', true);
    }
    // TODO: conectar "Aprobar descargo y certificar ticket" con el API para actualizar el estado de la tarea
    // (sin contrato definido todavía; por ahora el botón no hace nada).
    $(document).on('click','[data-cecon-detalle]',function() {
        var boton=this;
        raiz.querySelector('#cecon-detalle-codigo').textContent=boton.dataset.codigo;
        campo('estado').textContent=''; campo('estado').className='badge';
        mostrar('contenido', false); mostrar('error', false); mostrar('aprobar', false); mostrar('carga', true);
        if(peticion) peticion.abort();
        modal.modal('show');
        peticion=$.ajax({url:urlDetalle+'/'+encodeURIComponent(boton.dataset.id), dataType:'json', cache:false})
            .done(function(d) { mostrar('carga', false); pintar(d); })
            .fail(function(xhr, estado) {
                if(estado==='abort') return;
                mostrar('carga', false);
                var mensaje=xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'No se pudo cargar el detalle. Actualice la página e intente nuevamente.';
                campo('error').textContent=mensaje; mostrar('error', true);
            });
    });
    modal.on('hidden.bs.modal', function() { if(peticion) peticion.abort(); });
});
</script>
