<div id="modal_detail" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Agregar Detalle</h4>
			</div>
			<input type="hidden" name="thispage" id="thispage" value="edit">
			<form id="form_add_detail">
				<input type="hidden" name="id_detalle" id="index_to" value="">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="control-label">Detalle</label>
								<select class="form-control input_detail" name="detalle" id="detalle" required>
									<option disabled selected value="">Select</option>
									<option value="camisa">Camisa</option>
									<option value="camisa_mangalarga">Camisa Mangalarga</option>
									<option value="camisa_mangacorta">Camisa Mangacorta</option>
									<option value="polo">Polo</option>
									<option value="polo_mangalarga">Polo Mangalarga</option>
									<option value="polo_mangacorta">Polo Mangacorta</option>
									<option value="pantalon">Pantalon</option>
									<option value="gorro">Gorro</option>
									<option value="chaleco">Chaleco</option>
									<option value="casaca">Casaca</option>
									<option value="fotocheck">Fotocheck</option>
									<option value="tocasdetela">Tocas de tela</option>
									<option value="mandil">Mandil</option>
									<option value="trajeprotector">Traje Protector</option>
									<option value="botasdejebe">Botas de jebe</option>
									<option value="lentes">Lentes</option>
									<option value="zapatilla">Zapatilla</option>
									<option value="botines">Botines</option>
									<option value="casco">casco</option>
									<option value="mascarilla">Mascarilla</option>
									<option value="protectorfacial">Protector Facial</option>
									<option value="guantes">Guantes</option>
									<option value="tapaoidos">Tapa Oidos</option>
									<option value="poncho_inpermeable">Poncho inpermeable</option>
									<option value="canguro">Canguro</option>
									<option value="cross">Cross</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="control-label">Color</label>
								<input type="text" name="color" id="color" class="form-control input_detail">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="control-label">Talla</label>
								<input type="text" name="talla" id="talla" class="form-control input_detail">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="control-label">Cantidad</label>
								<input type="number" name="cantidad" id="cantidad" class="form-control input_detail" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="control-label">Estado</label>
								<select class="form-control input_detail" name="estado" id="estado" required>
									<option disabled selected value="">Select</option>
									<option value="malo">Malo</option>
									<option value="bueno">Bueno</option>
									<option value="nuevo">Nuevo</option>
									<option value="usado">Usado</option>
								</select>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="" class="control-label">Posesión</label>
								<select class="form-control input_detail" name="posesion" id="posesion" required>
									<option disabled selected value="">Seleccionar</option>
									<option value="trabajador">Trabajador</option>
									<option value="segelim">Segelim</option>
								</select>
							</div>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="" class="control-label">Comentario</label>
								<textarea class="form-control" name="comentario" id="comentario" cols="30" rows="3"></textarea>
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

