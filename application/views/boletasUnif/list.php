<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<!-- <div id="modal_detail_boleta" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Detalle de boletas</h4>
			</div>
			<div class="modal-body">
				<table class="table" cellspacing="0" width="100%">
					<thead class="thead-light">
						<tr>
							<th>Fecha</th>
							<th>N° Boleta</th>
							<th class="text-center">Acciones</th>
						</tr>
					</thead>
					<tbody id="rows_vol">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div> -->
<!-- /.modal -->
<!-- Start content -->
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">RESUMEN ASIGNACION DE UNIFORMES</h4>
					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#">Gestor</a></li>
						<li class="breadcrumb-item active">Gestor de Boletas Asignación de uniformes</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-xs-12">
				<div class="card-box">
					<h4 class="header-title m-t-0 m-b-20">Lista de Personal con Asignación de uniformes</h4>
					<table id="datatable" class="table" cellspacing="0" width="100%">
						<thead class="thead-light">
							<tr>
								<th>Nombre Completo</th>
								<th>DNI</th>
								<th>Nro Boleta</th>
								<th class="text-center">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php if ($list_boletas): ?>
								<?php foreach ($list_boletas as $vol): ?>
									<tr class="">
										<td class="nombres"><?= $vol->apellido_pat." ". $vol->nombres ?></td>
										<td class=""><?= $vol->dni ?></td>
										<td class=""><?= $vol->nro_boleta ?></td>
										<td class="text-center">
											<a href="<?= site_url() ?>BoletasUnif/edit_vol/<?= $vol->nro_boleta ?>" target="_blank">Ver</a>
											<a href="<?= site_url() ?>BoletasUnif/pdf_resumen/<?= $vol->dni ?>" class="pl-2 pr-2">Resumen</a>
										</td>
									</tr>
								<?php endforeach ?>
							<?php endif ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>

<script src="<?= base_url()?>assets/plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/jszip.min.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/pdfmake.min.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/vfs_fonts.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/buttons.html5.min.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/buttons.print.min.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/buttons.colVis.min.js"></script>

<script src="<?= base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script src="<?= base_url()?>assets/js/backend/boletas_uniforme/index.js?v=<?=$this->config->item("curr_ver");?>"></script>