<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$meses = array(1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
$mes = ($meses[(int)substr($asignacion->periodo,5,2)] ?? '').' '.substr($asignacion->periodo,0,4);
$diferencia = $asignacion->presupuesto - $asignacion->total;
?>
<section data-asignacion-consulta data-asignacion-id="<?= (int)$asignacion->id ?>" data-cliente-id="<?= (int)$cliente->id ?>" aria-label="<?= html_escape($mes) ?>">
    <div class="almacen-asignacion-consulta-cabecera">
        <div><h5><?= html_escape($mes) ?></h5><?php $this->load->view('almacen/estado_asignacion',array('estado'=>$asignacion->estado)); ?></div>
        <a class="btn btn-light" href="<?= site_url('almacen/imprimir_asignacion/'.$asignacion->id) ?>" target="_blank" rel="noopener"><i class="mdi mdi-printer" aria-hidden="true"></i> Imprimir</a>
    </div>
    <dl class="almacen-asignacion-consulta-cifras">
        <div><dt>Presupuesto</dt><dd>S/ <?= number_format($asignacion->presupuesto,2) ?></dd></div>
        <div><dt>Total solicitado</dt><dd>S/ <?= number_format($asignacion->total,2) ?></dd></div>
        <div class="<?= $diferencia<0?'text-danger':'' ?>"><dt>Diferencia del mes</dt><dd>S/ <?= number_format($diferencia,2) ?></dd></div>
        <div class="<?= $saldo_anterior+$diferencia<0?'text-danger':'' ?>"><dt>Saldo acumulado</dt><dd>S/ <?= number_format($saldo_anterior+$diferencia,2) ?></dd></div>
    </dl>
    <?php if (!$detalles): ?><p role="status">Sin productos registrados.</p><?php else: ?>
    <div class="table-responsive almacen-asignacion-consulta-contenedor"><table class="table table-bordered almacen-asignacion-consulta-tabla mb-0" aria-label="Productos de <?= html_escape($mes) ?>">
        <thead><tr><th>Producto</th><th>Unidad</th><th>Marca</th><th>Cantidad</th><th>Precio S/</th><th>Importe S/</th></tr></thead>
        <tbody>
        <?php $categoria=null; foreach ($detalles as $d): if ($categoria!==$d->categoria): $categoria=$d->categoria; ?>
            <tr class="almacen-consulta-categoria"><th colspan="6" scope="rowgroup"><?= html_escape($categoria) ?></th></tr>
        <?php endif; ?>
            <tr class="almacen-consulta-producto">
                <td class="almacen-consulta-descripcion"><?= html_escape($d->descripcion) ?></td>
                <td class="almacen-consulta-unidad"><span class="almacen-consulta-etiqueta">Unidad:</span> <?= html_escape($d->unidad) ?></td>
                <td class="almacen-consulta-marca"><span class="almacen-consulta-etiqueta">Marca:</span> <?= html_escape($d->marca ?: 'Sin marca') ?></td>
                <td class="text-right almacen-consulta-cantidad"><span class="almacen-consulta-etiqueta">Cantidad:</span> <span><?= number_format($d->cantidad,3) ?></span></td>
                <td class="text-right almacen-consulta-precio"><span class="almacen-consulta-etiqueta">Precio S/</span> <span><?= number_format($d->precio_valorado,2) ?></span></td>
                <td class="text-right almacen-consulta-importe"><span class="almacen-consulta-etiqueta">Importe S/</span> <strong><?= number_format($d->total,2) ?></strong></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php endif; ?>
    <?php if ($asignacion->observaciones!==''): ?><p class="almacen-consulta-nota"><strong>Observaciones:</strong> <?= nl2br(html_escape($asignacion->observaciones)) ?></p><?php endif; ?>
    <?php if ($asignacion->comentario_revision!==''): ?><p class="almacen-consulta-nota"><strong>Comentario de revisión:</strong> <?= nl2br(html_escape($asignacion->comentario_revision)) ?></p><?php endif; ?>
</section>
