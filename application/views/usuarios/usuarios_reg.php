<link href="<?=base_url()?>assets/css/form/css.css" rel="stylesheet">
<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />


<div id="modal_insert_usu" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Categoria de trabajador</h4>
            </div>
            <form id="form_insert_usu">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="hidden" name="action" value="AC">
                                <input type="hidden" name="id_usu" id="id_usu" value="">
                                <select name="tipo_usu" class="form-control" required="true">
                                	<?php if ($categories): ?>
                                		<option value="" disabled selected>Select Category</option>
                                		<?php foreach ($categories as $category): ?>
                                			<option value="<?= $category->work_char ?>"><?= $category->name_work ?></option>
                                		<?php endforeach ?>
                                	<?php endif ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success waves-effect waves-light">APROBAR</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->
<!-- Start content -->
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">Usuarios Registrados</h4>

					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#">Usuarios</a></li>
						<li class="breadcrumb-item active">Usuarios Recien Registrados</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-xs-12">
				<div class="card-box">
					<h4 class="header-title m-t-0 m-b-20">Apobar o rechazar usuarios</h4>
					<table id="datatable" class="table" cellspacing="0" width="100%">
						<thead class="thead-light">
							<tr>
								<th>NOMBRE</th>
								<th>APELLIDO</th>
								<th>TIPO DOC</th>
								<th>NRO DOC</th>
								<th>EMAIL</th>
								<th class="text-center">Accion</th>
							</tr>
						</thead>
						<tbody>
						<?php if ($users_suscribers): ?>
							<?php foreach ($users_suscribers as $users): ?>
								<tr>
									<td><?= $users->nombre_usu ?></td>
									<td><?= $users->apellido_pat . " " . $users->apellido_mat ?></td>
									<td><?= $users->id_tipo_doc ?></td>
									<td><?= $users->nro_documento ?></td>
									<td><?= $users->email_usu ?></td>
									<td data-id="<?= $users->id_usu ?>" class="text-center"><a href="#" data-action="AC" class="action_usu"><span class="label label-success">APROBAR</span></a><a href="#" data-action="RJ" class="ml-2 action_usu"><span class="label label-danger">RECHAZAR</span></a></td>
								</tr>
							<?php endforeach ?>
						<?php else: ?>
							<tr>
								<td colspan="6" class="text-center font-weight-bold">No tiene usuarios recien registrados.</td>
							</tr>
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

<script src="<?= base_url()?>assets/plugins/datatables/jszip.min.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/pdfmake.min.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/vfs_fonts.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/buttons.html5.min.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/buttons.print.min.js"></script>
<script src="<?= base_url()?>assets/plugins/datatables/buttons.colVis.min.js"></script>


<script src="<?= base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script src="<?=base_url()?>assets/js/backend/usuarios/usuarios.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script>
	$('#datatable').DataTable({
        "scrollX": true
    });
</script>