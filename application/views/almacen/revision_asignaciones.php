<?php defined('BASEPATH') OR exit('No direct script access allowed'); $vista_compra=$vista==='compra'; $pendientes_despacho=$pendientes_despacho ?? $aprobados; ?>
<div class="content"><div class="container-fluid" id="almacen-administracion">
<?php $this->load->view('almacen/cabecera',array('cabecera_titulo'=>'Administración de almacén','cabecera_acciones'=>array())); ?>
<?php foreach(array('almacen_ok'=>'success','almacen_error'=>'danger') as $key=>$class): if($m=$this->session->flashdata($key)): ?>
    <div class="alert alert-<?= $class ?>" role="status"><?= html_escape($m) ?></div>
<?php endif; endforeach; ?>
<div class="almacen-administracion-barra">
    <ul class="nav nav-tabs" id="almacen-secciones" role="tablist">
        <li class="nav-item"><a class="nav-link <?= !$vista_compra?'active':'' ?>" id="tab-asignaciones" data-toggle="tab" data-vista="asignaciones" href="#panel-asignaciones" role="tab" aria-controls="panel-asignaciones" aria-selected="<?= !$vista_compra?'true':'false' ?>"><i class="mdi mdi-format-list-bulleted" aria-hidden="true"></i> Asignaciones</a></li>
        <li class="nav-item"><a class="nav-link <?= $vista_compra?'active':'' ?>" id="tab-compra" data-toggle="tab" data-vista="compra" href="#panel-compra" role="tab" aria-controls="panel-compra" aria-selected="<?= $vista_compra?'true':'false' ?>"><i class="mdi mdi-cart" aria-hidden="true"></i> Orden de compra</a></li>
    </ul>
    <form method="get" action="<?= site_url('almacen/revision_asignaciones') ?>" class="almacen-mes" data-almacen-filtros data-almacen-mes>
        <label for="periodo">Mes</label><input type="month" name="periodo" id="periodo" class="form-control" value="<?= html_escape($periodo) ?>" required>
        <input type="hidden" name="vista" value="<?= html_escape($vista) ?>"><input type="hidden" name="cliente_id" value="<?= (int)$cliente_id ?>"><input type="hidden" name="estado" value="<?= html_escape($estado) ?>">
        <noscript><button class="btn btn-secondary">Aplicar</button></noscript>
    </form>
