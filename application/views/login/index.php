<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Adminox - Responsive Web App Kit</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- Spinkit css -->
        <link href="<?=base_url()?>assets/css/spinkit.css" rel="stylesheet" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="<?=base_url()?>assets/images/favicon.ico">
        
        <!-- Footer cfarro -->
        <link href="<?=base_url()?>assets/css/footer.css" rel="stylesheet" type="text/css" />

        <!-- App css -->
        <link href="<?=base_url()?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/css/style.css" rel="stylesheet" type="text/css" />
        
        <link href="<?=base_url()?>assets/css/login-register/login-register.css" rel="stylesheet" type="text/css" />
        
        <!-- Toast - Sweet Alert css-->
        <link href="<?=base_url()?>assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet">
        <link href="<?=base_url()?>assets/plugins/jquery-toastr/jquery.toast.min.css" rel="stylesheet" />
        
        <script src="<?=base_url()?>assets/js/modernizr.min.js"></script>

    </head>


    <body class="pb-0">
        <div id="modal_forgotpassword" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">Recuperar Contraseña</h4>
                    </div>
                    <div class="modal-body">

                        <p class="text-center">Ingresa tu email, y en seguida te llegará un correo para poder recuperar tu contraseña. </p>
                        <form class="form-horizontal" action="#" id="form_forgot_password">

                            <div class="form-group row m-b-20">
                                <div class="col-12">
                                    <label for="email_forgot">Email address</label>
                                    <input class="form-control" type="email" id="email_forgot" required placeholder="john@deo.com">
                                </div>
                            </div>
                            <div class="form-group row text-center m-t-10">
                                <div class="col-12">
                                    <button class="btn btn-md btn-block btn-primary waves-effect waves-light" type="submit">Reset Password</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div><!-- /.modal -->
        <!-- HOME -->
        <section>
            <div class="">
                <div class="row" id="auxiliar">
                    <div id="div_login" class="col-md-5 col-sm-5 col-xs-12" style="background: white">
                        <div class="wrapper-page">
                            <div class="account-pages">
                                <div class="account-box" >
                                    <div class="account-logo-box">
                                        <h2 class="text-uppercase text-center">
                                            <a href="index.html" class="text-success">
                                                <span><img src="<?=base_url()?>assets/images/logo_dark.png" alt="" height="30"></span>
                                            </a>
                                        </h2>
                                    </div>
                                    <div class="account-content ">
                                        <form action="login/access_login" id="form_login" method="POST">

                                            <div class="form-group m-b-20 row">
                                                <div class="col-12">
                                                    <label for="email_login">Email</label>
                                                    <input class="form-control" type="email" name="email_login" id="email_login" required="" placeholder="john@deo.com" value="<?=  $this->session->flashdata('email_error')? $this->session->flashdata('email_error'): ""?>">
                                                </div>
                                            </div>

                                            <div class="form-group row m-b-20">
                                                <div class="col-12">
                                                    <a href="#" id="a_forgot_password" class="text-muted pull-right"><small>¿Has olvidado tu contraseña?</small></a>
                                                    <label for="password">Contraseña</label>
                                                    <input class="form-control" type="password" required="" name="password" placeholder="Enter your password">
                                                </div>
                                            </div>
                                            
                                            <div class="text-center">
                                                <label class="error" style="<?= $this->session->flashdata('error')? 'display:inline-block' : 'display:none'; ?>"><?=  $this->session->flashdata('error')? $this->session->flashdata('error'): ""?></label>
                                            </div>
                                            <div class="form-group row text-center m-t-10">
                                                <div class="col-12">
                                                    <button class="gradient btn btn-md btn-block btn-primary font-weight-bold" type="submit">INICIAR SESIÓN</button>
                                                </div>
                                            </div>

                                        </form>
                                        <div class="row m-t-50">
                                            <div class="col-sm-12 text-center">
                                                <p class="text-muted">¿No tienes una cuenta?<a id="redirect_login" href="page-register.html" class="text-dark m-l-5"><b>Registrarse</b></a></p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <!-- end card-box-->
                        </div>
                        <!-- end wrapper -->
                    </div>
                    <div id="back_login" class="col-md-7 col-sm-7 col-xs-12" style="background-color: #183341">

                        <div class="wrapper-page">

                            <div class="account-pages text-center">
                                <h1>SIGN IN TO GET STARTED</h2>
                                <p>Adminpro, your BD dynamic in little time.</p>
                            </div>

                        </div>
                    </div>
                    <div id="back_register" class="col-md-6 col-sm-6 col-xs-12" style="background-color: #183341; display: none" style="display: none">

                        <div class="wrapper-page">

                            <div class="account-pages text-center pl-4 pr-4">
                                <h1>WELCOME THE ADMIN OF ADMINOX, REGISTER YOUR USER.</h2>
                                <p>You have to wait to aprobate from de admin.</p>
                            </div>

                        </div>
                    </div>
                    <div id="div_register" class="col-md-6 col-sm-6 col-xs-12" style="background: white; display: none">
                        <div class="wrapper-page">
                            <div class="account-pages">
                                <div class="account-box">
                                    <div class="account-logo-box pl-0 pr-0">
                                        <h2 class="text-uppercase text-center">
                                            <a href="index.html" class="text-success">
                                                <span><img src="assets/images/logo_dark.png" alt="" height="30"></span>
                                            </a>
                                        </h2>
                                        <h5 class="text-uppercase font-bold m-b-5 m-t-50">Registro</h5>
                                        <p class="m-b-0">Consigue acceso al panel de administración</p>
                                    </div>
                                    <div class="account-content pl-0 pr-0">
                                        <form class="form-horizontal" action="#" id="form_register">

                                            <div class="form-group row m-b-20">
                                                <div class="col-12">
                                                    <label for="names_user">Nombres</label>
                                                    <input class="form-control sololetras" type="text" id="names_user" name="names_user" required="" placeholder="Michael Zenaty">
                                                </div>
                                            </div>
                                            <div class="form-group row m-b-20">
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label for="apellido_pa">Apellido paterno</label>
                                                    <input class="form-control sololetras" type="text" id="apellido_pa" name="apellido_pa" required="" placeholder="">
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label for="apellido_ma">Apellido materno</label>
                                                    <input class="form-control sololetras" type="text" id="apellido_ma" name="apellido_ma" required="" placeholder="">
                                                </div>
                                            </div>
                                            <div class="form-group row m-b-20">
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label for="tipo_doc">Tipo Documento</label>
                                                    <select id="tipo_doc" name="tipo_doc" required class="select2 form-control">
                                                        <option value="" disabled selected>Select a type</option>
                                                        <?php if (isset($tipos_documento) && $tipos_documento): ?>
                                                            <?php foreach ($tipos_documento as $documento): ?>
                                                                <option value="<?= $documento->id_type ?>" data-nro="<?= $documento->nro_car ?>" data-longitud="<?= $documento->logintud ?>"><?= $documento->name_documento ?></option>
                                                            <?php endforeach ?>
                                                        <?php endif ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12">
                                                    <label for="nro_doc">Nro Documento</label>
                                                    <input class="form-control letrasnumeros" type="text" id="nro_doc" name="nro_doc" required="" placeholder="">
                                                </div>
                                            </div>


                                            <div class="form-group row m-b-20">
                                                <div class="col-12">
                                                    <label for="email_reg">Email</label>
                                                    <input class="form-control" type="email" id="email_reg" name="email_reg" required="" placeholder="john@deo.com">
                                                </div>
                                            </div>

                                            <div class="form-group row m-b-20">
                                                <div class="col-12">
                                                    <label for="new_password">Contraseña</label>
                                                    <input class="form-control" type="password" required="" id="new_password" name="new_password" placeholder="Enter your password">
                                                </div>
                                            </div>
                                            <div class="form-group row m-b-20">
                                                <div class="col-12">
                                                    <label for="confirm_password">Vuelva a ingresar su contraseña</label>
                                                    <input class="form-control" type="password" required="" id="confirm_password" name="confirm_password" placeholder="Enter your password">
                                                </div>
                                            </div>


                                            <div class="form-group row text-center m-t-10">
                                                <div class="col-12">
                                                    <button class="btn btn-md btn-block font-weight-bold gradient_reg" type="submit" style="color: white">REGISTRARSE</button>
                                                </div>
                                            </div>

                                        </form>

                                        <div class="row m-t-50">
                                            <div class="col-12 text-center">
                                                <p class="text-muted">¿Ya tienes una cuenta?<a id="redirect_register" href="page-login.html" class="text-dark m-l-5"><b>Iniciar Sesión</b></a></p>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- end card-box-->
                            </div>


                        </div>
                        <!-- end wrapper -->
                    </div>
                </div>
            </div>
        </section>
        <div id="loading-circle-overlay" >
            <div class="loader">
                <div class="sk-cube-grid">
                    <div class="sk-cube sk-cube1"></div>
                    <div class="sk-cube sk-cube2"></div>
                    <div class="sk-cube sk-cube3"></div>
                    <div class="sk-cube sk-cube4"></div>
                    <div class="sk-cube sk-cube5"></div>
                    <div class="sk-cube sk-cube6"></div>
                    <div class="sk-cube sk-cube7"></div>
                    <div class="sk-cube sk-cube8"></div>
                    <div class="sk-cube sk-cube9"></div>
                </div>
            </div>
        </div>

        <!-- Sweet Alert -->
        <script src="<?=base_url()?>assets/plugins/sweet-alert2/sweetalert2.min.js"></script>
        <script>
            <?php if ($this->session->flashdata('forgoterror')): ?>
                swal('¡Oh, hubo un error','<?= $this->session->flashdata('forgoterror'); ?>','error');
            <?php endif ?>
          var site_url = '<?=site_url()?>';
          var base_url = '<?=base_url()?>';
        </script>
          <!-- END HOME -->
        <!-- jQuery  -->
        <script src="<?=base_url()?>assets/js/jquery.min.js"></script>
        <!-- <script src="base url assets/js/popper.min.js"></script> Popper for Bootstrap --> 
        <script src="<?=base_url()?>assets/js/bootstrap.min.js"></script>
        <!-- Toast -->
        <script src="<?=base_url()?>assets/plugins/jquery-toastr/jquery.toast.min.js" type="text/javascript"></script>
        <!-- Toast -->
        <script src="<?=base_url()?>assets/js/backend/toast_alert.js?v=<?=$this->config->item("curr_ver");?>"></script>
        <!-- Validator -->
        <script src="<?=base_url()?>js/jquery-validation-1.19.0/dist/jquery.validate.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>js/validatorinput.js"></script>
        <script src="<?=base_url()?>assets/js/backend/login/fronted_log_reg.js?v=<?=$this->config->item("curr_ver");?>"></script>
        <!-- login -->
        <script src="<?=base_url()?>assets/js/backend/login/login.js?v=<?=$this->config->item("curr_ver");?>"></script>
        <script src="<?=base_url()?>assets/js/backend/login/register.js?v=<?=$this->config->item("curr_ver");?>"></script>

    </body>
</html>
