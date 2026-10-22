<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="almacen-cabecera <?= !empty($cabecera_asignacion)?'asignacion-top':'' ?>">
    <h4><?= html_escape($cabecera_titulo) ?><?php if(!empty($cabecera_estado)): ?> <?php $this->load->view('almacen/estado_asignacion', array('estado'=>$cabecera_estado)); endif; ?><?php if(!empty($cabecera_solo_lectura)): ?> <span class="badge badge-secondary">Solo lectura</span><?php endif; ?></h4>
    <?php if(!empty($cabecera_acciones)): ?><div class="almacen-acciones" role="group" aria-label="Acciones de la pantalla">
    <?php foreach($cabecera_acciones as $accion): $es_enlace=isset($accion['url']); ?>
        <<?= $es_enlace?'a':'button' ?> class="btn <?= html_escape($accion['clase'] ?? 'btn-outline-secondary') ?>" <?php if($es_enlace): ?>href="<?= html_escape($accion['url']) ?>"<?php else: ?>type="submit" form="<?= html_escape($accion['form']) ?>"<?php if(isset($accion['name'])): ?> name="<?= html_escape($accion['name']) ?>" value="<?= html_escape($accion['value']) ?>"<?php endif; ?><?php endif; ?><?php if(!empty($accion['formulario'])): ?> data-almacen-formulario="<?= html_escape($accion['formulario']) ?>" data-editor-titulo="<?= html_escape($accion['texto']) ?>" aria-haspopup="dialog"<?php endif; ?><?php if(!empty($accion['documento'])): ?> data-documento-guardado<?php endif; ?><?php if(!empty($accion['nueva_pestana'])): ?> target="_blank" rel="noopener"<?php endif; ?><?php if(!empty($accion['aria'])): ?> aria-label="<?= html_escape($accion['aria']) ?>"<?php endif; ?><?php if(!empty($accion['disabled'])): ?> disabled<?php endif; ?>>
            <i class="mdi <?= html_escape($accion['icono']) ?>" aria-hidden="true"></i><span><?= html_escape($accion['texto']) ?></span>
        </<?= $es_enlace?'a':'button' ?>>
    <?php endforeach; ?>
    </div><?php endif; ?>
</div>
