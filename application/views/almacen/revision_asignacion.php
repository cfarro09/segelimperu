<?php defined('BASEPATH') OR exit('No direct script access allowed');
$editable = !empty($puede_ajustar_cantidades);
$acciones = array(
    array('texto'=>'Volver','icono'=>'mdi-arrow-left','clase'=>'btn-light','aria'=>'Volver a revisión','url'=>site_url('almacen/revision_asignaciones').'?periodo='.rawurlencode($asignacion->periodo)),
    array('texto'=>'Imprimir','icono'=>'mdi-printer','url'=>site_url('almacen/imprimir_asignacion/'.$asignacion->id),'nueva_pestana'=>true,'documento'=>true),
    array('texto'=>'Excel','icono'=>'mdi-file-excel','url'=>site_url('almacen/exportar_asignacion/'.$asignacion->id),'aria'=>'Exportar asignación a Excel','documento'=>true)
);
if ($editable) array_splice($acciones, 1, 0, array(array('texto'=>'Guardar','aria'=>'Guardar cantidades','icono'=>'mdi-content-save','clase'=>'btn-primary','form'=>'form-revision-cantidades')));
?>
<div class="content"><div class="container-fluid">
<?php $this->load->view('almacen/cabecera', array('cabecera_titulo'=>'Revisar asignación · '.$cliente->nombre.' · '.$asignacion->periodo, 'cabecera_acciones'=>$acciones)); ?>
<?php foreach(array('almacen_ok'=>'success','almacen_error'=>'danger') as $key=>$clase): if($mensaje=$this->session->flashdata($key)): ?>
<div class="alert alert-<?= $clase ?>" role="alert"><?= html_escape($mensaje) ?></div>
<?php endif; endforeach; ?>
<?php if(!empty($errores)): ?><div class="alert alert-danger" role="alert"><?php foreach($errores as $error): ?><div><?= html_escape($error) ?></div><?php endforeach; ?></div><?php endif; ?>
<div class="card-box">
    <div class="almacen-revision-resumen">
        <p class="mb-0">Estado: <?php $this->load->view('almacen/estado_asignacion',array('estado'=>$asignacion->estado)); ?> <span data-despacho-estado><?php if ($despacho ?? null): ?><span class="badge badge-info">Despachada</span><?php endif; ?></span> · Publicado: <?= html_escape($asignacion->publicado_en) ?></p>
        <div class="revision-decision-acciones">
            <?php if($asignacion->estado==='publicado'): ?>
            <?= form_open('almacen/decidir_asignacion',array('id'=>'form-decision-revision','class'=>'revision-decision','data-exceso'=>max(0,$asignacion->total-$asignacion->presupuesto))) ?>
            <input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="id" value="<?= (int)$asignacion->id ?>"><input type="hidden" name="revision" value="<?= (int)$asignacion->revision ?>">
            <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#modal-rechazo"><i class="mdi mdi-close" aria-hidden="true"></i> Rechazar</button>
            <button type="submit" class="btn btn-sm btn-success" name="decision" value="aprobado" data-aprobar><i class="mdi mdi-check" aria-hidden="true"></i> Aprobar</button>
            <?= form_close() ?>
            <?php endif; ?>
            <div data-despacho-acciones><?php $this->load->view('almacen/despacho_acciones',array('asignacion'=>$asignacion,'despacho'=>$despacho ?? null)); ?></div>
        </div>
    </div>
    <?php $saldo_proyectado=$saldo_anterior+$asignacion->presupuesto-$asignacion->total; ?>
    <dl class="revision-montos">
        <div><dt>Presupuesto máximo</dt><dd>S/ <?= number_format($asignacion->presupuesto,2) ?></dd></div>
        <div><dt>Total solicitado</dt><dd>S/ <span id="revision-total"><?= number_format($asignacion->total,2) ?></span>
            <span class="badge badge-danger revision-exceso" id="revision-exceso" <?= $asignacion->total>$asignacion->presupuesto?'':'hidden' ?>>Excede S/ <span><?= number_format(max(0,$asignacion->total-$asignacion->presupuesto),2) ?></span></span></dd></div>
        <div><dt>Saldo acumulado proyectado</dt><dd>S/ <span id="revision-saldo"<?= $saldo_proyectado<0?' class="text-danger"':'' ?>><?= number_format($saldo_proyectado,2) ?></span></dd></div>
    </dl>
    <?php if($editable): ?>
    <?= form_open('almacen/guardar_revision_asignacion',array('id'=>'form-revision-cantidades','data-guardar-formulario'=>'1','data-error-inicial'=>!empty($errores)?'1':'0','data-presupuesto'=>$asignacion->presupuesto,'data-saldo-anterior'=>$saldo_anterior)) ?>
    <input type="hidden" name="token" value="<?= html_escape($token) ?>">
    <input type="hidden" name="id" value="<?= (int)$asignacion->id ?>">
    <input type="hidden" name="revision" value="<?= (int)($revision_formulario ?? $asignacion->revision) ?>">
    <p id="estado-revision" class="small mb-2" role="status" aria-live="polite">Cantidades guardadas</p>
    <?php elseif(!empty($restriccion_ajuste)): ?><p class="small text-muted mb-2"><?= html_escape($restriccion_ajuste) ?></p><?php endif; ?>
    <div class="table-responsive"><table data-paginar class="table table-bordered table-sm revision-cantidades-tabla<?= $editable?' revision-editable':'' ?>">
        <thead><tr><th>Categoría</th><th>Descripción</th><th>Unidad</th><th>Marca</th><th>Cantidad</th><th>Precio valorado S/</th><th>Total S/</th></tr></thead>
        <tbody><?php foreach($detalles as $d): ?>
        <tr data-precio="<?= html_escape($d->precio_valorado) ?>">
            <td data-label="Categoría"><?= html_escape($d->categoria) ?></td><td class="revision-producto"><?= html_escape($d->descripcion) ?></td><td data-label="Unidad"><?= html_escape($d->unidad) ?></td><td data-label="Marca"><?= html_escape($d->marca) ?></td>
            <td class="text-right" data-label="Cantidad"><?php if($editable):
                $valor = isset($cantidades_revision[$d->producto_id]) ? $cantidades_revision[$d->producto_id] : number_format($d->cantidad,3,'.','');
                $invalido = $valor!=='' && !preg_match('/^[0-9]{1,9}(?:[.,][0-9]{1,3})?$/D', $valor);
            ?>
                <label class="sr-only" for="revision-cantidad-<?= (int)$d->producto_id ?>">Cantidad de <?= html_escape($d->descripcion) ?> (<?= html_escape($d->unidad) ?>)</label>
                <input class="form-control form-control-sm revision-cantidad text-right<?= $invalido?' is-invalid':'' ?>" id="revision-cantidad-<?= (int)$d->producto_id ?>" name="cantidades[<?= (int)$d->producto_id ?>]" value="<?= html_escape($valor) ?>" inputmode="decimal" pattern="[0-9]{1,9}([.,][0-9]{1,3})?" maxlength="13" autocomplete="off">
            <?php else: ?><?= number_format($d->cantidad,3) ?><?php endif; ?></td>
            <td class="text-right" data-label="Precio S/"><span><?= number_format($d->precio_valorado,2) ?></span></td>
            <td class="text-right" data-label="Total S/"><strong data-revision-importe><?= number_format($d->total,2) ?></strong></td>
        </tr><?php endforeach; ?></tbody>
    </table></div>
    <?php if($editable): ?><?= form_close() ?><?php endif; ?>
    <?php if($asignacion->observaciones!==''): ?><p><strong>Observaciones:</strong> <?= nl2br(html_escape($asignacion->observaciones)) ?></p><?php endif; ?>
    <?php if($asignacion->estado!=='publicado' && $asignacion->comentario_revision!==''): ?><p><strong>Comentario de revisión:</strong> <?= nl2br(html_escape($asignacion->comentario_revision)) ?></p><?php endif; ?>
