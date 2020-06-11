<link href="<?=base_url()?>assets/css/form/css.css" rel="stylesheet">
<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
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
						<li class="breadcrumb-item active">Todos los usuarios</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-xs-12">
				<div class="card-box">
					<h4 class="header-title m-t-0 m-b-20">Mantenimiento de Usuarios</h4>
					<table id="datatable" class="table" cellspacing="0" width="100%">
						<thead class="thead-light">
							<tr>
								<th>NOMBRE</th>
								<th>APELLIDO</th>
								<th>TIPO DOC</th>
								<th>NRO DOC</th>
								<th>EMAIL</th>
								<th>TIPO</th>
								<th>Estado</th>
								<th>Acción</th>
							</tr>
						</thead>
						<tbody>
						<?php if ($users_suscribers): ?>
							<?php foreach ($users_suscribers as $users): ?>
								<tr>
									<td><?= $users->nombre_usu ?></td>
									<td><?= $users->apellido_pat . " " . $users->apellido_mat ?></td>
									<td><?= $users->name_documento ?></td>
									<td><?= $users->nro_documento ?></td>
									<td><?= $users->email_usu ?></td>
									<td><?= $users->tipo_usu ?></td>
									<td><?= ($users->estado_usu == "AC" ? '<span class="label label-success">Activo</span>' : ($users->estado_usu == "IN" ? '<span class="label label-danger">Inactivo</span>' : ($users->estado_usu == "PG" ? '<span class="label label-warning">Pendiente</span>' : '<span class="label label-danger">Rechazado</span>' ))) ?></td>
									<td><a href="#" data-id="<?= $users->id_usu ?>" class="go_edit_usuario"><i class="fa fa-edit"></i></a></td>
								</tr>
							<?php endforeach ?>
						<?php else: ?>
							<tr>
								<td colspan="6" class="text-center font-weight-bold">No tiene usuarios registrados.</td>
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

<script src="<?= base_url()?>assets/plugins/datatables/dataTables.buttons.min.js"></script>


<script src="<?= base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script src="<?=base_url()?>assets/js/backend/usuarios/usuarios.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script>
	$('#datatable').DataTable({
        "scrollX": true
    });
</script>