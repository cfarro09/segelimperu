<script src="<?= base_url() ?>assets/js/croppie.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>assets/css/croppie.css" />

<style>
	.custom-file-upload {
		display: block;
		cursor: pointer;
	}

	input[type="file"] {
		display: none;
	}

	.borderfile {
		border: 1px solid #c0bbbb;
		border-radius: 15px;
		margin-bottom: 5px;
		padding: 0px;
	}

	.imagex {
		border-radius: 15px 15px 0 0;
	}

	.modal {
		overflow-y: auto;
	}
</style>


<div id="modal_informacionadicional" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog" style="min-width: 900px">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h4 class="modal-title">Información adicional</h4>
			</div>
			<!-- <form id="form_add_detail"> -->
			<input type="hidden" name="index" id="index_to" value="">
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-6 mb-2">
						<div class="card">
							<div class="card-header">Informacion personal</div>
							<div class="card-body row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="sexo" class="control-label">Sexo</label>
										<select class="form-control input_detail" name="sexo" id="sexo" required>
											<option disabled selected value="">Seleccione</option>
											<option value="Masculino">Masculino</option>
											<option value="Femenino">Femenino</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="edad" class="control-label">Edad</label>
										<input type="number" name="edad" id="edad" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="nacionalidad" class="control-label">Nacionalidad</label>
										<input type="text" name="nacionalidad" id="nacionalidad" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="correo" class="control-label">Correo</label>
										<input type="text" name="correo" id="correo" class="form-control input_detail">
									</div>
								</div>
								<div id="div_fecha_nacimiento" class="mb-3 col-lg-6 col-xs-12">
									<label>Fecha nacimiento</label>
									<div class="input-group">
										<input required name="fecha_nacimiento" id="fecha_nacimiento" type="text" class="form-control input_detail datepicker-autoclose" placeholder="mm/dd/yyyy" data-date-format="yyyy-mm-dd">
										<div class="input-group-append"><span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="departamento" class="control-label">departamento</label>
										<input type="text" name="departamento" id="departamento" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="provincia" class="control-label">provincia</label>
										<input type="text" name="provincia" id="provincia" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="distrito" class="control-label">distrito</label>
										<input type="text" name="distrito" id="distrito" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-12 col-xs-12 divparent borderfile">
									<input type="hidden" name="rutafoto" id="rutafoto" class="input_detail">
									<img class="imagex" style="width: 100%">
									<div class="uploadfile text-center py-2">
										<label class="custom-file-upload pointer mb-0" style="font-size: 20px; bottom: 0px">
											<!-- <input type="file" data-tag="rutafoto" name="file" accept="image/*" onchange="loadimage(this)" /> -->
											<input type="file" name="upload_image" class="upload_image" data-tag="rutafoto" accept="image/*">
											<span><i class="fa fa-camera pointer" style="font-size: 20px;"></i></span>
											<span class="" style="color: #c3c3c3; font-size: 16px">Subir Foto (210 x 280)</span>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-6 mb-2">
						<div class="card">
							<div class="card-header">Nivel Educativo</div>
							<div class="card-body row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="educacionprimaria" class="control-label">Primaria</label>
										<input type="text" name="educacionprimaria" id="educacionprimaria" class="form-control input_detail">
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="educacionsecundaria" class="control-label">Secundaria</label>
										<input type="text" name="educacionsecundaria" id="educacionsecundaria" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="educacionsuperior" class="control-label">Superior</label>
										<input type="text" name="educacionsuperior" id="educacionsuperior" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="educacioncarrera" class="control-label">Carrera</label>
										<input type="text" name="educacioncarrera" id="educacioncarrera" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="educaciontitulo" class="control-label">Titulo Obtenido</label>
										<input type="text" name="educaciontitulo" id="educaciontitulo" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-12 col-xs-12 divparent borderfile">
									<input type="hidden" name="rutafirmahuella" id="rutafirmahuella" class="input_detail">
									<img class="imagex" style="width: 100%">
									<div class="uploadfile text-center py-2">
										<label class="custom-file-upload pointer mb-0" style="font-size: 20px; bottom: 0px">
											<!-- <input type="file" data-tag="rutafirmahuella" name="file" accept="image/*" onchange="loadimage(this)" /> -->
											<input type="file" name="upload_image" class="upload_image" data-tag="rutafirmahuella" accept="image/*">
											<span><i class="fa fa-camera pointer" style="font-size: 20px;"></i></span>
											<span class="" style="color: #c3c3c3; font-size: 16px">Subir Firma + Huella (700 x 300)</span>
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12 mb-2">
						<div class="card">
							<div class="card-header">Experiencia laboral</div>
							<div class="card-body ">
								<div class="row">
									<div class="col-sm-6 ">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Empresa</label>
													<input type="text" class="form-control empresa1" data-name="empresa">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Cargo</label>
													<input type="text" class="form-control empresa1" data-name="cargo">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Duracion</label>
													<input type="text" class="form-control empresa1" data-name="duracion">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Jefe</label>
													<input type="text" class="form-control empresa1" data-name="jefe">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Telefono</label>
													<input type="text" class="form-control empresa1" data-name="telefono">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Motivo Retiro</label>
													<input type="text" class="form-control empresa1" data-name="motivoretiro">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Remuneracion</label>
													<input type="text" class="form-control empresa1" data-name="remuneracion">
												</div>
											</div>
										</div>
									</div>
									<div class="col-sm-6 ">
										<div class="row">
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Empresa</label>
													<input type="text" class="form-control empresa2" data-name="empresa">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Cargo</label>
													<input type="text" class="form-control empresa2" data-name="cargo">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Duracion</label>
													<input type="text" class="form-control empresa2" data-name="duracion">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Jefe</label>
													<input type="text" class="form-control empresa2" data-name="jefe">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Telefono</label>
													<input type="text" class="form-control empresa2" data-name="telefono">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Motivo Retiro</label>
													<input type="text" class="form-control empresa2" data-name="motivoretiro">
												</div>
											</div>
											<div class="col-sm-6">
												<div class="form-group">
													<label class="control-label">Remuneracion</label>
													<input type="text" class="form-control empresa2" data-name="remuneracion">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6 mb-2">
						<div class="card">
							<div class="card-header">Datos del Conyuge o Conviviente</div>
							<div class="card-body row">
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label">Vinculo</label>
										<input type="text" class="form-control conyuge" data-name="vinculo">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label">Sexo</label>
										<input type="text" class="form-control conyuge" data-name="sexo">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label">Nombre completo</label>
										<input type="text" class="form-control conyuge" data-name="nombrecompleto">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label">DNI</label>
										<input type="text" class="form-control conyuge" data-name="dni">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label">Telefono</label>
										<input type="text" class="form-control conyuge" data-name="telefono">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label">F. Nacimiento</label>
										<input type="text" class="form-control conyuge" data-name="fecha_nacimiento">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label">Nacionalidad</label>
										<input type="text" class="form-control conyuge" data-name="nacionalidad">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label">Ocupación</label>
										<input type="text" class="form-control conyuge" data-name="ocupacion">
									</div>
								</div>
								<div class="col-sm-6">
									<div class="form-group">
										<label class="control-label">Lugar Trabajo</label>
										<input type="text" class="form-control conyuge" data-name="lugartrabajo">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6 mb-2">
						<div class="card">
							<div class="card-header">Información adicional</div>
							<div class="card-body row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="antecedentes" class="control-label">Antecedentes</label>
										<select name="antecedentes" id="antecedentes" class="form-control input_detail">
											<option value="NO">NO</option>
											<option value="SI">SI</option>
										</select>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="sindicato" class="control-label">Sindicato</label>
										<input type="text" name="sindicato" id="sindicato" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="emergencias" class="control-label">Emergencias</label>
										<input type="text" name="emergencias" id="emergencias" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="nroemergencias" class="control-label">Nro Emergencias</label>
										<input type="text" name="nroemergencias" id="nroemergencias" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-12">
									<div class="form-group">
										<label for="direccion" class="control-label">Direccion</label>
										<input type="text" name="direccion" id="direccion" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="padrenombrecompleto" class="control-label">Nombre del Padre</label>
										<input type="text" name="padrenombrecompleto" id="padrenombrecompleto" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="padreocupacion" class="control-label">Ocupacion del Padre</label>
										<input type="text" name="padreocupacion" id="padreocupacion" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="madrenombrecompleto" class="control-label">Nombre de la Madre</label>
										<input type="text" name="madrenombrecompleto" id="madrenombrecompleto" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="madreocupacion" class="control-label">Ocupación de la madre</label>
										<input type="text" name="madreocupacion" id="madreocupacion" class="form-control input_detail">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12 mb-2">
						<div class="card">
							<div class="card-header">Datos de los hijos</div>
							<div class="card-body row">
								<button type="button" class="btn btn-secondary" onclick="mostrarmodalhijos()">AGREGAR</button>
								<table class="table">
									<thead>
										<th>Nombre Completo</th>
										<th>F. Nacimiento</th>
										<th>DNI</th>
										<th>Grado Instruccion</th>
										<th>Ocupacion</th>
										<th>Vive con Ud</th>
										<th>ESSALUD</th>
										<th>Acciones</th>
									</thead>
									<tbody id="body_hijos"></tbody>
								</table>

							</div>
						</div>
					</div>
					<div class="col-sm-6 mb-2">
						<div class="card">
							<div class="card-header">Ubicacion del Domicilio</div>
							<div class="card-body row">

								<div class="col-md-6">
									<div class="form-group">
										<label for="direccionactual" class="control-label">Direccion Actual</label>
										<input type="text" name="direccionactual" id="direccionactual" class="form-control input_detail">
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="telefonoactual" class="control-label">Telefono Actual</label>
										<input type="text" name="telefonoactual" id="telefonoactual" class="form-control input_detail">
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="distritoactual" class="control-label">Distrito Actual</label>
										<input type="text" name="distritoactual" id="distritoactual" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="provinciaactual" class="control-label">Provincia Actual</label>
										<input type="text" name="provinciaactual" id="provinciaactual" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="departamentoactual" class="control-label">Departamento Actual</label>
										<input type="text" name="departamentoactual" id="departamentoactual" class="form-control input_detail">
									</div>
								</div>
								<div class="col-md-12 col-xs-12 divparent borderfile">
									<input type="hidden" name="rutamapa" id="rutamapa" class="input_detail">
									<img class="imagex" style="width: 100%">
									<div class="uploadfile text-center py-2">
										<label class="custom-file-upload pointer mb-0" style="font-size: 20px; bottom: 0px">
											<!-- <input type="file" data-tag="rutamapa" name="file" accept="image/*" onchange="loadimage(this)" /> -->
											<input type="file" name="upload_image" class="upload_image" data-tag="rutamapa" accept="image/*">
											<span><i class="fa fa-camera pointer" style="font-size: 20px;"></i></span>
											<span class="" style="color: #c3c3c3; font-size: 16px">Subir Mapa (600 x 200)</span>
										</label>
									</div>

									</label>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6 mb-2">
						<div class="card">
							<div class="card-header">Medida Indumentaria</div>
							<div class="card-body row">

								<div class="col-md-6">
									<div class="form-group">
										<label for="tallacamisa" class="control-label">Talla Camisa</label>
										<input type="text" name="tallacamisa" id="tallacamisa" class="form-control input_detail">
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="tallapantalon" class="control-label">Talla Pantalon</label>
										<input type="text" name="tallapantalon" id="tallapantalon" class="form-control input_detail">
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
										<label for="tallacalzado" class="control-label">Talla Calzado</label>
										<input type="text" name="tallacalzado" id="tallacalzado" class="form-control input_detail">
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-6 col-xs-12 mb-2 divparent borderfile">
						<input type="hidden" name="rutaimagenantecedentes" id="rutaimagenantecedentes" class="input_detail">
						<img class="imagex" style="width: 100%">
						<div class="uploadfile text-center py-2">
							<label class="custom-file-upload pointer mb-0" style="font-size: 20px; bottom: 0px">
								<input type="file" data-tag="rutaimagenantecedentes" name="file" accept="image/*" onchange="loadimage(this)" />
								<span><i class="fa fa-camera pointer" style="font-size: 20px;"></i></span>
								<span class="" style="color: #c3c3c3; font-size: 16px">Subir Imagen Antecedentes</span>
							</label>
						</div>
					</div>
					<div class="col-md-6 col-xs-12 divparent borderfile">
						<input type="hidden" name="rutaimagendni" id="rutaimagendni" class="input_detail">
						<img class="imagex" style="width: 100%">
						<div class="uploadfile text-center py-2">
							<label class="custom-file-upload pointer mb-0" style="font-size: 20px; bottom: 0px">
								<input type="file" data-tag="rutaimagendni" name="file" accept="image/*" onchange="loadimage(this)" />
								<span><i class="fa fa-camera pointer" style="font-size: 20px;"></i></span>
								<span class="" style="color: #c3c3c3; font-size: 16px">Subir Imagen DNI</span>
							</label>
						</div>
					</div>
					<div class="col-md-6 col-xs-12 divparent borderfile">
						<input type="hidden" name="rutaimagenreciboservicios" id="rutaimagenreciboservicios" class="input_detail">
						<img class="imagex" style="width: 100%">
						<div class="uploadfile text-center py-2">
							<label class="custom-file-upload pointer mb-0" style="font-size: 20px; bottom: 0px">
								<input type="file" data-tag="rutaimagenreciboservicios" name="file" accept="image/*" onchange="loadimage(this)" />
								<span><i class="fa fa-camera pointer" style="font-size: 20px;"></i></span>
								<span class="" style="color: #c3c3c3; font-size: 16px">Subir Imagen Recibo Servicios</span>
							</label>
						</div>
					</div>

					<div class="col-sm-12 mb-2">
						<div class="uploadfile text-center py-2">
							<label class="custom-file-upload pointer mb-0" style="font-size: 20px; bottom: 0px">
								<input type="file" name="file[]" accept="image/*" multiple onchange="loadmultiimage(this)" />
								<span><i class="fa fa-camera pointer" style="font-size: 20px;"></i></span>
								<span class="" style="color: #c3c3c3; font-size: 16px">Agregar Imagenes Extras</span>
							</label>
						</div>
						<div class="row" id="containerimagenesextras">

						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
			</div>
			<!-- </form> -->
		</div>
	</div>
