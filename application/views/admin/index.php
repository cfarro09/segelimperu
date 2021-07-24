<link href="<?=base_url()?>assets/css/form/css.css" rel="stylesheet">
<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<!-- Start content -->
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">Listar</h4>

					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#">Gestor</a></li>
						<li class="breadcrumb-item active">Gestor de Tablas - AdminPRO</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 col-xs-12">
				<div class="card-box">
					<h4 class="header-title m-t-0 m-b-20">Configure your Tables</h4>
					<table id="datatable" class="table" cellspacing="0" width="100%">
						<thead class="thead-light">
							<tr>
								<th>TABLE</th>
								<th>ALIAS</th>
								<th>STATUS</th>
							</tr>
						</thead>
						<tbody>
						<?php if ($data_tables): ?>
							<?php foreach ($data_tables as $nameTable => $value): ?>
								<tr>
									<td class="nametable"><?= $nameTable ?></td>
									<td class="aliastable row">
										<div class="col-md-8 col-xs-10 pr-0">
											<input type="text" class="" data-old="<?= $value['alias'] ?>" value="<?= $value['alias'] ?>">
										</div>
									</td>
									<td><a href="#"><?= $value['status'] == 'IN' ? '<span class="label pr-2 pl-2 label-danger">Inactivo</span>' : '<span class="pr-2 pl-2 label label-success">Activo</span>'?></a></td>
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
<script src="<?=base_url()?>assets/js/backend/admin/admin.js?v=<?=$this->config->item("curr_ver");?>"></script>
