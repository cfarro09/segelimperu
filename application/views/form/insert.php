<!-- Start content -->
<div class="content">
	<div class="container-fluid">
		<div class="row">
			<div class="col-12">
				<div class="page-title-box">
					<h4 class="page-title float-left">Register <?= $alias_table ?></h4>

					<ol class="breadcrumb float-right">
						<li class="breadcrumb-item"><a href="#">Adminox</a></li>
						<li class="breadcrumb-item"><a href="#"><?= $alias_table ?></a></li>
						<li class="breadcrumb-item active">Register</li>
					</ol>

					<div class="clearfix"></div>
				</div>
			</div>
		</div>


		<div class="row">
			<div class="col-md-8 col-xs-12">
				<div class="card-box">
                	<h4 class="header-title m-t-0 m-b-20"><?= $alias_table ?></h4>
                	<p>Register a new value</p>
                	<input type="hidden" id="name_table" value="<?= $name_table ?>">
					<form id="form_generate_register" method="POST">
						<div id="crate_form" class="form-group col-md-12 col-sm-12 col-xs-12 row">
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
<script type="text/javascript" src="<?=base_url()?>js/validatorinput.js"></script>
<script src="<?=base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script src="<?=base_url()?>assets/js/backend/form/create_form.js?v=<?=$this->config->item("curr_ver");?>"></script>
