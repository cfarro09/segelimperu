<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$meses = array(1=>'Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
?>
<div data-cliente-asignaciones-contenido data-cliente-id="<?= (int)$cliente->id ?>" class="almacen-asignaciones-paneles">
    <?php if (!$asignaciones): ?><p class="mb-0" role="status">Este cliente aún no tiene asignaciones.</p><?php else: ?>
    <nav class="almacen-asignaciones-lista" aria-label="Asignaciones de <?= html_escape($cliente->nombre) ?>">
        <div class="list-group">
        <?php foreach ($asignaciones as $a): $mes = ($meses[(int)substr($a->periodo,5,2)] ?? '').' '.substr($a->periodo,0,4); $seleccionada = (int)$a->id === (int)$asignacion->id; ?>
            <a class="list-group-item list-group-item-action <?= $seleccionada?'active':'' ?>" href="<?= site_url('almacen/asignaciones').'?cliente_id='.(int)$cliente->id.'&amp;asignacion_id='.(int)$a->id ?>" data-asignacion-detalle data-asignacion-id="<?= (int)$a->id ?>" aria-controls="almacen-asignacion-detalle" <?= $seleccionada?'aria-current="true"':'' ?>>
                <span class="almacen-asignacion-lista-mes"><strong><?= html_escape($mes) ?></strong><span class="almacen-asignacion-ver">Ver <i class="mdi mdi-chevron-right" aria-hidden="true"></i></span></span>
                <span class="almacen-asignacion-lista-resumen"><?php $this->load->view('almacen/estado_asignacion',array('estado'=>$a->estado)); ?><span>S/ <?= number_format($a->total,2) ?></span></span>
            </a>
        <?php endforeach; ?>
        </div>
    </nav>
    <div id="almacen-asignacion-detalle" class="almacen-asignacion-detalle" role="region" aria-label="Detalle de la asignación" aria-busy="false">
        <?php $this->load->view('almacen/asignacion_consulta',array('cliente'=>$cliente,'asignacion'=>$asignacion,'detalles'=>$detalles,'saldo_anterior'=>$saldo_anterior)); ?>
    </div>
    <span class="sr-only" data-asignacion-anuncio role="status"></span>
    <?php endif; ?>
</div>
