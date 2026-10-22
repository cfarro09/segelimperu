<link href="<?= base_url() ?>assets/css/form/css.css" rel="stylesheet">

<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" 
type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />

<?php $can_change_receipt_status = in_array(strtolower($this->session->userdata('email_usu')), array('admin@segelim.com', 'fdcarlosd1@gmail.com')); ?>

<div id="modal_status" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Cambiar estado</h4>
			</div>
			<form id="form_status">
				<input type="hidden" name="receipt_id" id="status_receipt_id" value="">
				<div class="modal-body">
					<div class="form-group">
						<label for="receipt_status" class="control-label">Estado</label>
						<select required name="receipt_status" id="receipt_status" class="form-control">
							<option value="PENDIENTE">Pendiente</option>
							<option value="APROBADO">Aprobado</option>
							<option value="RECHAZADO">Rechazado</option>
						</select>
					</div>
					<div class="form-group">
						<label for="status_observation" class="control-label">Observaciones</label>
						<textarea name="status_observation" id="status_observation" rows="4" class="form-control"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
					<button type="submit" class="btn btn-info waves-effect waves-light">GUARDAR</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="modal_operation" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Agregar recibo</h4>
			</div>
			<input type="hidden" name="thispage" id="thispage" value="edit">
			<form id="form_recibo">
				<input type="hidden" name="receipt_id" id="receipt_id" value="">
				<div class="modal-body">
					<div class="row">
						
						<div class="col-md-12">
							<div class="form-group">
								<label for="personal_receipt" class="control-label">Seleccionar personal</label>
								<select id="personal_receipt" class="form-control select2 select2-receipt-personal">
									<option value="" disabled selected>Buscar personal</option>
									<?php if (isset($personal) && $personal): ?>
										<?php foreach ($personal as $value): ?>
											<?php
												$full_name = trim($value->nombres . " " . $value->apellido_pat . " " . $value->apellido_mat);
												$document_number = trim($value->nrodocumento);
												$phone_number_personal = trim($value->telefonoactual);
											?>
											<option value="<?= $value->idPersonal ?>" data-client="<?= htmlspecialchars($full_name, ENT_QUOTES, 'UTF-8') ?>" data-document="<?= htmlspecialchars($document_number, ENT_QUOTES, 'UTF-8') ?>" data-phone="<?= htmlspecialchars($phone_number_personal, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($full_name . " " . $document_number, ENT_QUOTES, 'UTF-8') ?></option>
										<?php endforeach ?>
									<?php endif ?>
								</select>
							</div>
						</div>

						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Apellidos y nombres</label>
								<input autocomplete="off" required readonly type="text" name="client" id="client" class="form-control input_detail">
							</div>
						</div>

						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Monto</label>
								<input autocomplete="off" required type="number" step="any" name="price" id="price" class="form-control input_detail">
							</div>
						</div>

						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">N° documento de identidad</label>
								<input autocomplete="off" required readonly name="certificate_number" id="certificate_number" class="form-control input_detail">
							</div>
						</div>

						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">N° celular</label>
								<input autocomplete="off" name="phone_number" id="phone_number" class="form-control input_detail">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label for="" class="control-label">Concepto</label>
								<textarea autocomplete="off" required name="concept" id="concept" class="form-control input_detail"></textarea>
							</div>
						</div>

						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Unidad</label>
								<input autocomplete="off" required name="made_in" id="made_in" class="form-control input_detail">
							</div>
						</div>


						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Banco / N° cuenta</label>
								<input autocomplete="off" name="bank_account" id="bank_account" class="form-control input_detail">
							</div>
						</div>


						<div class="col-lg-6 col-xs-12">
							<label>Fecha</label>
							<div class="input-group">
								<input autocomplete="off" required name="date_receipt" id="date_receipt" type="text" class="form-control datepicker-autoclose" placeholder="mm/dd/yyyy" data-date-format="yyyy-mm-dd">
								<div class="input-group-append">
									<span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
								</div>
							</div>
						</div>

						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Responsable de liquidación</label>
								<input autocomplete="off" name="liquidation_name" id="liquidation_name" class="form-control input_detail">
							</div>
						</div>

						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Responsable del pago</label>
								<input autocomplete="off" name="payer_name" id="payer_name" class="form-control input_detail">
							</div>
						</div>

						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Persona que da conformidad</label>
								<input autocomplete="off" name="conformity_name" id="conformity_name" class="form-control input_detail">
							</div>
						</div>

						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Tipo de pago</label>
								<select required name="payment_type" id="payment_type" class="form-control input_detail">
									<option value="EFECTIVO">EFECTIVO</option>
									<option value="YAPE">YAPE</option>
									<option value="PLIN">PLIN</option>
									<option value="TRANSFERENCIA">TRANSFERENCIA</option>
								</select>
							</div>
						</div>

						<div class="col-lg-6 col-xs-12">
							<div class="form-group">
								<label for="" class="control-label">Horas trabajadas</label>
								<input autocomplete="off" name="worked_hours" id="worked_hours" class="form-control input_detail">
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
					<h4 class="page-title float-left">RESUMEN DE RECIBO</h4>
					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#">Gestor</a></li>
						<li class="breadcrumb-item active">Gestor de recibos</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-2" style="display: flex; align-items: center">
				<button class="btn btn-success" onclick="registrarrecibo()">Agregar recibo</button>
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
								<th>Apellidos y nombres</th>
								<th>Monto</th>
								<th>Concepto</th>
								<th>Unidad</th>
								<th>N° documento identidad</th>
								<th>N° celular</th>
								<th>Banco / N° cuenta</th>
								<th>Resp. liquidación</th>
								<th>Resp. pago</th>
								<th>Conformidad</th>
								<th>Tipo pago</th>
								<th>Fecha</th>
								<th>Estado</th>
								<th>Horas trabajadas</th>
								<th>Observaciones</th>
								<th class="text-center">Acciones</th>
							</tr>
						</thead>
						<tbody></tbody>
						<tfoot>
							<tr>
								<th colspan="16">Total</th>
								<th id="total_order"></th>
							</tr>
						</tfoot>
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
<script src="<?= base_url() ?>assets/js/datatables-segelim.js?v=<?= filemtime(FCPATH.'assets/js/datatables-segelim.js') ?>"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.20/dataRender/datetime.js"></script>

