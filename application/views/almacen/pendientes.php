<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content"><div class="container-fluid">
<?php $this->load->view('almacen/cabecera', array('cabecera_titulo'=>'Pendientes de almacén', 'cabecera_acciones'=>array(array('texto'=>'Nuevo pendiente','icono'=>'mdi-plus','url'=>site_url('almacen/pendiente'),'clase'=>'btn-primary','formulario'=>'pendiente')))); ?>
<?php foreach(array('almacen_ok'=>'success','almacen_error'=>'danger') as $key=>$class): if($m=$this->session->flashdata($key)): ?><div class="alert alert-<?= $class ?>" role="status"><?= html_escape($m) ?></div><?php endif; endforeach; ?>
<div class="card-box">
    <div class="pendientes-barra">
        <nav class="pendientes-vistas" aria-label="Filtrar por estado">
            <?php foreach(Almacen_pendientes_model::VISTAS as $clave=>$texto): $actual=$vista===$clave; ?>
            <a class="btn btn-sm <?= $actual?'btn-primary':'btn-light' ?>" href="<?= site_url('almacen/pendientes').'?vista='.$clave ?>"<?= $actual?' aria-current="page"':'' ?>><?= html_escape($texto) ?> <span class="badge <?= $actual?'badge-light':'badge-secondary' ?>"><?= (int)$conteos[$clave] ?></span></a>
            <?php endforeach; ?>
        </nav>
        <?php if($conteos['urgentes']): ?><span class="badge badge-danger pendientes-urgentes"><i class="mdi mdi-alert" aria-hidden="true"></i> <?= (int)$conteos['urgentes'] ?> urgente<?= $conteos['urgentes']>1?'s':'' ?> sin atender</span><?php endif; ?>
    </div>
    <div class="table-responsive"><table data-almacen-tabla data-filtros-columna class="table table-striped table-sm pendientes-tabla"><thead><tr>
        <th>Fecha</th><th data-filtro="lista">Tipo</th><th data-filtro="lista">Sede</th><th>Pendiente</th><th data-filtro="lista">Enviado por</th><th data-filtro="lista">Prioridad</th><th data-filtro="lista">Estado</th><th>Días</th><th>Observaciones</th><th>Acciones</th>
    </tr></thead><tbody>
    <?php foreach($pendientes as $p) $this->load->view('almacen/pendiente_fila', array('p'=>$p,'puede_gestionar'=>$puede_gestionar,'vista'=>$vista)); ?>
    </tbody></table></div>
</div></div></div>
<?php $this->load->view('almacen/formulario_modal'); ?>
<?php $this->load->view('almacen/datatables'); ?>
