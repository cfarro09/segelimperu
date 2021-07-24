<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />

<link href="<?= base_url(); ?>assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
<style>
	.Vencido{
		background: #FF6666;
		color: white;
	}
	.Pronto{
		background: #FFF083;
	}
</style>
<div id="modal_renovar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Renovación de Contrato</h4>
            </div>
            <form id="form_renovar">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="field-1" class="control-label">Trabajador</label>
                                <input type="hidden" name="idpers" id="id_usu">
                                <input required type="text" class="form-control" id="nombre_usu" disabled=>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
								<label>Inicio de Contrato</label>
								<div class="input-group">
									<input required type="text" name="start_cont" class="form-control datepicker-autoclose" placeholder="mm/dd/yyyy" id="start_cont" data-date-format="yyyy-mm-dd">
									<div class="input-group-append">
										<span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
									</div>
								</div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Dur. Meses</label>
                                <input required type="number" id="int_meses" class="form-control" name="time_meses">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
								<label class="control-label">Dur. Dias</label>
                                <input required type="number" id="int_dias" class="form-control" name="time_dias">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">Tipo Cont</label>
                                <select required class="form-control" name="tipo_con">
                                	<option value="" disabled selected>Select a tipo</option>
                                	<option value="Planilla">Planilla</option>
                                	<option value="RRHH">RR.HH</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
								<label class="control-label">Empresa</label>
                                <select required class="form-control" name="empresa_cont">
                                	<option value="" disabled selected>Select a Empresa</option>
                                	<option value="SegelimPeru">Segelim Peru</option>
                                	<option value="SolucionesAmbientales">Soluciones Ambientales</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">RENOVAR</button>
                </div>
            </form>
        </div>
    </div>
</div><!-- /.modal -->
<div id="modal_historial" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog" style="max-width: 800px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="title-historial">Historial de Contratos</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <table id="datatablehistory" class="table table-responsive" cellspacing="0" width="100%">
					</table>
                </div>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
<!-- Start content -->
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">ULTIMO CONTRATO DE CADA TRABAJADOR</h4>

					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#">Gestor</a></li>
						<li class="breadcrumb-item active">Gestor de Contratos</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-10 col-xs-12">
				<div class="card-box">
					<h4 class="header-title m-t-0 m-b-20">Ultimo contrato del personal laborando</h4>
					<div class="form-group m-b-20" id="div_range_picker">
						<label>Filtrar fecha de vencimiento</label>
						<div class="col-md-4 col-xs-12">
							<input class="form-control input-daterange-datepicker" id="range_date_cont" type="text" name="daterange"/>
						</div>
					</div>
					<table id="datatable" class="table" cellspacing="0" width="100%">
						<thead class="thead-light">
							<tr>
								<th>Nombre</th>
								<th>Nro Documento</th>
								<th>Contrato Vencimiento</th>
								<th>Foquito</th>
								<th>Opciones</th>
							</tr>
						</thead>
						<tbody>
							<?php if ($personal): ?>
								<?php foreach ($personal as $empl): ?>
									<?php  $date_venc = new DateTime(date('Y-m-d',strtotime($empl->end_cont))); 
									$now = new DateTime(date("Y-m-d"));
									$interval = (int) $now->diff($date_venc)->format('%R%a');
									$opcion = ($interval < 0 ? "Vencido" : ($interval < 5 ? "Pronto" : "")) 
									?>
									<tr class="<?= $opcion ?>">
										<td class="nombres"><?= $empl->nombres ?></td>
										<td class=""><?= $empl->nro_doc ?></td>
										<td class="end_cont"><?= $empl->end_cont ?></td>
										<td class=""><?= $opcion ?></td>
										<td class="row text-center" data-endcont="<?= $empl->end_cont ?>" data-name="<?= $empl->nombres ?>" data-id="<?= $empl->idpers ?>">
											<a href="#" class="show_renovar">Renovar</a>
											<a href="#" class="show_historial pl-2 pr-2">Historial</a>
											<a href="#" class="desafiliar pl-2 pr-2">Desafiliar</a>
											<a href="#" data-idcont="<?= $empl->id_cont  ?>" class="editar_cont pl-2 pr-2">Editar</a>
										</td>
									</tr>
								<?php endforeach ?>
							<?php endif ?>
						</tbody>
					</table>
					<div class="form-group m-b-20 text-center pt-4" id="div_range_picker">
						<label>¿No encuentras el empleado?</label>
						<label >Talvez sea un trabajador nuevo</label>
						<div>
							<button id="add_register_cont" class="btn btn-primary waves-effect">Agregar Contrato</button>
						</div>
						<span class="help-block"><small>*Agregue solo un contrato para el empleado que recien se incorporó</small></span>
					</div>
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

<!-- Date Range Picker -->
<script src="<?= base_url()?>assets/plugins/moment/moment.js"></script>
<script src="<?= base_url()?>assets/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>

<!-- Toast -->
<script src="<?= base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script src="<?= base_url()?>assets/js/backend/seg_contratos/seg_contratos.js?v=<?=$this->config->item("curr_ver");?>"></script>