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
    <div class="table-responsive"><table data-almacen-tabla data-filtros-columna class="table table-striped table-sm pendientes-tabla cecon-tabla"><thead><tr>
        <th>Código</th><th>Registro</th><th data-filtro="lista">Cliente</th><th data-filtro="lista">Categoría</th><th data-filtro="lista">Área actual</th><th data-filtro="lista">Supervisor</th><th data-filtro="lista">Estado</th><th>Comentario del supervisor</th><th>Descargo</th><th>Cierre</th>
    </tr></thead><tbody>
    <?php foreach($tareas as $t) $this->load->view('cecon/fila', array('t'=>$t)); ?>
    </tbody></table></div>
</div></div></div>
<?php $this->load->view('almacen/datatables'); ?>
