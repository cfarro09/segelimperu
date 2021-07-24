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
					<button type="submit" id="btn_submit" class="btn btn-info waves-effect waves-light">AGREGAR</button>
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
					<table id="datatable" class="table" cellspacing="0" width="100%">
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
						<tbody>
							<?php if ($list_recibos): ?>
								<?php foreach ($list_recibos as $recibo): ?>
									<tr class="">
										<td class="receipt_id"><?= $recibo->receipt_id ?></td>
										<td class="client"><?= $recibo->client ?></td>
										<td class="price"><?= $recibo->price ?></td>
										<td class="concept"><?= $recibo->concept ?></td>
										<td class="made_in"><?= $recibo->made_in ?></td>
										<td class="certificate_number"><?= $recibo->certificate_number ?></td>
										<td class="date_receipt"><?= $recibo->date_receipt ?></td>
										<td class="text-center">
											<a href="<?= site_url() ?>personal/editar/<?= $recibo->receipt_id ?>"><i class="action fa fa-edit "></i></a>
											<a href="#" onclick="delete_reg(<?= $recibo->receipt_id ?>, 'idPersonal', 'Personal', 'Personal')" class="action fa fa-trash pl-1 remove_list"></i></a>
											<a href="<?= site_url() ?>recubo/reporte/<?= $recibo->receipt_id ?>" class="action fa fa-file-pdf-o pl-1"></i></a>
											
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

<script>
	$('.datepicker-autoclose').datepicker({
		autoclose: true,
		todayHighlight: true
	});

	function registrarrecibo () {
		receipt_id.value = "0"
		$("#modal_operation").modal();
	}

	document.getElementById("form_recibo").onsubmit = function (e) {
		e.preventDefault();

		fetch_post("<?= base_url() ?>recibo/insertar", $("#form_recibo").serialize()).then(r => {
			console.log(r);
			if (r.success)
				location.reload()
			else
				toast_error('¡Oh, hubo un error!', data.msg);

		})
	}
</script>