<script src="<?= base_url() ?>assets/plugins/moment/moment.js"></script>
<script src="<?= base_url() ?>assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>

<script src="<?= base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script src="<?= base_url()?>assets/js/backend/boletas_uniforme/index.js?v=<?=$this->config->item("curr_ver");?>"></script>

<script src="<?=base_url()?>assets/js/backend/form/list_form.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script>
	var tabletmp = null;
	var canChangeReceiptStatus = <?= $can_change_receipt_status ? 'true' : 'false' ?>;
	$('.datepicker-autoclose').datepicker({
		autoclose: true,
		todayHighlight: true
	});
	$('#modal_operation').on('shown.bs.modal', function() {
		if ($('#personal_receipt').hasClass('select2-hidden-accessible')) {
			$('#personal_receipt').select2('destroy');
		}
		$('#personal_receipt').select2({
			dropdownParent: $('#modal_operation'),
			width: '100%'
		});
	});
	$(document).on('select2:open', function() {
		setTimeout(function() {
			var searchField = document.querySelector('.select2-container--open .select2-search__field');
			if (searchField) searchField.focus();
		}, 0);
	});

	function normalizeReceiptDate(dateValue) {
		if (!dateValue || dateValue === 'Invalid date') return '';
		if (moment(dateValue, 'YYYY-MM-DD', true).isValid()) return dateValue;
		if (moment(dateValue, 'DD/MM/YYYY', true).isValid()) return moment(dateValue, 'DD/MM/YYYY').format('YYYY-MM-DD');
		return dateValue;
	}

	function selectReceiptPersonalByDocument(documentNumber) {
		var selectedValue = '';
		$('#personal_receipt option').each(function() {
			if ($(this).data('document') == documentNumber) {
				selectedValue = $(this).val();
				return false;
			}
		});
		$('#personal_receipt').val(selectedValue).trigger('change.select2');
	}

	function registrarrecibo () {
		form_recibo.reset();
		receipt_id.value = "0"
		$('#personal_receipt').val('').trigger('change.select2');
		$("#modal_operation").modal();
	}
	function editrecibo (obj, id) {
		receipt_id.value = id;

		client.value = $(obj).parent().parent().find(".client").text();
		price.value = $(obj).parent().parent().find(".price").text(); 
		certificate_number.value = $(obj).parent().parent().find(".certificate_number").text();
		phone_number.value = $(obj).parent().parent().find(".phone_number").text();
		concept.value = $(obj).parent().parent().find(".concept").text();
		made_in.value = $(obj).parent().parent().find(".made_in").text();
		bank_account.value = $(obj).parent().parent().find(".bank_account").text();
		liquidation_name.value = $(obj).parent().parent().find(".liquidation_name").text();
		payer_name.value = $(obj).parent().parent().find(".payer_name").text();
		conformity_name.value = $(obj).parent().parent().find(".conformity_name").text();
		payment_type.value = $(obj).parent().parent().find(".payment_type").text();
		worked_hours.value = $(obj).parent().parent().find(".worked_hours").text();
		selectReceiptPersonalByDocument(certificate_number.value);
		var rowData = tabletmp.row($(obj).closest('tr')).data();
		date_receipt.value = normalizeReceiptDate(rowData && rowData.date_receipt ? rowData.date_receipt : $(obj).parent().parent().find(".date_receipt").text());
		$("#modal_operation").modal();
	}
	$('#personal_receipt').change(function() {
		var selectedOption = $(this).find(':selected');
		client.value = selectedOption.data('client') || '';
		certificate_number.value = selectedOption.data('document') || '';
		phone_number.value = selectedOption.data('phone') || '';
	});
	function editStatusRecibo(obj, id) {
		var rowData = tabletmp.row($(obj).closest('tr')).data();
		status_receipt_id.value = id;
		receipt_status.value = rowData && rowData.receipt_status ? rowData.receipt_status : 'PENDIENTE';
		status_observation.value = rowData && rowData.status_observation ? rowData.status_observation : '';
		$("#modal_status").modal();
	}
	document.getElementById("form_status").onsubmit = function (e) {
		e.preventDefault();
		fetch_post("<?= base_url() ?>recibo/cambiar_estado", $("#form_status").serialize()).then(r => {
			if (r.success) {
				form_status.reset();
				$("#modal_status").modal("hide");
				$("#datatableaux").DataTable().ajax.reload();
			} else {
				toast_error('¡Oh, hubo un error!', r.msg);
			}
		})
	}
	document.getElementById("form_recibo").onsubmit = function (e) {
		e.preventDefault();
		date_receipt.value = normalizeReceiptDate(date_receipt.value);
		fetch_post("<?= base_url() ?>recibo/insertar", $("#form_recibo").serialize()).then(r => {
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

				var createdAt = data[12] || 0; // Our date column in the table

				if (createdAt != 0) createdAt = `${createdAt.substring(6,10)}-${createdAt.substring(3,5)}-${createdAt.substring(0,2)}`
				if (($startdate == "" || $enddate == "") || (moment(createdAt).isSameOrAfter($startdate) && moment(createdAt).isSameOrBefore($enddate))) {
					ss = ss + parseFloat(data[2])
					return true;
				} else {
					return false;
				}
			});
			console.log(ss);
			tabletmp.draw();
			$(tabletmp.column(2).footer()).html("Total: " + ss.toFixed(2));
		});
		
		tabletmp =  $('#datatableaux').DataTable({
			"processing": true,
			"ajax": {
				"url": "<?= base_url() ?>recibo/getData",
				"type": "POST",
				
			},
			initComplete:function(){
				console.log("dd");
				$('#datatableaux').DataTable().buttons().container().appendTo('#datatableaux_wrapper .col-md-6:eq(0)');
			},
			buttons: ['excel', 'pdf', 'colvis'],
			columnDefs: [
				{
					targets: 12,
					render: function(data) {
						var normalizedDate = normalizeReceiptDate(data);
						return moment(normalizedDate, 'YYYY-MM-DD', true).isValid() ? moment(normalizedDate).format('DD/MM/YYYY') : data;
					}
				}
			],
			footerCallback: function (tfoot, data, start, end, display) {
				var api = this.api();
				var p = api.column(2).data().reduce(function (a, b) {
					return a + parseFloat(b);
				}, 0)
				$(api.column(2).footer()).html("Total: " + p.toFixed(2));
			},
			"columns": [
				{ className:"receipt_id", data: "receipt_id" },
				{ className:"client", data: "client" },
				{ className:"price", data: "price" },
				{ className:"concept", data: "concept" },
				{ className:"made_in", data: "made_in" },
				{ className:"certificate_number", data: "certificate_number" },
				{ className:"phone_number", data: "phone_number" },
				{ className:"bank_account", data: "bank_account" },
				{ className:"liquidation_name", data: "liquidation_name" },
				{ className:"payer_name", data: "payer_name" },
				{ className:"conformity_name", data: "conformity_name" },
				{ className:"payment_type", data: "payment_type" },
				{ className:"date_receipt", data: "date_receipt" },
				{ className:"receipt_status", data: "receipt_status", render: function(data) { return data ? data : 'PENDIENTE'; } },
				{ className:"worked_hours", data: "worked_hours", render: function(data) { return data ? data : ''; } },
				{ className:"status_observation", data: "status_observation", render: function(data) { return data ? data : ''; } },
				{ 
					data: "receipt_id",
					sortalbe: false,
					render: function( data, type, full, meta ) {
						var statusButton = canChangeReceiptStatus ? `<a href="#" onclick="editStatusRecibo(this, ${full.receipt_id})" title="Cambiar estado" class="action fa fa-check-square-o pl-1"></a>` : '';
						return `<a href="#" onclick="editrecibo(this, ${full.receipt_id})"><i class="action fa fa-edit "></i></a>
						<a href="#" onclick="delete_reg(${full.receipt_id}, 'receipt_id', 'receipt', 'Recibo')" class="action fa fa-trash pl-1 remove_list"></i></a>
						<a target="_blank" href="<?= site_url() ?>recibo/reporte/${full.receipt_id}" class="action fa fa-file-pdf-o pl-1"></i></a>
						${statusButton}`
					}
				},
			]
		});

		//sum column price on datatable

	}
</script>