<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<table class="table table-sm table-bordered compra-tabla-clientes">
    <caption class="sr-only">Cantidades solicitadas por producto y cliente</caption>
    <thead><tr>
        <th scope="col">Producto</th><th scope="col">Unidad</th><th scope="col">Marca</th>
        <?php foreach($asignaciones as $a): ?><th scope="col" class="compra-cliente"><?= html_escape($a->nombre) ?></th><?php endforeach; ?>
        <th scope="col">Total solicitado</th>
    </tr></thead>
    <tbody>
    <?php foreach($detalles as $d): ?>
        <tr data-producto-id="<?= (int)$d->producto_id ?>">
            <th scope="row" class="compra-producto"><?= html_escape($d->descripcion) ?></th>
            <td><?= html_escape($d->unidad) ?></td><td><?= html_escape($d->marca) ?></td>
            <?php foreach($asignaciones as $a): ?><td class="text-right" data-cliente-id="<?= (int)$a->cliente_id ?>"><?= number_format($cantidades_clientes[$d->producto_id][$a->cliente_id] ?? 0,3) ?></td><?php endforeach; ?>
            <td class="text-right font-weight-bold"><?= number_format($d->solicitado,3) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
