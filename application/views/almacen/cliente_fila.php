<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<tr data-registro-id="<?= (int)$c->id ?>"><td><?= html_escape($c->codigo) ?></td><td><?= html_escape($c->nombre) ?></td><td><?= html_escape($c->grupo) ?></td><td><?= html_escape($c->supervisor) ?></td><td class="small"><?= html_escape(Almacen_clientes_model::resumen_horarios($c->horarios ?? array())) ?></td><td><?= isset($c->con_material)&&!(int)$c->con_material?'Sin material':'Con material' ?></td><td class="text-right"><?= number_format($c->presupuesto,2) ?></td><td class="text-right"><?= number_format($c->saldo_inicial,2) ?></td><td><?= $c->activo?'Activo':'<span class="badge badge-secondary">Inactivo</span>' ?></td>
    <td class="almacen-celda-acciones"><div class="almacen-acciones-iconos">
        <a class="btn btn-light" href="<?= site_url('almacen/cliente/'.$c->id) ?>" data-almacen-formulario="cliente" data-editor-titulo="Editar cliente" data-almacen-tooltip title="Editar cliente" aria-label="Editar cliente" aria-haspopup="dialog"><i class="mdi mdi-pencil" aria-hidden="true"></i></a>
        <?php if(Almacen_acceso::permite($this->session->userdata('tipo_usu'),'almacen','asignaciones')): ?>
        <a class="btn btn-light" href="<?= site_url('almacen/asignaciones').'?cliente_id='.(int)$c->id ?>" data-cliente-asignaciones data-cliente-id="<?= (int)$c->id ?>" data-cliente-nombre="<?= html_escape($c->nombre) ?>" data-almacen-tooltip title="Asignaciones" aria-label="Asignaciones" aria-haspopup="dialog" aria-controls="almacen-asignaciones"><i class="mdi mdi-calendar-text" aria-hidden="true"></i></a>
        <?php if($c->activo): ?><a class="btn btn-light" href="<?= site_url('almacen/asignacion').'?cliente_id='.(int)$c->id ?>" data-almacen-tooltip title="Asignar mes" aria-label="Asignar mes"><i class="mdi mdi-calendar-plus" aria-hidden="true"></i></a><?php endif; ?>
        <?php endif; ?>
    </div></td>
</tr>
