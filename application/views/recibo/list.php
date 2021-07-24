<link href="<?= base_url() ?>assets/css/form/css.css" rel="stylesheet">

<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" 
type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />


<div id="modal_operation" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
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
								<label for="" class="control-label">Cliente</label>
								<input autocomplete="off" required type="text" name="client" id="client" class="form-control input_detail">
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
								<label for="" class="control-label">N° acta conformidad</label>
								<input autocomplete="off" required type="number"  name="certificate_number" id="certificate_number" class="form-control input_detail">
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
								<label for="" class="control-label">Realizado en</label>
								<input autocomplete="off" required name="made_in" id="made_in" class="form-control input_detail">
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
			<div class="col-md-12 col-xs-12">
				<button class="btn btn-success" onclick="registrarrecibo()">Agregar recibo</button>
				<div class="card-box">
					<table id="datatableaux" class="table" cellspacing="0" width="100%">
					<thead class="thead-light">
							<tr>
								<th>ID</th>
								<th>Cliente</th>
								<th>Monto</th>
								<th>Concepto</th>
								<th>Realizado en</th>
								<th>N° Conformidad</th>
								<th>Fecha</th>
								<th class="text-center">Acciones</th>
							</tr>
						</thead>
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

<script src="<?=base_url()?>assets/js/backend/form/list_form.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script>
	$('.datepicker-autoclose').datepicker({
		autoclose: true,
		todayHighlight: true
	});

	function registrarrecibo () {
		form_recibo.reset();
		receipt_id.value = "0"
		$("#modal_operation").modal();
	}
	function editrecibo (obj, id) {
		receipt_id.value = id;

		client.value = $(obj).parent().parent().find(".client").text();
		price.value = $(obj).parent().parent().find(".price").text(); 
		certificate_number.value = $(obj).parent().parent().find(".certificate_number").text();
		concept.value = $(obj).parent().parent().find(".concept").text();
		made_in.value = $(obj).parent().parent().find(".made_in").text();
		date_receipt.value = $(obj).parent().parent().find(".date_receipt").text();
		$("#modal_operation").modal();
	}
	document.getElementById("form_recibo").onsubmit = function (e) {
		e.preventDefault();
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
		$('#datatableaux').DataTable( {
			"processing": true,
			"ajax": {
				"url": "<?= base_url() ?>recibo/getData",
				"type": "POST"
			},
			"columns": [
				{ className:"receipt_id", data: "receipt_id" },
				{ className:"client", data: "client" },
				{ className:"price", data: "price" },
				{ className:"concept", data: "concept" },
				{ className:"made_in", data: "made_in" },
				{ className:"certificate_number", data: "certificate_number" },
				{ className:"date_receipt", data: "date_receipt" },

				{ 
					data: "receipt_id",
					sortalbe: false,
					render: function( data, type, full, meta ) {
						return `<a href="#" onclick="editrecibo(this, ${full.receipt_id})"><i class="action fa fa-edit "></i></a>
						<a href="#" onclick="delete_reg(${full.receipt_id}, 'receipt_id', 'receipt', 'Recibo')" class="action fa fa-trash pl-1 remove_list"></i></a>
						<a href="<?= site_url() ?>recibo/reporte/${full.receipt_id}" class="action fa fa-file-pdf-o pl-1"></i></a>`
					}
				},
			]
		} );
	}
</script>