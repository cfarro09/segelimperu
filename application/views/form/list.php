<link href="<?=base_url()?>assets/css/form/css.css" rel="stylesheet">
<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<!-- Start content -->
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">Listar <?= $alias_table ?></h4>

					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#"><?= $alias_table ?></a></li>
						<li class="breadcrumb-item active">Listar</li>
					</ol>

					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 col-xs-12">
				<div class="card-box">
					<h4 class="header-title m-t-0 m-b-20">Mantenimiento de <?= $alias_table ?></h4>
					<table id="datatable-buttons" class="table table-hover table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
								<?php if (isset($tittles) && $tittles): ?>
									<?php foreach ($tittles as $key => $value): ?>
										<?php if ($key != "id"): ?>
											<th><?= $value ?></th>
										<?php endif ?>
									<?php endforeach ?>
									<?php if ($escritura): ?>
										<th class="text-center">Actions</th>
									<?php endif ?>
								<?php endif ?>
							</tr>
						</thead>
						<tbody>
							<?php if (isset($data) && $data): ?>
								<?php foreach ($data as $row): ?>
									<?php if (isset($tittles) && $tittles): ?>
										<tr id="<?= $row->{$tittles['id']} ?>">
										<?php foreach ($tittles as $key => $value): ?>
											<?php if ($key != "id"): ?>
												<td><?= $row->{$key} ?></td>
											<?php else: ?>
												<?php if ($escritura): ?>
													<td class="text-center" >
														<a href="#" onclick="redirect_arg(<?= $row->{$value} ?>, '<?= $value ?>','<?= $name_table ?>', '<?= $alias_table ?>')"><i class="action fa fa-edit edit_list"></i></a>
														<a href="#"  onclick="delete_reg(<?= $row->{$value} ?>, '<?= $value ?>', '<?= $name_table ?>', '<?= $alias_table ?>')" class="action fa fa-trash pl-2 remove_list"></i></a>
													</td>
												<?php endif ?>
											<?php endif ?>
										<?php endforeach ?>
										</tr>
									<?php endif ?>
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
<script src="<?=base_url()?>assets/js/backend/form/list_form.js?v=<?=$this->config->item("curr_ver");?>"></script>
