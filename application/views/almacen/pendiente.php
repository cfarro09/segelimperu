<?php defined('BASEPATH') OR exit('No direct script access allowed'); $nuevo=!$pendiente||empty($pendiente->id); ?>
<div class="content"><div class="container-fluid"><?php $this->load->view('almacen/cabecera', array('cabecera_titulo'=>$nuevo?'Nuevo pendiente':'Editar pendiente', 'cabecera_acciones'=>array(array('texto'=>'Volver','icono'=>'mdi-arrow-left','clase'=>'btn-light','aria'=>'Volver a pendientes','url'=>site_url('almacen/pendientes'))))); ?><div class="card-box">
<?php $this->load->view('almacen/pendiente_formulario',array('pendiente'=>$pendiente,'errores'=>$errores,'token'=>$token,'en_modal'=>false,'clientes'=>$clientes,'solicitantes'=>$solicitantes,'puede_gestionar'=>$puede_gestionar,'nombre_usuario'=>$nombre_usuario)); ?>
</div></div></div>
