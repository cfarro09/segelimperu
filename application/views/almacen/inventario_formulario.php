<?php defined('BASEPATH') OR exit('No direct script access allowed'); $vista_inventario=$vista_inventario ?? 'completo'; $ver_movimiento=$vista_inventario!=='historial'; $ver_historial=$vista_inventario!=='movimiento'; ?>
<?= form_open('almacen/movimiento',array('id'=>'form-movimiento','data-almacen-edicion'=>'inventario','data-inventario-vista'=>$vista_inventario,'data-guardar-formulario'=>!empty($en_modal)?'0':'1','data-stock'=>(string)$producto->stock,'data-stock-confirmado'=>$producto->stock_confirmado?'1':'0','data-sin-stock'=>!$producto->stock_confirmado||(float)$producto->stock==0?'1':'0','data-producto'=>$producto->descripcion,'data-unidad'=>$producto->unidad)) ?>
<div class="<?= !empty($en_modal)?'modal-body':'almacen-formulario-campos' ?>">
    <div class="alert alert-danger" data-formulario-errores role="alert" tabindex="-1" hidden></div>
    <div class="alert alert-success" data-formulario-ok role="status" tabindex="-1" hidden></div>
    <?php if(!empty($en_modal)): ?><h6 class="almacen-inventario-producto"><?= html_escape($producto->descripcion) ?></h6><?php endif; ?>
    <div class="almacen-inventario-datos"><span><?= html_escape($producto->codigo) ?> · <?= html_escape($producto->marca) ?> · <?= html_escape($producto->unidad) ?></span><strong>Stock actual: <?= $producto->stock_confirmado?number_format($producto->stock,3).' '.html_escape($producto->unidad):'Por verificar' ?></strong></div>
    <?php if($ver_movimiento && !$producto->stock_confirmado): ?><div class="alert alert-warning">Stock pendiente: registra un <strong>conteo físico</strong>.</div><?php endif; ?>
    <input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="id" value="<?= (int)$producto->id ?>">
    <?php if($ver_movimiento): if($producto->activo): ?>
    <div data-movimiento-campos>
        <div class="row">
            <div class="col-md-4 form-group"><label for="tipo">Movimiento</label><select id="tipo" name="tipo" class="form-control" required><option value="">Selecciona un movimiento</option><?php if($producto->stock_confirmado): ?><option value="entrada">Entrada</option><option value="salida">Salida</option><?php endif; ?><option value="ajuste">Conteo físico (stock final)</option></select></div>
            <div class="col-md-3 form-group"><label id="cantidad-etiqueta" for="cantidad">Cantidad (<?= html_escape($producto->unidad) ?>)</label><input id="cantidad" name="cantidad" type="number" inputmode="decimal" min="0" max="999999999.999" step="0.001" required class="form-control"></div>
            <div class="col-md-5 form-group"><label for="motivo" id="motivo-etiqueta">Motivo / referencia</label><input id="motivo" name="motivo" maxlength="255" required class="form-control" placeholder="Entrega o guía de compra"></div>
        </div>
        <div class="row" data-campos-entrada hidden>
            <div class="col-md-7 form-group"><label for="proveedor">Proveedor <span class="text-muted">(opcional)</span></label><input id="proveedor" name="proveedor" maxlength="200" class="form-control" autocomplete="off" disabled></div>
            <div class="col-md-5 form-group"><label for="factura">N° de factura <span class="text-muted">(opcional)</span></label><input id="factura" name="factura" maxlength="40" class="form-control" autocomplete="off" placeholder="F001-000123" disabled></div>
        </div>
        <div id="movimiento-resumen" class="almacen-movimiento-resumen" aria-live="polite" hidden><strong>Stock final: <span id="stock-final-previsto"></span></strong><p id="movimiento-exceso" class="text-danger mt-2" hidden>La salida supera el stock disponible.</p></div>
    </div>
    <?php if(!empty($en_modal)): ?><div data-movimiento-revision hidden tabindex="-1"><h6 data-movimiento-titulo></h6><dl class="almacen-movimiento-detalles" data-movimiento-detalles></dl></div><?php endif; ?>
    <?php else: ?><div class="alert alert-warning">Activa el producto para registrar movimientos.</div><?php endif; endif; ?>
    <?php if($ver_historial): ?>
    <?php if($ver_movimiento): ?><details data-inventario-historial class="almacen-inventario-historial" <?= empty($en_modal)?'open':'' ?>><summary>Historial de movimientos (<?= count($movimientos) ?>)</summary><?php else: ?><div data-inventario-historial><?php endif; ?>
        <div class="table-responsive"><table class="table table-striped mb-0">
            <thead><tr><th>Fecha</th><th>Tipo</th><th>Variación</th><th>Stock anterior</th><th>Stock final</th><th>Motivo</th><th>ID usuario</th></tr></thead><tbody>
            <?php foreach($movimientos as $m): ?><tr><td><?= html_escape($m->creado_en) ?></td><td><?= $m->tipo==='ajuste'?'Conteo físico':html_escape(ucfirst($m->tipo)) ?></td><td class="text-right"><?= number_format($m->cantidad,3) ?></td><td class="text-right"><?= number_format($m->stock_anterior,3) ?></td><td class="text-right"><?= number_format($m->stock_nuevo,3) ?></td><td><?= html_escape($m->motivo) ?><?php if(($m->proveedor ?? '')!=='' || ($m->factura ?? '')!==''): ?><div class="small text-muted"><?= ($m->proveedor ?? '')!==''?'Proveedor: '.html_escape($m->proveedor):'' ?><?= ($m->proveedor ?? '')!=='' && ($m->factura ?? '')!==''?' · ':'' ?><?= ($m->factura ?? '')!==''?'Factura: '.html_escape($m->factura):'' ?></div><?php endif; ?></td><td class="text-right"><?= (int)$m->usuario_id ?></td></tr><?php endforeach; ?>
            <?php if(!$movimientos): ?><tr><td colspan="7">Sin movimientos registrados.</td></tr><?php endif; ?>
            </tbody>
        </table></div>
    <?php if($ver_movimiento): ?></details><?php else: ?></div><?php endif; ?>
    <?php endif; ?>
</div>
<div class="<?= !empty($en_modal)?'modal-footer':'almacen-formulario-acciones' ?>">
    <?php if(!empty($en_modal)): ?><button type="button" class="btn btn-light" data-dismiss="modal"><?= $ver_movimiento && $producto->activo?'Cancelar':'Cerrar' ?></button><?php if($ver_movimiento && $producto->activo): ?><button type="button" class="btn btn-light" data-movimiento-editar hidden>Corregir</button><?php endif; ?><?php endif; ?>
    <?php if($ver_movimiento && $producto->activo): ?><button class="btn btn-primary" type="submit">Registrar movimiento</button><?php endif; ?>
</div>
<?= form_close() ?>
