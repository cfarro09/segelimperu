<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php if ($despacho): ?>
<a class="btn btn-sm btn-light" href="<?= site_url('almacen/guia_salida/'.$despacho->id) ?>" target="_blank" rel="noopener"><i class="mdi mdi-file-pdf" aria-hidden="true"></i> Guía PDF</a>
<?php elseif ($asignacion->estado==='aprobado'): ?>
<a class="btn btn-sm btn-primary" href="<?= site_url('almacen/despacho/'.$asignacion->id) ?>" data-almacen-formulario="despacho" data-editor-titulo="Despachar asignación"><i class="mdi mdi-truck-delivery" aria-hidden="true"></i> Despachar</a>
<?php endif; ?>
