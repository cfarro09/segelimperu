<?php defined('BASEPATH') OR exit('No direct script access allowed'); $nuevo=!$cliente||!$cliente->id; ?>
<div class="content"><div class="container-fluid"><?php $this->load->view('almacen/cabecera', array('cabecera_titulo'=>$nuevo?'Nuevo cliente':'Editar cliente', 'cabecera_acciones'=>array(array('texto'=>'Volver','icono'=>'mdi-arrow-left','clase'=>'btn-light','aria'=>'Volver a clientes','url'=>site_url('almacen/clientes'))))); ?><div class="card-box">

<?php $this->load->view('almacen/cliente_formulario',array('cliente'=>$cliente,'errores'=>$errores,'token'=>$token,'en_modal'=>false,'supervisores'=>$supervisores,'puede_editar_presupuesto'=>$puede_editar_presupuesto ?? true)); ?>
</div></div></div>