<!-- Start content -->
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">Register Boletas de Asignacion de uniformes</h4>

					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#">Asignación de uniformes</a></li>
						<li class="breadcrumb-item active">Register</li>
					</ol>

					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 col-xs-12">
				<div class="card-box">
					<h4 class="header-title m-t-0 m-b-20">Asignación de uniformes</h4>
					<form id="form_edit_boleta" method="POST">
						<div id="crate_form" class="form-group col-md-12 col-sm-12 col-xs-12 row">
							<div class="col-lg-6 col-xs-12 mb-2">
								<label for="">Apellido Paterno</label>
								<input type="text" name="apellido_pat" id="apellido_pat" class="form-control" placheholder="Ingrese apellido paterno" value="<?= $boleta[0]->apellido_pat ?>" required>
							</div>
							<div class="col-lg-6 col-xs-12 mb-2">
								<label for="">Apellido Materno</label>
								<input type="text" name="apellido_mat" id="apellido_mat" class="form-control" placheholder="Ingrese apellido materno" value="<?= $boleta[0]->apellido_mat ?>" required>
							</div>
							<div class="col-lg-6 col-xs-12 mb-2">
								<label for="nombres">Nombres</label>
								<input type="text" name="nombres" id="nombres" class="form-control" placheholder="Ingrese nombres" value="<?= $boleta[0]->nombres ?>" required>
							</div>
							<div class="col-lg-6 col-xs-12 mb-2">
								<label for="dni">N° de Identificación</label>
								<input type="number" name="dni" id="dni" class="form-control" placheholder="Ingrese nro de identificacion" value="<?= $boleta[0]->dni ?>" required>
							</div>
							<div class="col-lg-6 col-xs-12 mb-2">
								<label for="">Nro° Boleta</label>
								<input type="number" disabled class="form-control" placheholder="Ingrese numero de boleta" required value="<?= $boleta[0]->nro_boleta ?>">
								<input type="hidden" id="nro_boleta" name="nro_boleta" value="<?= $boleta[0]->nro_boleta ?>">
							</div>

							<div class="col-lg-6 col-xs-12 mb-2">
								<label>Fecha</label>
								<div class="input-group">
									<input type="text" name="fecha" class="form-control datepicker-autoclose" placeholder="mm/dd/yyyy" data-date-format="yyyy-mm-dd" value="<?= $boleta[0]->fecha ?>">
									<div class="input-group-append">
										<span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
									</div>
								</div>
							</div>
							<div class="col-lg-12 col-xs-12 mb-2">
								<label for="observacion">Observación</label>
								<textarea class="form-control" rows="5" name="observacion" id="observacion"><?= $boleta[0]->observacion ?></textarea>
							</div>
							<div class="col-lg-12 col-xs-12 my-2">
								<div class="text-center">
									<button type="submit" class="btn btn-primary" style="font-weight: bold">MODIFICAR</button>
								</div>
							</div>
							<div class="col-lg-12 col-xs-12 mt-2">
									<div class="col-lg-12 col-xs-6 text-right mb-2">
										<a href="#" id="add_detail" class="btn btn-secondary justify-content-center justify-content-md-between align-items-center">
											AGREGAR
											<i class="fa fa-plus-circle" style="font-size: 25px"></i></a></th>
										</a>
									</div>
								<table class="table">
									<thead class="thead-light">
										<tr>
											<th class="text-center">Detalle</th>
											<th class="text-center">Color</th>
											<th class="text-center">Talla</th>
											<th class="text-center">Cantidad</th>
											<th class="text-center">Estado</th>
											<th class="text-center">Posesion</th>
											<th class="text-center">Acciones</th>
										</tr>
									</thead>
									<tbody id="columns_table">
										<?php if ($detalle_boleta): ?>
											<?php foreach ($detalle_boleta as $value): ?>
												<tr id="tr_<?= $value->id_detalle ?>" data-index="<?= $value->id_detalle ?>">
													<td class="input_detail_row detalle"><?= str_replace("_", " ", $value->detalle) ?></td>
													<td class="input_detail_row color"><?= $value->color ?></td>
													<td class="input_detail_row talla"><?= $value->talla ?></td>
													<td class="input_detail_row cantidad"><?= $value->cantidad ?></td>
													<td class="input_detail_row estado"><?= $value->estado ?></td>
													<td class="input_detail_row posesion"><?= $value->posesion ?></td>
													<td class="input_detail_row comentario d-none"><?= $value->comentario ?></td>
													<td class="text-center">
														<a href="#" class="edit_row"><i class="fa fa-edit mr-2" style="font-size: 25px"></i></a>
														<a href="#" data-id_detalle="<?= $value->id_detalle ?>" class="delete_row"><i class="fa fa-trash" style="font-size: 25px"></i></a>
													</td>
												</tr>
											<?php endforeach ?>
										<?php endif ?>
									</tbody>
								</table>
							</div>
						</div>
						
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('.datepicker-autoclose').datepicker({
		autoclose: true,
		todayHighlight: true
	});
</script>
<script src="<?=base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>

<script type="text/javascript" src="<?=base_url()?>js/validatorinput.js"></script>
<script src="<?= base_url()?>assets/js/backend/boletas_uniforme/index.js?v=<?=$this->config->item("curr_ver");?>"></script>

