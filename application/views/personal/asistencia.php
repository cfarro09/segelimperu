<link href="<?= base_url() ?>assets/css/form/css.css" rel="stylesheet">

<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" 
type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />

<div id="modal_operation" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Agregar asistencia</h4>
			</div>
			<input type="hidden" name="thispage" id="thispage" value="edit">
			<form id="form_recibo">
				<input type="hidden" name="assistcontrolid" id="assistcontrolid" value="">
				<div class="modal-body">
					<div class="row">
						
						<div class="col-lg-12 col-xs-12 mb-2">
							<label for="">Sleccionar Personal</label>
							<select name="personalid" id="personalid" required class="form-control select2">
								<option value="" disabled selected>Buscar Personal</option>
								<?php foreach ($personal as $value) : ?>
									<option value="<?= $value->idPersonal ?>"><?= $value->nombres . " " . $value->apellido_pat . " " . $value->apellido_mat . " " . $value->nrodocumento ?></option>
								<?php endforeach ?>
							</select>
							<!-- <input type="text" name="apellido_mat" id="apellido_mat" class="form-control" placheholder="Ingrese numero de boleta" required> -->
						</div>
						<div class="col-lg-12 col-xs-12 mb-2 row">
							<div class="col-lg-6 col-xs-12">
								<label>Fecha</label>
								<div class="input-group">
									<input autocomplete="off" required name="assist_date" id="assist_date" type="text" class="form-control datepicker-autoclose" placeholder="mm/dd/yyyy" data-date-format="yyyy-mm-dd">
									<div class="input-group-append">
										<span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
									</div>
								</div>
							</div>
						</div>
							
						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Hora inicio</label>
								<input type="time" value="06:00" autocomplete="off" required name="time_start" id="time_start" class="form-control input_detail">
							</div>
						</div>


						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Hora final</label>
								<input type="time" value="18:00" autocomplete="off" required name="time_end" id="time_end" class="form-control input_detail">
							</div>
						</div>

						<div class="col-lg-12 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Empresa</label>
								<input autocomplete="off" required name="company" id="company" class="form-control input_detail">
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
					<h4 class="page-title float-left">RESUMEN DE ASISTENCIAS</h4>
					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#">Gestor</a></li>
						<li class="breadcrumb-item active">Gestor de asistencias</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2" style="display: flex; align-items: center">
				<button class="btn btn-success" onclick="registrarrecibo()">Agregar asistencia</button>
			</div>
			
		</div>
		<div class="row">
			<div class="col-md-12 col-xs-12">
				<div class="card-box table-responsive">
					<table id="datatableaux" class="table table-responsive w-100 d-block d-md-table" cellspacing="0" width="100%">
						<thead class="thead-light">
							<tr>
								<th>ID</th>
								<th>Fecha</th>
								<th>Hora inicio</th>
								<th>Hora fin</th>
								<th>Empresa</th>
								<th>Personal</th>
								<th class="text-center">Acciones</th>
							</tr>
						</thead>
						<tbody>

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
		assistcontrolid.value = "0"
		$("#modal_operation").modal();
		$('#personalid').val("").trigger('change');
		form_recibo.reset();

	}
	function editrecibo (obj, id, personalid) {
		assistcontrolid.value = id;
		debugger
		personalid.value = $(obj).parent().parent().find(".assistcontrolid").text();
		$('#personalid').val(personalid).trigger('change');

		assist_date.value = $(obj).parent().parent().find(".assist_date").text();
		time_start.value = $(obj).parent().parent().find(".time_start").text();
		time_end.value = $(obj).parent().parent().find(".time_end").text();
		company.value = $(obj).parent().parent().find(".company").text();
		$("#modal_operation").modal();
	}
	document.getElementById("form_recibo").onsubmit = function (e) {
		e.preventDefault();
		fetch_post("<?= base_url() ?>personal/asistenciainsertar", $("#form_recibo").serialize()).then(r => {
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
		
		tabletmp =  $('#datatableaux').DataTable({
			"processing": true,
			"ajax": {
				"url": "<?= base_url() ?>personal/getAsistencias",
				"type": "POST",
				
			},
			initComplete:function(){
				console.log("dd");
				$('#datatableaux').DataTable().buttons().container().appendTo('#datatableaux_wrapper .col-md-6:eq(0)');
			},
			buttons: ['excel', 'pdf', 'colvis'],
			//columnDefs: [
				//{
				//	targets: 6,
				//	render: $.fn.dataTable.render.moment('DD/MM/YYYY')	
				//}
			//],
			"columns": [
				{ className:"assistcontrolid", data: "assistcontrolid" },
				{ className:"assist_date", data: "assist_date" },
				{ className:"time_start", data: "time_start" },
				{ className:"time_end", data: "time_end" },
				{ className:"company", data: "company" },
				{ className:"personal", data: "personal" },
				{ 
					data: "assistcontrolid",
					sortalbe: false,
					render: function( data, type, full, meta ) {
						return `
						<a href="#" onclick="editrecibo(this, ${full.assistcontrolid}, ${full.personalid})"><i class="action fa fa-edit "></i></a>
						<a href="#" onclick="delete_reg(${full.assistcontrolid}, 'assistcontrolid', 'receipt', 'Recibo')" class="action fa fa-trash pl-1 remove_list"></i></a>`
					}
				},
			]
		});

		//sum column price on datatable

	}
</script>