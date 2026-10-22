<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content"><div class="container-fluid"><?php $this->load->view('almacen/orden_compra_contenido',array('compra'=>$compra,'detalles'=>$detalles,'asignaciones'=>$asignaciones,'cantidades_clientes'=>$cantidades_clientes,'token'=>$token)); ?></div></div>