</div>
<div class="card-box"><h4 class="header-title">Historial</h4><div class="table-responsive"><table class="table"><thead><tr><th>Fecha</th><th>Acción</th><th>Usuario</th><th>Comentario</th></tr></thead><tbody>
<?php foreach($eventos as $e): ?><tr><td><?= html_escape($e->creado_en) ?></td><td><?= html_escape($e->estado_anterior===$e->estado_nuevo && strpos($e->comentario,'Ajuste de cantidades:')===0 ? 'Cantidades ajustadas' : ucfirst($e->estado_anterior?:'Nuevo').' → '.ucfirst($e->estado_nuevo)) ?></td><td class="text-right"><?= (int)$e->usuario_id ?></td><td><?= html_escape($e->comentario) ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
</div></div>
<?php if($asignacion->estado==='publicado'): ?>
<div class="modal fade" id="modal-rechazo" tabindex="-1" role="dialog" aria-labelledby="modal-rechazo-titulo" aria-hidden="true"><div class="modal-dialog modal-dialog-centered" role="document"><div class="modal-content">
    <div class="modal-header"><h5 class="modal-title" id="modal-rechazo-titulo">Rechazar asignación</h5><button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button></div>
    <div class="modal-body"><p class="small text-muted">La asignación vuelve al supervisor para que la corrija. Indica qué debe ajustar.</p>
        <label for="comentario">Motivo del rechazo</label><textarea id="comentario" name="comentario" form="form-decision-revision" class="form-control" maxlength="1000" rows="3"></textarea></div>
    <div class="modal-footer"><button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button><button type="submit" form="form-decision-revision" class="btn btn-danger" name="decision" value="rechazado">Rechazar asignación</button></div>
</div></div></div>
<?php endif; ?>
<?php $this->load->view('almacen/formulario_modal'); ?>
