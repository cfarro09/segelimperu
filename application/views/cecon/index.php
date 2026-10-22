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
<div class="modal fade" id="cecon-detalle" tabindex="-1" role="dialog" aria-labelledby="cecon-detalle-titulo" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="cecon-detalle-titulo">Detalle de la tarea</h5><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
        <div class="modal-body"><dl class="row mb-0 cecon-detalle">
            <?php foreach(array('registro'=>'Registro','cliente'=>'Cliente','categoria'=>'Categoría','area'=>'Área actual','supervisor'=>'Supervisor','estado'=>'Estado','comentario'=>'Comentario del supervisor','descargo'=>'Descargo','cierre'=>'Cierre') as $campo=>$etiqueta): ?>
            <dt class="col-sm-4"><?= html_escape($etiqueta) ?></dt><dd class="col-sm-8" data-campo="<?= $campo ?>"></dd>
            <?php endforeach; ?>
        </dl></div>
        <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button></div>
    </div></div>
</div>
<?php $this->load->view('almacen/datatables'); ?>
<script>
jQuery(function($) {
    var modal=$('#cecon-detalle');
    $(document).on('click','[data-cecon-detalle]',function() {
        var datos=this.dataset;
        modal.find('#cecon-detalle-titulo').text('Tarea '+datos.codigo);
        modal.find('[data-campo]').each(function() { $(this).text(datos[this.dataset.campo] || '—'); });
        modal.modal('show');
    });
});
</script>
