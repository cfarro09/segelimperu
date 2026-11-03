<?php defined('BASEPATH') OR exit('No direct script access allowed');
$volver = 'almacen/cargos?vista='.$vista.($periodo!==''?'&periodo='.$periodo:''); ?>
<div class="content"><div class="container-fluid">
<?php $this->load->view('almacen/cabecera', array('cabecera_titulo'=>'Cargos de guías', 'cabecera_acciones'=>array())); ?>
<?php foreach(array('almacen_ok'=>'success','almacen_error'=>'danger') as $key=>$class): if($m=$this->session->flashdata($key)): ?><div class="alert alert-<?= $class ?>" role="status"><?= html_escape($m) ?></div><?php endif; endforeach; ?>
<div class="card-box">
    <div class="pendientes-barra">
        <nav class="pendientes-vistas" aria-label="Estado del cargo">
            <?php foreach(Almacen_cargos_model::VISTAS as $clave=>$texto): $actual=$vista===$clave; ?>
            <a class="btn btn-sm <?= $actual?'btn-primary':'btn-light' ?>" href="<?= site_url('almacen/cargos').'?vista='.$clave.($periodo!==''?'&periodo='.$periodo:'') ?>"<?= $actual?' aria-current="page"':'' ?>><?= html_escape($texto) ?> <span class="badge <?= $actual?'badge-light':'badge-secondary' ?>"><?= (int)$conteos[$clave] ?></span></a>
            <?php endforeach; ?>
        </nav>
        <div class="cargos-acciones">
            <form method="get" action="<?= site_url('almacen/cargos') ?>" class="cargos-mes"><input type="hidden" name="vista" value="<?= html_escape($vista) ?>"><label for="cargos-periodo" class="mb-0">Mes</label><input type="month" id="cargos-periodo" name="periodo" class="form-control form-control-sm" value="<?= html_escape($periodo) ?>" onchange="this.form.submit()"><?php if($periodo!==''): ?><a class="btn btn-link btn-sm" href="<?= site_url('almacen/cargos').'?vista='.$vista ?>">Todos los meses</a><?php endif; ?></form>
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-cargo-manual"><i class="mdi mdi-plus" aria-hidden="true"></i> Registrar guía manual</button>
        </div>
    </div>
    <div class="table-responsive"><table data-almacen-tabla data-filtros-columna class="table table-striped table-sm"><thead><tr>
        <th>Guía</th><th>Fecha</th><th data-filtro="lista">Cliente / sede</th><th data-filtro="lista">Supervisor</th><th data-filtro="lista">Origen</th><th data-filtro="lista">Estado</th><th>Días sin cargo</th><th>Subido / verificado</th><th>Acciones</th>
    </tr></thead><tbody>
    <?php foreach($cargos as $k): $dias=Almacen_cargos_model::dias_sin_cargo($k); $estado=!$k->archivo?'Pendiente de subir':(!$k->verificado_en?'Por verificar':'Verificado'); ?>
    <tr data-registro-id="<?= (int)$k->id ?>">
        <td><strong><?= html_escape($k->numero_guia) ?></strong></td>
        <td data-order="<?= html_escape($k->fecha_guia) ?>"><?= html_escape(date('d/m/Y',strtotime($k->fecha_guia))) ?></td>
        <td><?= html_escape($k->sede) ?></td>
        <td><?= html_escape(trim($k->supervisor)) ?></td>
        <td><?= $k->despacho_id?'Sistema':'Manual' ?></td>
        <td><span class="badge <?= !$k->archivo?'badge-warning':(!$k->verificado_en?'badge-info':'badge-success') ?>"><?= $estado ?></span></td>
        <td class="text-right" data-order="<?= $dias===null?-1:$dias ?>"><?= $dias===null?'—':($dias===0?'Hoy':$dias) ?></td>
        <td class="small"><?php if($k->archivo): ?><?= html_escape(trim($k->subido_por_nombre)) ?> · <?= html_escape(date('d/m/Y',strtotime($k->subido_en))) ?><?php if($k->verificado_en): ?><br>✓ <?= html_escape(trim($k->verificado_por_nombre)) ?> · <?= html_escape(date('d/m/Y',strtotime($k->verificado_en))) ?><?php endif; ?><?php endif; ?><?php if($k->observaciones!==''): ?><div class="text-muted"><?= html_escape($k->observaciones) ?></div><?php endif; ?></td>
        <td class="almacen-celda-acciones"><div class="almacen-acciones-iconos">
            <?php if($k->archivo): ?><a class="btn btn-light" href="<?= site_url('almacen/cargo_archivo/'.$k->id) ?>" target="_blank" rel="noopener" data-almacen-tooltip title="Ver cargo" aria-label="Ver cargo de la guía <?= html_escape($k->numero_guia) ?>"><i class="mdi <?= $k->archivo_tipo==='application/pdf'?'mdi-file-document':'mdi-file-image' ?>" aria-hidden="true"></i></a><?php endif; ?>
            <?php if(!$k->verificado_en): ?><button type="button" class="btn btn-light" data-toggle="modal" data-target="#modal-subir-cargo" data-cargo-id="<?= (int)$k->id ?>" data-cargo-guia="<?= html_escape($k->numero_guia.' · '.$k->sede) ?>" data-almacen-tooltip title="<?= $k->archivo?'Reemplazar cargo':'Subir cargo' ?>" aria-label="<?= $k->archivo?'Reemplazar':'Subir' ?> cargo de la guía <?= html_escape($k->numero_guia) ?>"><i class="mdi <?= $k->archivo?'mdi-camera':'mdi-upload' ?>" aria-hidden="true"></i></button><?php endif; ?>
            <?php if($k->archivo && !$k->verificado_en && $puede_verificar): ?><?= form_open('almacen/verificar_cargo',array('class'=>'d-inline')) ?><input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="id" value="<?= (int)$k->id ?>"><input type="hidden" name="accion" value="verificar"><input type="hidden" name="volver" value="<?= html_escape($volver) ?>"><button class="btn btn-light text-success" data-almacen-tooltip title="Marcar verificado" aria-label="Marcar verificado"><i class="mdi mdi-check" aria-hidden="true"></i></button><?= form_close() ?><?php endif; ?>
            <?php if($k->verificado_en && $puede_desverificar): ?><?= form_open('almacen/verificar_cargo',array('class'=>'d-inline')) ?><input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="id" value="<?= (int)$k->id ?>"><input type="hidden" name="accion" value="desverificar"><input type="hidden" name="volver" value="<?= html_escape($volver) ?>"><button class="btn btn-light" data-almacen-tooltip title="Quitar verificación" aria-label="Quitar verificación"><i class="mdi mdi-undo-variant" aria-hidden="true"></i></button><?= form_close() ?><?php endif; ?>
        </div></td>
    </tr>
    <?php endforeach; ?>
    </tbody></table></div>
