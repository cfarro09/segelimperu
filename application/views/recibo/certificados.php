<link href="<?= base_url() ?>assets/css/form/css.css" rel="stylesheet">

<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" 
type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />

<div id="modal_operation" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Certificado conformidad</h4>
			</div>
			<input type="hidden" name="thispage" id="thispage" value="edit">
			<form id="form_recibo">
				<input type="hidden" name="certificadoid" id="certificadoid" value="">
				<div class="modal-body">
					<div class="row">
						
						<div class="col-lg-6 col-md-12">
							<div class="form-group">
								<label for="" class="control-label">Servicio</label>
								<select required name="servicio" id="servicio" class="form-control input_detail">
									<option>DESINSECTACION</option>
									<option>LIMPIEZARESERVORIO</option>
									<option>LIMPIEZATANQUE</option>
									<option>DESRATIZACION</option>
									<option>DESINFECCION</option>
								</select>
							</div>
						</div>

						<div class="col-lg-6 col-xs-12">
							<label>Fecha servicio</label>
							<div class="input-group">
								<input autocomplete="off" required name="fechaservicio" id="fechaservicio" type="text" class="form-control datepicker-autoclose" placeholder="mm/dd/yyyy" data-date-format="yyyy-mm-dd">
								<div class="input-group-append">
									<span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-xs-12">
							<label>Fecha vencimiento</label>
							<div class="input-group">
								<input autocomplete="off" required name="vencimiento" id="vencimiento" type="text" class="form-control datepicker-autoclose" placeholder="mm/dd/yyyy" data-date-format="yyyy-mm-dd">
								<div class="input-group-append">
									<span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
								</div>
							</div>
						</div>
						<div class="col-lg-6 col-md-12">
							<div class="form-group">
								<label for="" class="control-label">Área</label>
								<input autocomplete="off" required type="text" name="area" id="area" class="form-control input_detail">
							</div>
						</div>
						<div class="col-lg-6 col-md-12">
							<div class="form-group">
								<label for="" class="control-label">Cliente</label>
								<input autocomplete="off" required type="text" name="cliente" id="cliente" class="form-control input_detail">
							</div>
						</div>
						<div class="col-lg-6 col-md-12">
							<div class="form-group">
								<label for="" class="control-label">Ubicación</label>
								<input autocomplete="off" required type="text" name="ubicacion" id="ubicacion" class="form-control input_detail">
							</div>
						</div>
						<div class="col-lg-6 col-md-12">
							<div class="form-group">
								<label for="" class="control-label">Giro</label>
								<input autocomplete="off" required type="text" name="giro" id="giro" class="form-control input_detail">
							</div>
						</div>

						

					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
					<button type="submit" id="btn_submit" class="btn btn-info waves-effect waves-light">GUARDAR</button>
				</div>
			</form>
		</div>
	</div>
</div><!-- /.modal -->


<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">RESUMEN DE CERTIFICADOS DE CONFORMIDAD</h4>
					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#">Gestor</a></li>
						<li class="breadcrumb-item active">Gestor de certificados de conformidad</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2" style="display: flex; align-items: center">
				<button class="btn btn-success" onclick="registrarrecibo()">Agregar certificado</button>
			</div>
			<div class="col-sm-4">
				<div class="form-group	">
					<label>Filtrar fecha de recibo</label>
					<input class="form-control input-daterange-datepicker" id="fecharecibo" type="text" name="daterange" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 col-xs-12">
				<div class="card-box">
					<table id="datatableaux" class="table" cellspacing="0" width="100%">
						<thead class="thead-light">
							<tr>
								<th>ID</th>
								<th>Servicio</th>
								<th>Fecha servico</th>
								<th>Fecha vencimiento</th>
								<th>Giro</th>
								<th>Area</th>
								<th>Cliente</th>
								<th>Ubicacion</th>
								<th class="text-center">Acciones</th>
							</tr>
						</thead>
						<tbody></tbody>
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
<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/datetime.js"></script>

<script src="<?= base_url() ?>assets/plugins/moment/moment.js"></script>
<script src="<?= base_url() ?>assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>

<script src="<?= base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script src="<?= base_url()?>assets/js/backend/boletas_uniforme/index.js?v=<?=$this->config->item("curr_ver");?>"></script>

