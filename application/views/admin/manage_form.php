<link href="<?=base_url()?>assets/css/form/css.css" rel="stylesheet">
<!-- Start content -->
<div class="content">
    <div class="container-fluid">
		<div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title float-left">Configurador Form</h4>

                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="#">Adminox</a></li>
                        <li class="breadcrumb-item"><a href="#">Forms</a></li>
                        <li class="breadcrumb-item active">Configurador</li>
                    </ol>

                    <div class="clearfix"></div>
                </div>
            </div>
        </div>

		<div class="row">
			<div class="col-md-9 col-xs-12">
				<div class="card-box">
                	<h4 class="header-title m-t-0 m-b-20">Configure your BD</h4>
                	<p>Select a table and give attributes to generate the form</p>
	                <form class="" id="form_columns">
	                	<div class="form-group">
							<select class="form-control select2" id="select_tables">
								<option value="" disabled selected>Choose Table</option>
								<?php foreach ($result as $value): ?>
									<option value="<?= $value->nameTable ?>"><?= $value->aliasTable ?></option>
								<?php endforeach ?>
							</select>
							<span class="help-block"><small>Select a table to configure how to show in a CRUD</small></span>
	                	</div>
	                	<div class="form-group">
							<table class="table " width="100%">
								<thead class="thead-light">
									<tr>
										<th class="text-center">COLUMN</th>
										<th class="text-center">TYPE</th>
										<th class="text-center">NULL</th>
										<th class="text-center">FK</th>
										<th class="text-center">LIST</th>
										<th class="text-center">#</th>
									</tr>
								</thead>
								<tbody id="columns_table">
								</tbody>
							</table>
	                	</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?=base_url()?>js/validatorinput.js"></script>
<script src="<?=base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script src="<?=base_url()?>assets/js/backend/admin/form.js?v=<?=$this->config->item("curr_ver");?>"></script>