</div></div></div>

<div class="modal fade" id="modal-subir-cargo" tabindex="-1" role="dialog" aria-labelledby="modal-subir-cargo-titulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">
    <?= form_open_multipart('almacen/subir_cargo',array('data-cargo-form'=>'1')) ?><input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="id" value=""><input type="hidden" name="volver" value="<?= html_escape($volver) ?>">
    <div class="modal-header"><h5 class="modal-title" id="modal-subir-cargo-titulo">Subir cargo</h5><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
    <div class="modal-body"><p class="mb-2"><strong data-cargo-guia></strong></p>
        <label for="cargo-archivo">Foto o PDF de la guía firmada y sellada</label><input type="file" id="cargo-archivo" name="archivo" class="form-control-file" accept="image/*,application/pdf" required>
        <p class="small text-muted mt-1 mb-0">Desde el celular puedes tomar la foto directamente. Las fotos se comprimen al subirlas; los PDF deben pesar hasta 2 MB.</p>
        <img data-cargo-vista class="cargo-vista mt-2" alt="Vista previa del cargo" hidden></div>
    <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Subir cargo</button></div>
    <?= form_close() ?>
</div></div></div>

<div class="modal fade" id="modal-cargo-manual" tabindex="-1" role="dialog" aria-labelledby="modal-cargo-manual-titulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">
    <?= form_open_multipart('almacen/registrar_cargo',array('data-cargo-form'=>'1')) ?><input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="volver" value="<?= html_escape($volver) ?>">
    <div class="modal-header"><h5 class="modal-title" id="modal-cargo-manual-titulo">Registrar guía manual</h5><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
    <div class="modal-body"><p class="small text-muted">Para guías hechas a mano (talonario impreso), por ejemplo retiros de maquinaria o entregas fuera del sistema.</p>
        <div class="row">
            <div class="col-md-6 form-group"><label for="manual-guia">N° de guía</label><input id="manual-guia" name="numero_guia" class="form-control" required maxlength="13" pattern="\d{3,4}-\d{1,8}" placeholder="0001-003499" title="Serie-número, por ejemplo 0001-003499"></div>
            <div class="col-md-6 form-group"><label for="manual-fecha">Fecha de la guía</label><input id="manual-fecha" name="fecha_guia" type="date" class="form-control" required max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>"></div>
            <div class="col-md-12 form-group"><label for="manual-sede">Cliente / sede</label><input id="manual-sede" name="sede" list="manual-sedes" class="form-control" required maxlength="150" autocomplete="off"><datalist id="manual-sedes"><?php foreach($clientes as $c): ?><option value="<?= html_escape($c->nombre) ?>"><?php endforeach; ?></datalist></div>
            <div class="col-md-12 form-group"><label for="manual-archivo">Foto o PDF de la guía firmada</label><input type="file" id="manual-archivo" name="archivo" class="form-control-file" accept="image/*,application/pdf" required><img data-cargo-vista class="cargo-vista mt-2" alt="Vista previa del cargo" hidden></div>
            <div class="col-md-12 form-group mb-0"><label for="manual-obs">Observaciones <span class="text-muted">(opcional)</span></label><input id="manual-obs" name="observaciones" class="form-control" maxlength="500" placeholder="Ej.: retiro de aspiradora Fracht"></div>
        </div></div>
    <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Registrar guía</button></div>
    <?= form_close() ?>
</div></div></div>
<?php $this->load->view('almacen/datatables'); ?>
