<link href="<?= base_url() ?>assets/css/form/css.css" rel="stylesheet">

<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />


<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">RESUMEN PERSONAL</h4>
					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#">Gestor</a></li>
						<li class="breadcrumb-item active">Gestor Personal</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 col-xs-12">
				<div class="card-box">
					<h4 class="header-title m-t-0 m-b-20">Lista de Personal</h4>

					<div class="row">
						<div class="col-sm-4">
							<div class="form-group	">
								<label>Filtrar fecha de vencimiento</label>
								<input class="form-control input-daterange-datepicker" id="fechavencimiento" type="text" name="daterange" />
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group	">
								<label>Filtrar fecha ingresado al sistema</label>
								<input class="form-control input-daterange-datepicker" id="fechacreacion" type="text" name="daterange" />
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<table id="datatable" class="table" cellspacing="0" width="100%">
							<thead class="thead-light">
								<tr>
									<th class="text-center">N° File</th>
									<th class="text-center">Apellido Paterno</th>
									<th class="text-center">Apellido Materno</th>
									<th class="text-center">Nombres</th>
									<th class="text-center">N° Doc</th>
									<th class="text-center">Tipo Doc.</th>
									<th class="text-center">Tipo Contrato</th>
									<th class="text-center">Fecha ingreso</th>
									<th class="text-center">Estado</th>
									<th class="text-center">F. Vencimiento</th>
									<th class="text-center">F. Creado</th>
									<th class="text-center">Ajustes</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($listpersonal) : ?>
									<?php foreach ($listpersonal as $r) : ?>
										<tr class="">
											<td><?= $r->nrofile ?></td>
											<td><?= $r->apellido_pat ?></td>
											<td><?= $r->apellido_mat ?></td>
											<td><?= $r->nombres ?></td>
											<td><?= $r->nrodocumento ?></td>
											<td><?= $r->tipodocumento ?></td>
											<td><?= $r->tipocontrato ?></td>
											<td><?= $r->fecha_ingreso ? $r->fecha_ingreso : "1999-09-09" ?></td>
											<td><?= $r->estado ?></td>
											<td><?= $r->fecha_vencimiento ? $r->fecha_vencimiento : "1999-09-09" ?></td>
											<td><?= substr($r->datecreated, 0, 10) ?></td>
											<td class="text-center">
												<a href="<?= site_url() ?>personal/editar/<?= $r->idPersonal ?>"><i class="action fa fa-edit "></i></a>
												<a href="#" onclick="delete_reg(<?= $r->idPersonal ?>, 'idPersonal', 'Personal', 'Personal')" class="action fa fa-trash pl-1 remove_list"></i></a>
												<a href="<?= site_url() ?>personal/reportefichas/<?= $r->idPersonal ?>" class="action fa fa-file-pdf-o pl-1"></i></a>
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
</div>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.js"></script>

<script src="<?= base_url() ?>assets/plugins/datatables/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/jszip.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/pdfmake.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/vfs_fonts.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/buttons.html5.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/buttons.print.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/datatables/buttons.colVis.min.js"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/datetime.js"></script>

<script src="<?= base_url() ?>assets/plugins/moment/moment.js"></script>
<script src="<?= base_url() ?>assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>

<script src="<?= base_url() ?>assets/js/backend/toast_alert.js?v=<?= $this->config->item("curr_ver"); ?>"></script>
<script src="<?=base_url()?>assets/js/backend/form/list_form.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script>
	var table

	function initcontrols() {
		$('.input-daterange-datepicker').daterangepicker({
			buttonClasses: ['btn', 'btn-sm'],
			applyClass: 'btn-success',
			cancelClass: 'btn-default',
			autoclose: true,
			autoclose: true
		});
		$('#fechavencimiento').change(function(event) {
			$.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
				const $startdate = $('#fechavencimiento').data('daterangepicker').startDate._d.toISOString().substring(0, 10);
				const $enddate = $('#fechavencimiento').data('daterangepicker').endDate._d.toISOString().substring(0, 10);

				var createdAt = data[9] || 0; // Our date column in the table
				if (createdAt != 0) createdAt = `${createdAt.substring(6,10)}-${createdAt.substring(3,5)}-${createdAt.substring(0,2)}`
				if (($startdate == "" || $enddate == "") || (moment(createdAt).isSameOrAfter($startdate) && moment(createdAt).isSameOrBefore($enddate))) {
					return true;
				} else {
					return false;
				}
			});
			table.draw();
		});
		$('#fechacreacion').change(function(event) {
			$.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
				const $startdate = $('#fechacreacion').data('daterangepicker').startDate._d.toISOString().substring(0, 10);
				const $enddate = $('#fechacreacion').data('daterangepicker').endDate._d.toISOString().substring(0, 10);

				var createdAt = data[10] || 0; // Our date column in the table
				if (createdAt != 0) createdAt = `${createdAt.substring(6,10)}-${createdAt.substring(3,5)}-${createdAt.substring(0,2)}`
				if (($startdate == "" || $enddate == "") || (moment(createdAt).isSameOrAfter($startdate) && moment(createdAt).isSameOrBefore($enddate))) {
					return true;
				} else {
					return false;
				}
			});
			table.draw();
		});
		if ($('#datatable').length > 0) {
			table = $('#datatable').DataTable({
				"columnDefs": [{
						"targets": 0
					},
					{
						"targets": 1
					},
					{
						"targets": 2
					},
					{
						"targets": 3
					},
					{
						"targets": 4
					},
					{
						"targets": 5
					},
					{
						"targets": 6
					},
					{
						"targets": 7,
						render: $.fn.dataTable.render.moment('DD/MM/YYYY')


					},
					{
						"targets": 8
					},
					{
						"targets": 9,
						render: $.fn.dataTable.render.moment('DD/MM/YYYY')


					},
					{
						"targets": 10,
						render: $.fn.dataTable.render.moment('DD/MM/YYYY')

					},
					{
						"targets": 11
					},
				],
				lengthChange: false,
				buttons: ['excel', 'pdf', 'colvis'],
				"scrollX": true,
				"order": [
					[0, "desc"]
				],
			});
			table.buttons().container()
				.appendTo('#datatable_wrapper .col-md-6:eq(0)');
		}
	}
	$(document).ready(function() {
		initcontrols();
	})
</script>