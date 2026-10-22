<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content"><div class="container-fluid">
<?php $this->load->view('almacen/cabecera',array('cabecera_titulo'=>'Despachar asignación','cabecera_acciones'=>array(array('texto'=>'Volver','icono'=>'mdi-arrow-left','clase'=>'btn-light','url'=>site_url('almacen/revisar_asignacion/'.$asignacion->id))))); ?>
<?php foreach (array('almacen_ok'=>'success','almacen_error'=>'danger') as $key=>$clase): if ($mensaje=$this->session->flashdata($key)): ?><div class="alert alert-<?= $clase ?>" role="alert"><?= html_escape($mensaje) ?></div><?php endif; endforeach; ?>
<div class="card-box"><?php $this->load->view('almacen/despacho_formulario',get_defined_vars()); ?></div>
</div></div>