<script src="<?=base_url()?>assets/js/backend/form/list_form.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script>
	var tabletmp = null;
	$('.datepicker-autoclose').datepicker({
		autoclose: true,
		todayHighlight: true
	});

	function registrarrecibo () {
		form_recibo.reset();
		certificadoid.value = "0"
		$("#modal_operation").modal();
	}
	function editrecibo (obj, id) {
		certificadoid.value = id;
		servicio.value = $(obj).parent().parent().find(".servicio").text();
		fechaservicio.value = $(obj).parent().parent().find(".fechaservicio").text();
		vencimiento.value = $(obj).parent().parent().find(".vencimiento").text();
		giro.value = $(obj).parent().parent().find(".giro").text();
		area.value = $(obj).parent().parent().find(".area").text();
		cliente.value = $(obj).parent().parent().find(".cliente").text();
		ubicacion.value = $(obj).parent().parent().find(".ubicacion").text();

		$("#modal_operation").modal();
	}
	document.getElementById("form_recibo").onsubmit = function (e) {
		e.preventDefault();
		fetch_post("<?= base_url() ?>recibo/insertarcertificado", $("#form_recibo").serialize()).then(r => {
			if (r.success) {
				form_recibo.reset();
				$("#modal_operation").modal("hide")
				$("#datatableaux").DataTable().ajax.reload();
			}
			else
				toast_error('¡Oh, hubo un error!', data.msg);

		})
	}
	window.onload = () => {
		$('.input-daterange-datepicker').daterangepicker({
			buttonClasses: ['btn', 'btn-sm'],
			applyClass: 'btn-success',
			cancelClass: 'btn-default',
			autoclose: true,
			autoclose: true
		});

		$('#fecharecibo').change(function(event) {
			let ss = 0;
			$.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
				const $startdate = $('#fecharecibo').data('daterangepicker').startDate._d.toISOString().substring(0, 10);
				const $enddate = $('#fecharecibo').data('daterangepicker').endDate._d.toISOString().substring(0, 10);

				var createdAt = data[2] || 0; // Our date column in the table

				if (createdAt != 0) createdAt = `${createdAt.substring(6,10)}-${createdAt.substring(3,5)}-${createdAt.substring(0,2)}`
				if (($startdate == "" || $enddate == "") || (moment(createdAt).isSameOrAfter($startdate) && moment(createdAt).isSameOrBefore($enddate))) {
					ss = ss + parseFloat(data[2])
					return true;
				} else {
					return false;
				}
			});
			tabletmp.draw();
		});
		
		tabletmp =  $('#datatableaux').DataTable({
			"processing": true,
			"ajax": {
				"url": "<?= base_url() ?>recibo/getDatacertificado",
				"type": "POST",
			},
			initComplete:function(){
				$('#datatableaux').DataTable().buttons().container().appendTo('#datatableaux_wrapper .col-md-6:eq(0)');
			},
			buttons: ['excel', 'pdf', 'colvis'],
			columnDefs: [
				{
					targets: 2,
					render: $.fn.dataTable.render.moment('YYYY-MM-DD')	
				}
			],
			"columns": [

				{ className:"certificadoid", data: "certificadoid" },
				{ className:"servicio", data: "servicio" },
				{ className:"fechaservicio", data: "fechaservicio" },
				{ className:"vencimiento", data: "vencimiento" },
				{ className:"giro", data: "giro" },
				{ className:"area", data: "area" },
				{ className:"cliente", data: "cliente" },
				{ className:"ubicacion", data: "ubicacion" },
				{ 
					data: "certificadoid",
					sortalbe: false,
					render: function( data, type, full, meta ) {
						return `<a href="#" onclick="editrecibo(this, ${full.certificadoid})"><i class="action fa fa-edit "></i></a>
						<a href="#" onclick="delete_reg(${full.certificadoid}, 'certificadoid', 'certificado', 'certificado')" class="action fa fa-trash pl-1 remove_list"></i></a>
						<a target="_blank" href="<?= site_url() ?>recibo/reporte_certificado/${full.certificadoid}" class="action fa fa-file-pdf-o pl-1"></i></a>`
					}
				},
			]
		});

		//sum column price on datatable

	}
</script>