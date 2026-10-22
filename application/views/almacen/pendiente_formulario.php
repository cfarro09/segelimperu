<?php defined('BASEPATH') OR exit('No direct script access allowed');
$nuevo=!$pendiente||empty($pendiente->id); $v=function($campo,$defecto='') use ($pendiente) { return $pendiente&&isset($pendiente->$campo)?$pendiente->$campo:$defecto; };
$estado=$v('estado','pendiente'); $prioridad=$v('prioridad','normal'); $tipo=$v('tipo','');
$solicitante=$v('solicitante',!$puede_gestionar&&$nuevo?$nombre_usuario:''); ?>
<?= form_open('almacen/guardar_pendiente',array('data-almacen-edicion'=>'pendiente','id'=>'form-pendiente')) ?>
<div class="<?= !empty($en_modal)?'modal-body':'almacen-formulario-campos' ?>">
<div class="alert alert-danger" data-formulario-errores role="alert" tabindex="-1" <?= !$errores?'hidden':'' ?>><?php if($errores): ?><ul class="mb-0"><?php foreach($errores as $error): ?><li><?= html_escape($error) ?></li><?php endforeach; ?></ul><?php endif; ?></div>
<input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="id" value="<?= $nuevo?0:(int)$pendiente->id ?>">
<div class="row">
    <div class="col-md-3 form-group"><label for="pendiente-fecha">Fecha</label><input id="pendiente-fecha" name="fecha" type="date" required max="<?= date('Y-m-d') ?>" class="form-control" value="<?= html_escape($v('fecha',date('Y-m-d'))) ?>"></div>
    <div class="col-md-4 form-group"><label for="pendiente-tipo">Tipo</label><select id="pendiente-tipo" name="tipo" required class="form-control"><option value="">Selecciona</option><?php foreach(Almacen_pendientes_model::TIPOS as $clave=>$texto): ?><option value="<?= $clave ?>" <?= $tipo===$clave?'selected':'' ?>><?= html_escape($texto) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-5 form-group"><span class="d-block mb-2" id="pendiente-prioridad-etiqueta">Prioridad</span>
        <div class="btn-group btn-group-toggle" data-toggle="buttons" role="radiogroup" aria-labelledby="pendiente-prioridad-etiqueta">
            <label class="btn btn-outline-secondary btn-sm <?= $prioridad==='normal'?'active':'' ?>"><input type="radio" name="prioridad" value="normal" <?= $prioridad==='normal'?'checked':'' ?>> Normal</label>
            <label class="btn btn-outline-danger btn-sm <?= $prioridad==='urgente'?'active':'' ?>"><input type="radio" name="prioridad" value="urgente" <?= $prioridad==='urgente'?'checked':'' ?>> Urgente</label>
        </div>
    </div>
    <div class="col-md-7 form-group"><label for="pendiente-sede">Sede / unidad</label><input id="pendiente-sede" name="sede" list="pendiente-sedes" maxlength="150" required autocomplete="off" class="form-control" placeholder="Cliente o sede (p. ej. Almacén Borgoño)" value="<?= html_escape($v('sede')) ?>">
        <datalist id="pendiente-sedes"><?php foreach($clientes as $c): ?><option value="<?= html_escape($c->nombre) ?>"><?php endforeach; ?></datalist></div>
    <div class="col-md-5 form-group"><label for="pendiente-solicitante">Enviado por</label><input id="pendiente-solicitante" name="solicitante" list="pendiente-solicitantes" maxlength="80" autocomplete="off" class="form-control" value="<?= html_escape($solicitante) ?>">
        <datalist id="pendiente-solicitantes"><?php foreach($solicitantes as $s): ?><option value="<?= html_escape($s) ?>"><?php endforeach; ?></datalist></div>
    <div class="col-md-12 form-group"><label for="pendiente-detalle">Pendiente</label><textarea id="pendiente-detalle" name="detalle" rows="4" maxlength="3000" required class="form-control" placeholder="Qué se necesita, para quién, tallas o cantidades"><?= html_escape($v('detalle')) ?></textarea></div>
    <?php if($puede_gestionar): ?>
    <div class="col-md-4 form-group"><label for="pendiente-estado">Estado</label><select id="pendiente-estado" name="estado" class="form-control"><?php foreach(Almacen_pendientes_model::ESTADOS as $clave=>$texto): ?><option value="<?= $clave ?>" <?= $estado===$clave?'selected':'' ?>><?= html_escape($texto) ?></option><?php endforeach; ?></select></div>
    <?php else: ?>
    <div class="col-md-4 form-group"><span class="d-block mb-2">Estado</span><span class="badge badge-secondary"><?= html_escape(Almacen_pendientes_model::ESTADOS[$estado]) ?></span><div class="small text-muted">Lo actualiza Almacén.</div></div>
    <?php endif; ?>
    <div class="col-md-8 form-group"><label for="pendiente-observaciones">Observaciones</label><textarea id="pendiente-observaciones" name="observaciones" rows="2" maxlength="500" class="form-control"><?= html_escape($v('observaciones')) ?></textarea></div>
</div>
</div>
<div class="<?= !empty($en_modal)?'modal-footer':'almacen-formulario-acciones' ?>">
<?php if(!empty($en_modal)): ?><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><?php endif; ?>
<button type="submit" class="btn btn-primary">Guardar pendiente</button>
</div>
<?= form_close() ?>