</div>
<div class="tab-content">
    <div class="tab-pane <?= !$vista_compra?'active':'' ?>" id="panel-asignaciones" role="tabpanel" aria-labelledby="tab-asignaciones">
        <div class="card-box">
            <div class="almacen-revision-resumen"><h4 class="header-title">Asignaciones</h4><div><span class="badge badge-info"><?= (int)$publicados ?> por revisar</span> <span class="badge badge-success"><?= (int)$aprobados ?> aprobadas</span></div></div>
            <form method="get" action="<?= site_url('almacen/revision_asignaciones') ?>" class="almacen-revision-filtros" data-almacen-filtros>
                <input type="hidden" name="periodo" value="<?= html_escape($periodo) ?>"><input type="hidden" name="vista" value="asignaciones">
                <div><label for="cliente_id">Cliente</label><select name="cliente_id" id="cliente_id" class="form-control" data-select-buscable><option value="0">Todos</option><?php foreach($clientes as $c): ?><option value="<?= (int)$c->id ?>" <?= $cliente_id==(int)$c->id?'selected':'' ?>><?= html_escape($c->nombre) ?></option><?php endforeach; ?></select></div>
                <div><label for="estado">Estado</label><select name="estado" id="estado" class="form-control"><?php foreach(array('revision'=>'Todos los enviados','publicado'=>'Pendientes de revisión','aprobado'=>'Aprobados','rechazado'=>'Necesitan corrección') as $v=>$label): ?><option value="<?= $v ?>" <?= $estado===$v?'selected':'' ?>><?= $label ?></option><?php endforeach; ?></select></div>
                <noscript><button class="btn btn-secondary">Aplicar</button></noscript>
            </form>
            <div class="table-responsive"><table data-almacen-tabla class="table table-striped"><thead><tr><th>Mes</th><th>Cliente</th><th>Presupuesto máximo S/</th><th>Total S/</th><th>Exceso S/</th><th>Saldo acumulado S/</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
            <?php foreach($asignaciones as $a) $this->load->view('almacen/revision_asignacion_fila',array('a'=>$a)); ?>
            </tbody></table></div>
        </div>
    </div>
    <div class="tab-pane <?= $vista_compra?'active':'' ?>" id="panel-compra" role="tabpanel" aria-labelledby="tab-compra">
        <?php $abierta=null; foreach($compras as $o) if($o->estado==='borrador' && ($o->tipo ?? 'consolidada')==='consolidada') $abierta=$o; $nuevas_compra=(int)($nuevas_compra ?? 0); ?>
        <div class="card-box compra-ordenes-barra">
            <nav class="compra-ordenes" aria-label="Órdenes de compra del mes">
                <?php if(!$compras): ?><span class="text-muted">Aún no hay órdenes de compra este mes.</span><?php endif; ?>
                <?php foreach($compras as $o): $actual=$compra && (int)$compra->id===(int)$o->id; ?>
                <a class="btn btn-sm <?= $actual?'btn-primary':'btn-light' ?>" href="<?= site_url('almacen/revision_asignaciones').'?periodo='.rawurlencode($periodo).'&vista=compra&compra_id='.(int)$o->id ?>"<?= $actual?' aria-current="page"':'' ?>><?= html_escape(Almacen_compras_model::codigo($o)) ?><?php if(($o->tipo ?? '')==='manual'): ?> <span class="compra-manual-etiqueta"><?= html_escape($o->descripcion!==''?$o->descripcion:'Manual') ?></span><?php endif; ?> <span class="badge <?= array('borrador'=>'badge-warning','aprobada'=>'badge-info','recibida'=>'badge-success')[$o->estado] ?>"><?= array('borrador'=>'Borrador','aprobada'=>'Aprobada','recibida'=>'Recibida')[$o->estado] ?></span></a>
                <?php endforeach; ?>
            </nav>
            <div class="compra-generar">
            <button type="button" class="btn btn-outline-primary btn-sm" data-toggle="modal" data-target="#modal-compra-manual"><i class="mdi mdi-cart-plus" aria-hidden="true"></i> Nueva orden manual</button>
            <?php if(!$abierta): ?>
            <?= form_open('almacen/consolidar_compras',array('class'=>'compra-generar','data-compra-generar'=>'1')) ?><input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="periodo" value="<?= html_escape($periodo) ?>">
                <span class="small text-muted" data-compra-nuevas><?= $nuevas_compra?$nuevas_compra.($nuevas_compra>1?' asignaciones aprobadas nuevas sin orden de compra':' asignación aprobada nueva sin orden de compra'):'No hay asignaciones aprobadas nuevas.' ?></span>
                <button class="btn btn-primary btn-sm" <?= !$nuevas_compra?'disabled':'' ?>><i class="mdi mdi-cart-plus" aria-hidden="true"></i> Generar orden de compra</button>
            <?= form_close() ?>
            <?php endif; ?>
            </div>
        </div>
        <div class="modal fade" id="modal-compra-manual" tabindex="-1" role="dialog" aria-labelledby="modal-compra-manual-titulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">
            <?= form_open('almacen/crear_compra_manual') ?><input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="periodo" value="<?= html_escape($periodo) ?>">
            <div class="modal-header"><h5 class="modal-title" id="modal-compra-manual-titulo">Nueva orden de compra manual</h5><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
            <div class="modal-body"><p class="small text-muted">Para compras que no salen de las asignaciones (uniformes, EPP, reposición). Sigue el mismo flujo: Administración la aprueba y Almacén confirma la recepción.</p>
                <label for="compra-manual-descripcion">Descripción</label><input id="compra-manual-descripcion" name="descripcion" class="form-control" maxlength="150" required placeholder="Ej.: Uniformes y EPP - octubre"></div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><button class="btn btn-primary">Crear orden</button></div>
            <?= form_close() ?>
        </div></div></div>
        <?php if($compra) $this->load->view('almacen/orden_compra_contenido',array_merge($datos_compra,array('token'=>$token,'compra_integrada'=>true))); ?>
    </div>
</div>
</div></div>
<?php $this->load->view('almacen/datatables'); ?>
<?php $this->load->view('almacen/formulario_modal'); ?>