</div><!-- /.modal -->

<div id="modal_hijo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="form_hijos">
				<div class="modal-header" id="title_mhijos">Hijos</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="mhijos_nombrecompleto" class="control-label">Nombre Completo</label>
								<input type="text" required name="nombrecompleto" id="mhijos_nombrecompleto" class="form-control">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="mhijos_fechanacimiento" class="control-label">Fecha Nacimiento</label>
								<input type="text" required name="fechanacimiento" id="mhijos_fechanacimiento" class="form-control">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="mhijos_dni" class="control-label">N° DNI</label>
								<input type="text" required name="dni" id="mhijos_dni" class="form-control">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="mhijos_grado" class="control-label">Grado Instruccion</label>
								<input type="text" required name="grado" id="mhijos_grado" class="form-control">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="mhijos_ocupacion" class="control-label">Ocupacion</label>
								<input type="text" required name="ocupacion" id="mhijos_ocupacion" class="form-control">
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="mhijos_viveconud" class="control-label">Vive con UD</label>
								<select required name="viveconud" id="mhijos_viveconud" class="form-control">
									<option value="SI">SI</option>
									<option value="NO">NO</option>
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="mhijos_seguroessalud" class="control-label">Seguro ESSALUD</label>
								<select required name="seguroessalud" id="mhijos_seguroessalud" class="form-control">
									<option value="SI">SI</option>
									<option value="NO">NO</option>
								</select>
							</div>

						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cerrar</button>
						<button type="submit" id="btn_submit" class="btn btn-info waves-effect waves-light">GUARDAR</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- Start content -->
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">Editar Personal</h4>

					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#">Personal</a></li>
						<li class="breadcrumb-item active">Editar</li>
					</ol>

					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 col-xs-12">
				<div class="card-box">
					<h4 class="header-title m-t-0 m-b-20">Personal</h4>
					<form id="form_generate_register" method="POST">
						<div id="crate_form" class="form-group col-md-12 col-sm-12 col-xs-12 row">
                            <input type="hidden" id="idPersonal" name="idPersonal" class="input_detail">
							<div id="div_nrofile" class="col-lg-6 col-xs-12 mb-2">
								<label>Nro File</label>
								<input required autocomplete="off" id="nrofile" name="nrofile" type="text" class="input_detail form-control letrasnumeros" maxlength="10">
							</div>
							<div id="div_apellido_pat" class="col-lg-6 col-xs-12 mb-2">
								<label>Apellido Paterno</label>
								<input required autocomplete="off" id="apellido_pat" name="apellido_pat" type="text" class="form-control sololetras input_detail" maxlength="100">
							</div>
							<div id="div_apellido_mat" class="col-lg-6 col-xs-12 mb-2">
								<label>Apellido Materno</label>
								<input required autocomplete="off" id="apellido_mat" name="apellido_mat" type="text" class="form-control sololetras input_detail" maxlength="100">
							</div>
							<div id="div_nombres" class="col-lg-6 col-xs-12 mb-2">
								<label>Nombres</label>
								<input required autocomplete="off" id="nombres" name="nombres" type="text" class="input_detail form-control sololetras" maxlength="100">
							</div>
							<div id="div_tipodocumento" class="mb-2 col-lg-6 col-xs-12">
								<label>Tipo Documento</label>
								<select required class="form-control input_detail" id="tipodocumento" name="tipodocumento">
									<option value="" disabled selected>Seleccione</option>
									<option value="DNI">DNI</option>
									<option value="CARNETEXT">CARNETEXTRANJERIA</option>
									<option value="PTP">PTP</option>
									<option value="CEDULAIDENTIDIDAD">CEDULA DE IDENTIDAD</option>
									<option value="PASAPORTE">PASAPORTE</option>
								</select>
							</div>
							<div id="div_nrodocumento" class="col-lg-6 col-xs-12 mb-2">
								<label>Nro Documento</label>
								<input required autocomplete="off" id="nrodocumento" name="nrodocumento" type="text" class="form-control letrasnumeros_especiales input_detail" maxlength="100">
							</div>
							<div id="div_tipocontrato" class="mb-2 col-lg-6 col-xs-12">
								<label>Tipo Contrato</label>
								<select required class="input_detail form-control" id="tipocontrato" name="tipocontrato">
									<option value="" disabled selected>Seleccione</option>
									<option value="PRUEBA">PRUEBA</option>
									<option value="PLANILLA">PLANILLA</option>
								</select>
							</div>
							<div id="div_observacion" class="col-lg-12 col-xs-12 mb-2">
								<label>Observacion</label>
								<textarea required autocomplete="off" id="observacion" name="observacion" type="text" class="form-control input_detail" rows="5"></textarea>
							</div>
							<div id="div_fecha_ingreso" class="mb-3 col-lg-6 col-xs-12">
								<label>Fecha Ingreso</label>
								<div class="input-group">
									<input required autocomplete="off" id="fecha_ingreso" name="fecha_ingreso" type="text" class="input_detail form-control datepicker-autoclose" placeholder="mm/dd/yyyy" data-date-format="yyyy-mm-dd">
									<div class="input-group-append"><span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
									</div>
								</div>
							</div>
							<div id="div_estado" class="mb-2 col-lg-6 col-xs-12">
								<label>estado</label>
								<select required class="input_detail form-control" id="estado" name="estado">
									<option value="" disabled selected>Seleccione</option>
									<option value="INACTIVO">INACTIVO</option>
									<option value="ACTIVO">ACTIVO</option>
								</select>
							</div>
							<div id="div_fecha_vencimiento" class="mb-3 col-lg-6 col-xs-12">
								<label>Fecha Vencimiento</label>
								<div class="input-group">
									<input required autocomplete="off" id="fecha_vencimiento" name="fecha_vencimiento" type="text" class="input_detail form-control datepicker-autoclose" placeholder="mm/dd/yyyy" data-date-format="yyyy-mm-dd">
									<div class="input-group-append"><span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
									</div>
								</div>
							</div>
							<div class="col-sm-12 text-center">
								<button type="button" data-toggle="modal" data-target="#modal_informacionadicional" class="btn btn-success">Agregar información adicional</button>
							</div>
						</div>
						<div class="text-right">
							<button type="submit" class="btn btn-primary" style="font-weight: bold">REGISTRAR</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="uploadimageModal" class="modal" role="dialog">
	<div class="modal-dialog" style="max-width: 750px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Upload & Crop Image</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-9 text-center">
						<div id="image_demo" style="width:350px; margin-top:30px"></div>
					</div>
					<div class="col-md-3" style="padding-top:30px;">
						<br />
						<br />
						<br />
						<button class="btn btn-success crop_image" data-tag="">Cortar y Subir</button>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
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
<script src="<?= base_url() ?>assets/js/backend/toast_alert.js?v=<?= $this->config->item("curr_ver"); ?>"></script>

