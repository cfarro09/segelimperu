<?php defined('BASEPATH') OR exit('No direct script access allowed');
$abierto=$p->estado!=='atendido'; $dias=Almacen_pendientes_model::dias_abierto($p);
$clase_estado=array('pendiente'=>'badge-warning','en_proceso'=>'badge-info','atendido'=>'badge-success')[$p->estado]; ?>
<tr data-registro-id="<?= (int)$p->id ?>" class="<?= $p->prioridad==='urgente'&&$abierto?'pendiente-urgente':'' ?>">
    <td data-order="<?= html_escape($p->fecha) ?>"><?= html_escape(date('d/m/Y',strtotime($p->fecha))) ?></td>
    <td><?= html_escape(Almacen_pendientes_model::TIPOS[$p->tipo]) ?></td>
    <td><?= html_escape($p->sede) ?></td>
    <td class="pendiente-detalle"><?= nl2br(html_escape($p->detalle)) ?></td>
    <td><?= html_escape($p->solicitante) ?></td>
    <td><?= $p->prioridad==='urgente'?'<span class="badge badge-danger">Urgente</span>':'Normal' ?></td>
    <td><span class="badge <?= $clase_estado ?>"><?= html_escape(Almacen_pendientes_model::ESTADOS[$p->estado]) ?></span><?php if(!$abierto && $p->atendido_en): ?><div class="small text-muted"><?= html_escape(date('d/m/Y',strtotime($p->atendido_en))) ?><?= trim($p->atendido_por_nombre ?? '')!==''?' · '.html_escape($p->atendido_por_nombre):'' ?></div><?php endif; ?></td>
    <td class="text-right" data-order="<?= $dias===null?-1:$dias ?>"><?= $dias===null?'—':($dias===0?'Hoy':$dias) ?></td>
    <td class="small"><?= nl2br(html_escape($p->observaciones)) ?></td>
    <td class="almacen-celda-acciones"><div class="almacen-acciones-iconos">
        <a class="btn btn-light" href="<?= site_url('almacen/pendiente/'.$p->id) ?>" data-almacen-formulario="pendiente" data-editor-titulo="Editar pendiente" data-almacen-tooltip title="Editar pendiente" aria-label="Editar pendiente" aria-haspopup="dialog"><i class="mdi mdi-pencil" aria-hidden="true"></i></a>
        <?php if(!empty($puede_gestionar) && $abierto): ?>
        <?= form_open('almacen/atender_pendiente',array('class'=>'d-inline','data-pendiente-atender'=>'1','data-sede'=>$p->sede,'data-detalle'=>mb_substr($p->detalle,0,160))) ?><input type="hidden" name="token" value="<?= html_escape($token) ?>"><input type="hidden" name="id" value="<?= (int)$p->id ?>"><input type="hidden" name="vista" value="<?= html_escape($vista ?? 'abiertos') ?>"><button class="btn btn-light text-success" data-almacen-tooltip title="Marcar atendido" aria-label="Marcar atendido"><i class="mdi mdi-check-bold" aria-hidden="true"></i></button><?= form_close() ?>
        <?php endif; ?>
    </div></td>
</tr>
