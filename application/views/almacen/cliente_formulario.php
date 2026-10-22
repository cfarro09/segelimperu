<?php defined('BASEPATH') OR exit('No direct script access allowed');
$nuevo=!$cliente||!$cliente->id; $bloqueado=isset($puede_editar_presupuesto) && !$puede_editar_presupuesto; $solo_lectura=$bloqueado?' readonly':'';
$valor=function($campo,$defecto='') use ($cliente) { return html_escape($cliente&&isset($cliente->$campo)?$cliente->$campo:$defecto); };
$horarios=$cliente&&!empty($cliente->horarios)?$cliente->horarios:array();
$modalidad=$cliente&&isset($cliente->modalidad)?$cliente->modalidad:'';
$con_material=!$cliente||!isset($cliente->con_material)||(int)$cliente->con_material===1;
// Una fila de horario: días con casillas, hora de entrada/salida y detalle opcional. $i = índice o __i__ en la plantilla.
$fila_horario=function($i,$h=null) {
    $dias_fila=$h?(array)$h->dias:array(); ?>
    <div class="horario-fila" data-horario>
        <fieldset class="horario-dias"><legend class="sr-only">Días del horario</legend>
            <?php foreach(Almacen_clientes_model::DIAS as $clave=>$nombre): ?><label class="horario-dia" title="<?= html_escape($nombre) ?>"><input type="checkbox" name="horarios[<?= $i ?>][dias][]" value="<?= $clave ?>" <?= in_array($clave,$dias_fila,true)?'checked':'' ?>><span aria-hidden="true"><?= html_escape(mb_substr($nombre,0,1)==='M'&&$clave==='mie'?'X':mb_substr($nombre,0,1)) ?></span><span class="sr-only"><?= html_escape($nombre) ?></span></label><?php endforeach; ?>
            <span class="horario-atajos"><button type="button" class="btn btn-link btn-sm" data-dias="lun,mar,mie,jue,vie">L–V</button><button type="button" class="btn btn-link btn-sm" data-dias="lun,mar,mie,jue,vie,sab">L–S</button><button type="button" class="btn btn-link btn-sm" data-dias="lun,mar,mie,jue,vie,sab,dom">Todos</button></span>
        </fieldset>
        <div class="horario-horas">
            <label>Entrada <input type="time" class="form-control form-control-sm" name="horarios[<?= $i ?>][entrada]" step="900" required value="<?= html_escape($h?$h->entrada:'') ?>"></label>
            <label>Salida <input type="time" class="form-control form-control-sm" name="horarios[<?= $i ?>][salida]" step="900" required value="<?= html_escape($h?$h->salida:'') ?>"></label>
        </div>
        <input type="text" class="form-control form-control-sm horario-detalle" name="horarios[<?= $i ?>][detalle]" maxlength="80" placeholder="Detalle (opcional): sede, turno" aria-label="Detalle del horario" value="<?= html_escape($h?$h->detalle:'') ?>">
        <button type="button" class="btn btn-light btn-sm" data-horario-quitar aria-label="Quitar este horario" title="Quitar horario"><i class="mdi mdi-delete" aria-hidden="true"></i></button>
    </div>
<?php }; ?>
<?= form_open('almacen/guardar_cliente',array('data-almacen-edicion'=>'cliente','id'=>'form-cliente')) ?>
<div class="<?= !empty($en_modal)?'modal-body':'almacen-formulario-campos' ?>">
<div class="alert alert-danger" data-formulario-errores role="alert" tabindex="-1" <?= !$errores?'hidden':'' ?>><?php if($errores): ?><ul class="mb-0"><?php foreach($errores as $error): ?><li><?= html_escape($error) ?></li><?php endforeach; ?></ul><?php endif; ?></div>
<input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="id" value="<?= $nuevo?0:(int)$cliente->id ?>">

<h5 class="cliente-seccion">Cliente</h5>
<div class="row">
    <div class="col-md-4 form-group"><label for="codigo">Código</label><input id="codigo" name="codigo" maxlength="40" class="form-control" placeholder="Automático (CLI-0001…) si se deja vacío" value="<?= $valor('codigo') ?>"></div>
    <div class="col-md-8 form-group"><label for="nombre">Nombre del cliente</label><input id="nombre" name="nombre" maxlength="255" class="form-control" required value="<?= $valor('nombre') ?>"></div>
    <div class="col-md-4 form-group"><label for="ruc">RUC <span class="text-muted">(opcional)</span></label><input id="ruc" name="ruc" maxlength="11" inputmode="numeric" pattern="\d{11}" title="11 dígitos" class="form-control" value="<?= $valor('ruc') ?>"></div>
    <div class="col-md-4 form-group"><label for="grupo">Unidad asignada</label><input id="grupo" name="grupo" maxlength="80" class="form-control" value="<?= $valor('grupo') ?>"></div>
    <?php $supervisor_id=$cliente&&isset($cliente->supervisor_id)?(int)$cliente->supervisor_id:0; ?>
    <div class="col-md-4 form-group"><label for="supervisor_id">Supervisor responsable</label><select id="supervisor_id" name="supervisor_id" class="form-control"><option value="">Sin asignar</option><?php foreach($supervisores as $s): ?><option value="<?= (int)$s->id_usu ?>" <?= $supervisor_id===(int)$s->id_usu?'selected':'' ?>><?= html_escape(trim($s->nombre_usu.' '.$s->apellido_pat)) ?><?= $s->estado_usu!=='AC'?' (inactivo)':'' ?></option><?php endforeach; ?></select></div>
</div>