<script type="text/javascript" src="<?= base_url() ?>js/validatorinput.js"></script>
<script type="text/javascript">
	const getSelector = (tag) => document.querySelector(tag)
	const getSelectorAll = (tag) => document.querySelectorAll(tag)
	const fetch_post = async (url, data) => {
		$("#loading-circle-overlay").show();
		const response = await fetch(url, {
			method: 'POST',
			body: data,

		});
		$("#loading-circle-overlay").hide();
		if (response.ok) {
			try {
				return await response.json();
			} catch (e) {
				toast_error('¡Oh, hubo un error!', 'Vuelva a intentarlo.');
			}
		} else {
			toast_error('¡Oh, hubo un error!', 'Vuelva a intentarlo.');
		}
	}
	const fetch_post_file = async (url, data) => {
		$("#loading-circle-overlay").show();
		const response = await fetch(`${site_url}/${url}`, {
			method: 'POST',
			body: data,
		});
		$("#loading-circle-overlay").hide();
		if (response.ok) {
			try {
				return await response.json();
			} catch (e) {
				toast_error('¡Oh, hubo un error!', 'Vuelva a intentarlo.');
			}
		} else {
			toast_error('¡Oh, hubo un error!', 'Vuelva a intentarlo.');
		}
	}

	let hijoseleccionado = "";
	form_generate_register.addEventListener("submit", e => {
		e.preventDefault();

		const empresas = [];
		const hijos = [];
		const conyuge = {};
		const imagesextras = [];

		getSelectorAll("#body_hijos tr").forEach(ii => {
			const rowhijo = {}
			ii.querySelectorAll(".inputvalue").forEach(ix => {
				rowhijo[ix.dataset.name] = ix.textContent
			});
			hijos.push(rowhijo)
		});

		getSelectorAll(".conyuge").forEach(ii => {
			conyuge[ii.dataset.name] = ii.value;
		});
		getSelectorAll(".imageextra").forEach(ii => {
			imagesextras.push(ii.value);
		});

		const rowempresa1 = {};
		const rowempresa2 = {};
		getSelectorAll(".empresa1").forEach(ii => {
			rowempresa1[ii.dataset.name] = ii.value;
		});
		getSelectorAll(".empresa2").forEach(ii => {
			rowempresa2[ii.dataset.name] = ii.value;
		});
		empresas.push(rowempresa1);
		empresas.push(rowempresa2);

		const data = new FormData();
		getSelectorAll(".input_detail").forEach(inx => {
			data.append(inx.name, inx.value);
		});
		data.append("empresas", JSON.stringify(empresas));
		data.append("hijos", JSON.stringify(hijos));
		data.append("conyuge", JSON.stringify(conyuge));
		data.append("rutasextras", JSON.stringify(imagesextras));

		fetch_post(`${site_url}/personal/insertar`, data).then(res => {
			if (res) {
				if (res.success) {
					limpiar()
					swal('Bien!', res.msg, 'success');
				} else {
					swal('Error!', 'Hubo un error, vuelva a intentarlo', 'error')
				}
			}
		});
	})
	const limpiar = () => {
		form_generate_register.reset();
		body_hijos.innerHTML = "";
		getSelectorAll(".input_detail").forEach(inx => {
			inx.value = ""
		});

		getSelectorAll(".conyuge").forEach(ii => {
			ii.value = ""
		});
		getSelectorAll(".empresa1").forEach(ii => {
			// ii.value = ""
		});
		getSelectorAll(".empresa2").forEach(ii => {
			// ii.value = ""
		});
	}
	const mostrarmodalhijos = () => {
		title_mhijos.textContent = "Registrar Hijo"
		hijoseleccionado = "";
		$("#modal_hijo").modal()
		document.querySelector("#form_hijos").reset();
	}

	function crear_crop(w, h) {
		$image_crop = $('#image_demo').croppie({
			enableExif: true,
			viewport: {
				width: w,
				height: h,
				type: 'square' //circle
			},
			boundary: {
				width: 500,
				height: 500
			}
		});
		return $image_crop;
	}

	$(document).ready(function() {
		document.querySelector("#form_hijos").addEventListener("submit", guardarhijo);
		const data = {};
		<?php foreach ($r as $key => $value) : ?>
			data['<?= $key ?>'] = '<?= str_replace("'", "", $value) ?>';
			if (getSelector('#<?= $key ?>'))
				getSelector('#<?= $key ?>').value = '<?= str_replace("'", "", $value) ?>';
		<?php endforeach ?>
        
		rutafoto.parentElement.querySelector(".imagex").src = data.rutafoto;
		rutafirmahuella.parentElement.querySelector(".imagex").src = data.rutafirmahuella;
		rutamapa.parentElement.querySelector(".imagex").src = data.rutamapa;
        
        rutaimagendni.parentElement.querySelector(".imagex").src = data.rutaimagendni;
        rutaimagenantecedentes.parentElement.querySelector(".imagex").src = data.rutaimagenantecedentes;
        rutaimagenreciboservicios.parentElement.querySelector(".imagex").src = data.rutaimagenreciboservicios;
        


		const empresas = data.empresas != null && data.empresas != "" ? JSON.parse(data.empresas) : [];
		const conyuge = data.conyuge ? JSON.parse(data.conyuge) : {};
		const hijos = data.hijos ? JSON.parse(data.hijos) : [];
		const imagesextras = data.rutasextras ? JSON.parse(data.rutasextras) : [];

		imagesextras.forEach(ii => {
			containerimagenesextras.innerHTML += `
					<div class="col-sm-5 my-2 mr-3 p-3 divparent position-relative" style="border-radius: 10px;border: 1px solid;">
						<button type="button" class="position-absolute close" style="top: -15px; right: -15px;" onclick="borrarimagen(this)">×</button>
						<span>
							<input type="hidden" class="imageextra" value="${ii}">
							<img style="width: 100%" src="${ii}">
						</span>
					</div>
				`;
		})
		console.log("dd");
		
		for (let h of hijos) {
			let columnstr = "";
			for (const [key, value] of Object.entries(h)) {
				columnstr += `<td class="inputvalue ${key}" data-name="${key}">${value}</td>`;
			}
			columnstr += `
			<td class="text-center" style="font-size: 20px">
			<i style="cursor: pointer" class="fa fa-edit" onclick="editarhijo(this)"></i>
			<i style="cursor: pointer" class="fa fa-trash" onclick="eliminarhijo(this)"></i>
			</td>`;
			body_hijos.innerHTML += `<tr>${columnstr}</tr>`
		}
		if (empresas.length > 0) {
			getSelectorAll(".empresa1").forEach(ox => {
				ox.value = empresas[0][ox.dataset.name]
			});
			getSelectorAll(".empresa2").forEach(ox => {
				ox.value = empresas[1][ox.dataset.name]
			});
		}

		getSelectorAll(".conyuge").forEach(ox => {
			ox.value = conyuge[ox.dataset.name] ? conyuge[ox.dataset.name] : ""
		});

		$('.upload_image').on('change', function() {
			var me = $(this).attr('data-tag');
			var w, h;
			switch (me) {
				case 'rutafoto':
					w = 336;
					h = 448;
					break;
				case 'rutafirmahuella':
					w = 450;
					h = 180;
					break;
				case 'rutamapa':
					w = 484;
					h = 162;
					break;
				default:
					break;
			}

			var reader = new FileReader();
			var cropper = crear_crop(w, h);
			reader.onload = function(event) {
				cropper.croppie('bind', {
					url: event.target.result,
				}).then(function() {
					console.log('jQuery bind complete');
				});
			}
			reader.readAsDataURL(this.files[0]);
			$('.crop_image').attr('data-tag', me);
			$('#uploadimageModal').modal('show');
		});

		$('.crop_image').click(function(event) {
			var me = $(this).attr('data-tag');
			var selector;
			var selector_img;
			$("#loading-circle-overlay").show();
			$image_crop.croppie('result', {
				type: 'canvas',
				size: 'viewport'
			}).then(function(response) {
				$.ajax({
					url: `${site_url}/personal/loadimage`,
					type: "POST",
					data: {
						"image": response
					},
					success: function(data) {
						var res = jQuery.parseJSON(data);
						$('#uploadimageModal').modal('hide');
						$("#loading-circle-overlay").hide();
						$image_crop.croppie('destroy');
						selector = $('#' + me);
						// selector_img = selector.closest('div');
						selector_img = selector.closest(".divparent").children('.imagex');

						selector.val(res.rutafoto);
						selector_img.attr('src', res.rutafoto);
					}
				});
			})
		});



	})
	const guardarhijo = e => {
		e.preventDefault();
		let columnstr = "";
		if (!hijoseleccionado) {
			for (let h of e.target.elements) {
				if (h.tagName != "BUTTON")
					columnstr += `<td class="inputvalue ${h.name}" data-name="${h.name}">${h.value}</td>`;
			}
			columnstr += `
			<td class="text-center" style="font-size: 20px">
			<i style="cursor: pointer" class="fa fa-edit" onclick="editarhijo(this)"></i>
			<i style="cursor: pointer" class="fa fa-trash" onclick="eliminarhijo(this)"></i>
			</td>`;
			body_hijos.innerHTML += `<tr>${columnstr}</tr>`
		} else {
			for (let h of e.target.elements) {
				if (h.tagName != "BUTTON") {
					hijoseleccionado.querySelector(`.${h.name}`).textContent = h.value;
				}
			}
		}
		$("#modal_hijo").modal("hide")

	}
	const editarhijo = e => {
		title_mhijos.textContent = "Editar Hijo"
		hijoseleccionado = e.closest("tr");
		hijoseleccionado.querySelectorAll(".inputvalue").forEach(ee => {
			getSelector(`#mhijos_${ee.dataset.name}`).value = ee.textContent;
		});
		$("#modal_hijo").modal()
	}
	const eliminarhijo = e => {
		e.closest("tr").remove();
	}
	const loadimage = async e => {
		const tag = "#" + e.dataset.tag;
		const imgdd = e.closest(".divparent").querySelector(".imagex");
		const ff = document.createElement("form");
		ff.innerHTML = e.outerHTML
		ff.file.files = e.files
		var fd = new FormData(ff);
		const res = await fetch_post_file('personal/loadimage1', fd);
		if (res) {
			if (res && res.rutafoto) {
				debugger
				getSelector(tag).value = res.rutafoto
				imgdd.src = res.rutafoto;
			} else {
				swal("Error!", res.msg, "error");
			}
			e.value = "";
		}
	}
	const borrarimagen = e => {
		e.closest(".divparent").remove()
	}
	const loadmultiimage = async e => {
		var fd = new FormData();

		var ins = e.files.length;
		for (var x = 0; x < ins; x++) {
			fd.append("files[]", e.files[x]);
		}
		const res = await fetch_post_file('personal/loadmultiimagenes', fd);
		if (res) {
			res.forEach(ex => {
				containerimagenesextras.innerHTML += `
					<div class="col-sm-5 my-2 mr-3 p-3 divparent position-relative" style="border-radius: 10px;border: 1px solid;">
						<button type="button" class="position-absolute close" style="top: -15px; right: -15px;" onclick="borrarimagen(this)">×</button>
						<span>
							<input type="hidden" class="imageextra" value="${ex}">
							<img style="width: 100%" src="${ex}">
						</span>
					</div>
				`;
			})
			e.value = "";
		}
	}
</script>