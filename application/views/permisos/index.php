<link href="<?=base_url()?>assets/css/form/css.css" rel="stylesheet">
<!-- Start content -->
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">Permisos de Usuarios</h4>

					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminpro</a></li>
						<li class="breadcrumb-item"><a href="#">Permisos</a></li>
						<li class="breadcrumb-item active">Gestor Permisos</li>
					</ol>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 col-xs-12">
				<div class="card-box">
					<h4 class="header-title m-t-0 m-b-20">Gestionar Permisos</h4>
	                <div class="form-group">
						<select class="form-control select2" id="select_category">
							<option value="" disabled selected>Choose Categoría</option>
							<?php foreach ($categories as $value): ?>
								<option value="<?= $value->id_work ?>"><?= $value->name_work ?></option>
							<?php endforeach ?>
						</select>
	                </div>
	                <div class="form-group">
						<table class="table" id="table_permisos" cellspacing="0" width="100%">
							<thead class="thead-light">
								<tr>
									<th>TABLE</th>
									<th class="text-center">LECTURA</th>
									<th class="text-center">ESCRITURA</th>
								</tr>
							</thead>
							<tbody id="tbody_permisos">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?= base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script src="<?=base_url()?>assets/js/backend/permisos/permisos.js?v=<?=$this->config->item("curr_ver");?>"></script>