<h5 class="cliente-seccion">Contacto</h5>
<div class="row">
    <div class="col-md-4 form-group"><label for="contacto">Persona de contacto</label><input id="contacto" name="contacto" maxlength="150" class="form-control" autocomplete="off" value="<?= $valor('contacto') ?>"></div>
    <div class="col-md-4 form-group"><label for="telefono">Teléfono</label><input id="telefono" name="telefono" type="tel" maxlength="30" pattern="[0-9+() \-]{6,30}" title="Números, espacios, +, guiones o paréntesis" class="form-control" autocomplete="off" value="<?= $valor('telefono') ?>"></div>
    <div class="col-md-4 form-group"><label for="correo">Correo</label><input id="correo" name="correo" type="email" maxlength="150" class="form-control" autocomplete="off" value="<?= $valor('correo') ?>"></div>
    <div class="col-md-12 form-group"><label for="direccion">Dirección de entrega</label><input id="direccion" name="direccion" maxlength="255" class="form-control" value="<?= $valor('direccion') ?>"></div>
</div>

<h5 class="cliente-seccion">Servicio</h5>
<div class="row">
    <div class="col-md-4 form-group"><label for="modalidad">Modalidad</label><select id="modalidad" name="modalidad" class="form-control"><option value="">Sin definir</option><?php foreach(Almacen_clientes_model::MODALIDADES as $clave=>$texto): ?><option value="<?= html_escape($clave) ?>" <?= $modalidad===$clave?'selected':'' ?>><?= html_escape($texto) ?></option><?php endforeach; ?><?php if($modalidad!=='' && !isset(Almacen_clientes_model::MODALIDADES[$modalidad])): ?><option value="" selected><?= html_escape($modalidad) ?> (corregir)</option><?php endif; ?></select></div>
    <div class="col-md-8 form-group"><span class="d-block mb-2" id="material-etiqueta">Material</span>
        <div class="btn-group btn-group-toggle cliente-material" data-toggle="buttons" role="radiogroup" aria-labelledby="material-etiqueta">
            <label class="btn btn-outline-primary btn-sm <?= $con_material?'active':'' ?>"><input type="radio" name="con_material" value="1" <?= $con_material?'checked':'' ?>> Con material</label>
            <label class="btn btn-outline-primary btn-sm <?= !$con_material?'active':'' ?>"><input type="radio" name="con_material" value="0" <?= !$con_material?'checked':'' ?>> Sin material</label>
        </div>
    </div>
</div>
<div class="form-group" data-horarios data-siguiente="<?= count($horarios) ?>">
    <span class="d-block mb-1">Horarios <span class="text-muted small" data-horario-resumen><?= html_escape(Almacen_clientes_model::resumen_horarios($horarios)) ?></span></span>
    <div data-horario-lista><?php foreach($horarios as $i=>$h) $fila_horario($i,$h); ?></div>
    <p class="small text-muted mb-1" data-horario-vacio <?= $horarios?'hidden':'' ?>>Sin horarios registrados.</p>
    <button type="button" class="btn btn-outline-primary btn-sm" data-horario-agregar><i class="mdi mdi-plus" aria-hidden="true"></i> Agregar horario</button>
    <template data-horario-plantilla><?php $fila_horario('__i__'); ?></template>
</div>
<div class="form-group"><label for="observaciones_servicio">Observaciones del servicio</label><input id="observaciones_servicio" name="observaciones_servicio" maxlength="255" class="form-control" value="<?= $valor('observaciones_servicio') ?>"></div>

<h5 class="cliente-seccion">Presupuesto</h5>
<?php if($bloqueado): ?><p class="small text-muted mb-2"><i class="mdi mdi-lock" aria-hidden="true"></i> Presupuesto, saldo y estado los gestiona Administración.</p><?php endif; ?>
<div class="row">
<div class="col-md-4 form-group"><label for="presupuesto">Presupuesto máximo mensual (S/)</label><input id="presupuesto" name="presupuesto" type="number" min="0" max="999999999.99" step="0.01" required class="form-control"<?= $solo_lectura ?> value="<?= html_escape($cliente?$cliente->presupuesto:'0') ?>"></div>
<div class="col-md-4 form-group"><label for="saldo_inicial">Saldo acumulado inicial (S/)</label><input id="saldo_inicial" name="saldo_inicial" type="number" min="-999999999.99" max="999999999.99" step="0.01" required class="form-control"<?= $solo_lectura ?> value="<?= html_escape($cliente?$cliente->saldo_inicial:'0') ?>"></div>
<div class="col-md-4 form-group"><label for="activo">Estado</label><select id="activo" name="activo" class="form-control"<?= $bloqueado?' disabled':'' ?>><option value="1" <?= !$cliente||$cliente->activo?'selected':'' ?>>Activo</option><option value="0" <?= $cliente&&!$cliente->activo?'selected':'' ?>>Inactivo</option></select></div>
<div class="col-md-12 form-group" data-documento-baja><label for="documento_baja">Documento de baja (para inactivos)</label><input id="documento_baja" name="documento_baja" maxlength="255"<?= $solo_lectura ?> <?= $cliente && !$cliente->activo?'required':'' ?> class="form-control" value="<?= html_escape($cliente?$cliente->documento_baja:'') ?>"></div></div>

</div>
<div class="<?= !empty($en_modal)?'modal-footer':'almacen-formulario-acciones' ?>">
<?php if(!empty($en_modal)): ?><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><?php endif; ?>
<button type="submit" class="btn btn-primary">Guardar cliente</button>
</div>
<?= form_close() ?>
