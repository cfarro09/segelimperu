<?php defined('BASEPATH') OR exit('No direct script access allowed'); $errores_formulario=$errores_generales ?? $errores; ?>
<?= form_open('almacen/confirmar_despacho',array('data-almacen-edicion'=>'despacho')) ?>
<input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="id" value="<?= (int)$asignacion->id ?>"><input type="hidden" name="revision" value="<?= (int)$asignacion->revision ?>">
<div class="modal-body">
    <div data-formulario-errores class="alert alert-danger" role="alert" tabindex="-1" <?= !$errores_formulario || $despacho?'hidden':'' ?>><?php if (!$despacho && $errores_formulario): ?><ul class="mb-0"><?php foreach ($errores_formulario as $error): ?><li><?= html_escape($error) ?></li><?php endforeach; ?></ul><?php endif; ?></div>
    <h5 class="almacen-inventario-producto"><?= html_escape($cliente->nombre) ?></h5>
    <div class="almacen-inventario-datos"><span>Mes: <?= html_escape($asignacion->periodo) ?></span><span>Asignación #<?= (int)$asignacion->id ?></span></div>
    <?php if ($despacho): ?>
    <div class="alert alert-success" role="status">Esta asignación ya fue despachada.</div>
    <?php else: ?>
    <div class="table-responsive"><table class="table table-sm almacen-despacho-tabla"><thead><tr><th>Producto</th><th>Unidad</th><th>A entregar</th><th>Stock</th></tr></thead><tbody>
        <?php foreach ($detalles as $d): ?><tr class="<?= $d->problema!==''?'table-danger':'' ?>"><td><?= html_escape($d->descripcion) ?><?php if ($d->problema!==''): ?><small class="d-block text-danger"><?= html_escape($d->problema) ?><?= $d->faltante>0?' · Faltan '.number_format($d->faltante,3):'' ?></small><?php endif; ?></td><td><?= html_escape($d->unidad) ?></td><td class="text-right"><?= number_format($d->cantidad,3) ?></td><td class="text-right"><?= $d->stock===null?'Pendiente':number_format($d->stock,3) ?></td></tr><?php endforeach; ?>
    </tbody></table></div>
    <?php if ($puede_despachar): ?><p class="mb-0">Se descontarán del stock todas las cantidades indicadas.</p><?php endif; ?>
    <?php endif; ?>
</div>
<div class="modal-footer"><button type="button" class="btn btn-light" <?= $en_modal?'data-dismiss="modal"':'' ?> <?php if (!$en_modal): ?>onclick="location.href='<?= site_url('almacen/revisar_asignacion/'.$asignacion->id) ?>'"<?php endif; ?>><?= $puede_despachar?'Cancelar':'Cerrar' ?></button>
<?php if ($despacho): ?><a class="btn btn-primary" href="<?= site_url('almacen/guia_salida/'.$despacho->id) ?>" target="_blank" rel="noopener"><i class="mdi mdi-file-pdf" aria-hidden="true"></i> Guía PDF</a>
<?php elseif ($puede_despachar): ?><button type="submit" class="btn btn-primary"><i class="mdi mdi-truck-delivery" aria-hidden="true"></i> Confirmar despacho</button><?php endif; ?>
</div>
<?= form_close() ?>
