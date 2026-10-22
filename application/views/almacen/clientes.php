<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="content"><div class="container-fluid">
<?php $this->load->view('almacen/cabecera', array('cabecera_titulo'=>'Clientes y presupuestos', 'cabecera_acciones'=>array(array('texto'=>'Nuevo cliente','icono'=>'mdi-plus','url'=>site_url('almacen/cliente'),'clase'=>'btn-primary','formulario'=>'cliente')))); ?>
<?php if ($mensaje=$this->session->flashdata('almacen_ok')): ?><div class="alert alert-success" role="status"><?= html_escape($mensaje) ?></div><?php endif; ?>
<div class="card-box">

<div class="table-responsive"><table data-almacen-tabla data-filtros-columna data-buscar="<?= html_escape($buscar) ?>" class="table table-striped"><thead><tr><th>Código</th><th>Cliente</th><th data-filtro="lista">Unidad asignada</th><th data-filtro="lista">Supervisor</th><th>Horario</th><th data-filtro="lista">Material</th><th>Presupuesto máximo S/</th><th>Saldo inicial S/</th><th data-filtro="lista">Estado</th><th>Acciones</th></tr></thead><tbody>
<?php foreach($clientes as $c): $this->load->view('almacen/cliente_fila',array('c'=>$c)); endforeach; ?>
</tbody></table></div>

</div></div></div>

<?php $this->load->view('almacen/formulario_modal'); ?>
<?php if(Almacen_acceso::permite($this->session->userdata('tipo_usu'),'almacen','asignaciones')) $this->load->view('almacen/cliente_asignaciones_modal'); ?>
<?php $this->load->view('almacen/datatables'); ?>
