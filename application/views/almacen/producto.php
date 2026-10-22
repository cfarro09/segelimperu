<?php defined('BASEPATH') OR exit('No direct script access allowed'); $nuevo = !$producto || !$producto->id; ?>
<div class="content"><div class="container-fluid">
    <?php
    $acciones_cabecera=array(array('texto'=>'Volver','icono'=>'mdi-arrow-left','clase'=>'btn-light','aria'=>'Volver a productos','url'=>site_url('almacen/productos')));
    if(!$nuevo) $acciones_cabecera[]=array('texto'=>'Inventario','icono'=>'mdi-package-variant','url'=>site_url('almacen/inventario/'.$producto->id));
    $this->load->view('almacen/cabecera', array('cabecera_titulo'=>$nuevo?'Nuevo producto':'Editar producto','cabecera_acciones'=>$acciones_cabecera));
    ?>
    <div class="card-box">
        
        <?php $this->load->view('almacen/producto_formulario',array('producto'=>$producto,'errores'=>$errores,'token'=>$token,'en_modal'=>false)); ?>
    </div>
</div></div>
