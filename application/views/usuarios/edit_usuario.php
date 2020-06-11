<link href="<?=base_url()?>assets/css/form/css.css" rel="stylesheet">
<link href="<?= base_url(); ?>assets/plugins/datatables/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<link href="<?= base_url(); ?>assets/plugins/datatables/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
<!-- Start content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title float-left">Editar un usuario</h4>

                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="#">Adminox</a></li>
                        <li class="breadcrumb-item"><a href="#">Usuarios</a></li>
                        <li class="breadcrumb-item active">Editar usuarios</li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <?php if ($user): ?>
            <div class="row">
                <div class="col-md-8 col-xs-12">
                    <div class="mb-3">
                        <form action="" id="form_change_estado">
                                <input type="hidden" name="id_usu" value="<?= $user->id_usu ?>">
                                <?php if ($user->estado_usu == "IN" || $user->estado_usu == "RJ" || $user->estado_usu == "PG"): ?>
                                    <input type="hidden" name="estado_usu" id="estado_usu" value="AC">
                                    <?php $text = "HABILITAR USUARIO"; ?>
                                    <?php $class = "btn-success"; ?>
                                <?php else: ?>
                                    <input type="hidden" name="estado_usu" value="IN">
                                    <?php $text = "INHABILITAR USUARIO"; ?>
                                    <?php $class = "btn-danger"; ?>
                                <?php endif ?>
                                    <button type="submit" class="btn <?= $class; ?>"><?= $text ?></button>
                        </form>
                    </div>
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-20">MODIFICAR CATEGORIA DE TRABAJADOR</h4>
                        <form id="form_change_category">
                            <div class="col-sm-12 row">
                                <div class="col-sm-6">
                                    <input type="hidden" name="id_usu" value="<?= $user->id_usu ?>">
                                    <select name="tipo_usu" class="form-control">
                                        <?php if ($categories): ?>
                                            <option value="">Select category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category->work_char ?>" <?= $category->work_char == $user->tipo_usu ? "selected" : "" ?> ><?= $category->name_work ?></option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <button type="submit" class="btn btn-primary">MODIFICAR</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-20">Información personal del usuario</h4>
                        <form action="" id="form_usu_info" method="POST">
                            <input type="hidden" name="id_usu" value="<?= $user->id_usu ?>">
                            <div class="form-group row m-b-20">
                                <div class="col-12">
                                    <label for="nombre_usu">Nombres</label>
                                    <input class="form-control sololetras" type="text" id="nombre_usu" value="<?= $user->nombre_usu ?>" name="nombre_usu" required="" placeholder="Michael Zenaty">
                                </div>
                            </div>
                            <div class="form-group row m-b-20">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label for="apellido_pat">Apellido paterno</label>
                                    <input class="form-control sololetras" value="<?= $user->apellido_pat ?>" type="text" id="apellido_pat" name="apellido_pat" required="" placeholder="">
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label for="apellido_mat">Apellido materno</label>
                                    <input class="form-control sololetras" value="<?= $user->apellido_mat ?>" type="text" id="apellido_mat" name="apellido_mat" required="" placeholder="">
                                </div>
                            </div>
                            <div class="form-group row m-b-20">
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label for="id_tipo_doc">Tipo Documento</label>
                                    <select id="id_tipo_doc" name="id_tipo_doc" required class="select2 form-control">
                                        <option value="" disabled selected>Select a type</option>
                                        <?php if (isset($tipos_documento) && $tipos_documento): ?>
                                            <?php foreach ($tipos_documento as $documento): ?>
                                                <option value="<?= $documento->id_type ?>" <?= $documento->id_type == $user->id_tipo_doc ? "selected" : ""  ?> data-nro="<?= $documento->nro_car ?>" data-longitud="<?= $documento->logintud ?>"><?= $documento->name_documento ?></option>
                                            <?php endforeach ?>
                                        <?php endif ?>
                                    </select>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-12">
                                    <label for="nro_documento">Nro Documento</label>
                                    <input class="form-control letrasnumeros" value="<?= $user->nro_documento ?>" type="text" id="nro_documento" name="nro_documento" required="" placeholder="">
                                </div>
                            </div>
                            <div class="form-group row m-b-20">
                                <div class="col-12">
                                    <label for="email_usu">Email</label>
                                    <input class="form-control" type="email" value="<?= $user->email_usu ?>" id="email_usu" name="email_usu" required="" placeholder="john@deo.com">
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary ">ACTUALIZAR</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-box">
                        <h4 class="header-title m-t-0 m-b-20">Modificar Contraseña</h4>
                        <form action="" id="form_change_password" method="POST">
                            <input type="hidden" name="id_usu" value="<?= $user->id_usu ?>">
                            <div class="form-group row m-b-20">
                                <div class="col-6">
                                    <label for="password">Contraseña</label>
                                    <input class="form-control" type="password" required="" id="password" name="password" placeholder="Enter your password">
                                </div>
                                <div class="col-6">
                                    <label for="confirm_password">Vuelva a ingresar contraseña</label>
                                    <input class="form-control" type="password" required="" id="confirm_password" placeholder="Enter your password">
                                </div>
                            </div>
                            <div class="text-right">
                                <button type="submit" class="btn btn-primary ">MODIFICAR</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif ?>
    </div>
</div>

<script src="<?=base_url()?>js/jquery-validation-1.19.0/dist/jquery.validate.min.js"></script>
<script src="<?= base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
<script src="<?=base_url()?>assets/js/backend/usuarios/edituser.js?v=<?=$this->config->item("curr_ver");?>"></script>
