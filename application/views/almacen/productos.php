<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$resumen_stock = $resumen_stock ?? (object)array('valor_real'=>0,'valor_valorado'=>0,'pendientes'=>0);
?>
<div class="content"><div class="container-fluid">
    <?php $this->load->view('almacen/cabecera', array('cabecera_titulo'=>'Productos e inventario', 'cabecera_acciones'=>array(array('texto'=>'Nuevo producto','icono'=>'mdi-plus','url'=>site_url('almacen/producto'),'clase'=>'btn-primary','formulario'=>'producto')))); ?>
    <?php if ($mensaje = $this->session->flashdata('almacen_ok')): ?><div class="alert alert-success" role="status"><?= html_escape($mensaje) ?></div><?php endif; ?>
    <div data-resumen-stock>
        <div class="almacen-productos-valores">
            <div class="card-box"><h5>Valor a precio real</h5><p>S/ <span id="productos-valor-real"><?= number_format($resumen_stock->valor_real,2) ?></span></p></div>
            <div class="card-box"><h5>Valor a precio valorado</h5><p>S/ <span id="productos-valor-valorado"><?= number_format($resumen_stock->valor_valorado,2) ?></span></p></div>
        </div>
        <p id="productos-conteo-pendiente" class="small text-muted mb-2" <?= $resumen_stock->pendientes?'':'hidden' ?>><span><?= (int)$resumen_stock->pendientes ?></span> productos por verificar, excluidos del valor.</p>
    </div>
    <div class="card-box">
        <?php $this->load->view('almacen/stock_filtro'); ?>
        <div class="table-responsive"><table data-almacen-tabla data-filtros-columna data-filtrar-fumi data-filtrar-stock data-valores-stock data-buscar="<?= html_escape($buscar) ?>" class="table table-striped table-hover">
            <thead><tr><th>Código</th><th>Producto</th><th data-filtro="lista">Categoría</th><th data-filtro="lista">Marca</th><th data-filtro="lista">Unidad</th><th>Precio real S/</th><th>Precio valorado S/</th><th>Stock</th><th>Valor del stock S/</th><th>Vence</th><th data-filtro="lista">Estado</th><th>Acciones</th></tr></thead>
            <tbody><?php foreach($productos as $p): $this->load->view('almacen/producto_fila',array('p'=>$p)); endforeach; ?></tbody>
        </table></div>

    </div>
</div></div>

<?php $this->load->view('almacen/formulario_modal'); ?>
<?php $this->load->view('almacen/datatables'); ?>
