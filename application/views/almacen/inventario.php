<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content"><div class="container-fluid">
    <?php $titulo_inventario=($vista_inventario ?? '')==='historial'?'Historial · ':(($vista_inventario ?? '')==='movimiento'?'Movimiento · ':'Inventario · '); $this->load->view('almacen/cabecera', array('cabecera_titulo'=>$titulo_inventario.$producto->descripcion, 'cabecera_acciones'=>array(array('texto'=>'Volver','icono'=>'mdi-arrow-left','clase'=>'btn-light','aria'=>'Volver a productos','url'=>site_url('almacen/productos'))))); ?>
    <?php foreach(array('almacen_ok'=>'success','almacen_error'=>'danger') as $clave=>$clase): if($mensaje=$this->session->flashdata($clave)): ?><div class="alert alert-<?= $clase ?>" role="<?= $clase==='success'?'status':'alert' ?>"><?= html_escape($mensaje) ?></div><?php endif; endforeach; ?>
    <div class="card-box">
        <?php $this->load->view('almacen/inventario_formulario',array('producto'=>$producto,'movimientos'=>$movimientos,'token'=>$token,'en_modal'=>false,'vista_inventario'=>$vista_inventario ?? 'completo')); ?>
    </div>
</div></div>
