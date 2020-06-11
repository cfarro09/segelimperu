<!-- Start content -->
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">Editar un item de <?= $alias_table ?></h4>

					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#"><?= $alias_table ?></a></li>
						<li class="breadcrumb-item active">Editar</li>
					</ol>

					<div class="clearfix"></div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-8 col-xs-12">
				<div class="card-box">
                	<h4 class="header-title m-t-0 m-b-20">Editar <?= $alias_table ?></h4>
					<form id="form_edit" method="POST">
						<div id="edit_form" class="form-group col-md-12 col-sm-12 col-xs-12 row">
							<input type="hidden" id="name_table" name="name_table" value="<?= $name_table ?>">
							<input type="hidden" id="alias_table" value="<?= $name_table ?>">
							<input type="hidden" id="valueid" name="valueid" value="<?= $valueid ?>">
							<input type="hidden" id="nameid" name="nameid" value="<?= $nameid ?>">
						</div>
						<div class="text-right">
							<button type="submit" class="btn btn-primary ">ACTUALIZAR</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?=base_url()?>js/validatorinput.js"></script>
<script src="<?=base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script src="<?=base_url()?>assets/js/backend/form/edit_form.js?v=<?=$this->config->item("curr_ver");?>"></script>
