<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Adminpro</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />

        <!-- App favicon -->
        <link rel="shortcut icon" href="<?=base_url()?>assets/images/favicon.ico">

       <!-- App css -->
        <link href="<?=base_url()?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/css/icons.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/css/style.css" rel="stylesheet" type="text/css" />
        <link href="<?=base_url()?>assets/css/spinkit.css" rel="stylesheet" />

        <link href="<?=base_url()?>assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet">
        <script src="<?=base_url()?>assets/js/modernizr.min.js"></script>

    </head>

    <body class="bg-accpunt-pages">

        <!-- HOME -->
        <section>
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">

                        <div class="wrapper-page">

                            <div class="account-pages">
                                <div class="account-box">
                                    <div class="text-center account-logo-box">
                                        <h2 class="text-uppercase">
                                            <a href="index.html" class="text-success">
                                                <span><img src="<?=base_url()?>assets/images/logo_dark.png" alt="" height="30"></span>
                                            </a>
                                        </h2>
                                    </div>
                                    <div class="account-content">
                                        <div class="text-center m-b-20">
                                            <p class="text-muted m-b-0">Reestablecer Contraseña.</p>
                                        </div>
                                        <form id="form_reset_password" class="form-horizontal" method="POST" >

                                            <div class="form-group row m-b-20">
                                                <div class="col-12">
                                                    <label for="password">Contraseña</label>
                                                    <input class="form-control" type="password" name="password" id="password" required>
                                                </div>
                                            </div>
                                            <div class="form-group row m-b-20">
                                                <div class="col-12">
                                                    <label for="">Confirmar Contraseña</label>
                                                    <input class="form-control" type="password" name="confirm_password" required>
                                                </div>
                                            </div>
                                            <div class="form-group row text-center m-t-10">
                                                <div class="col-12">
                                                    <button class="btn btn-md btn-block btn-primary waves-effect waves-light" type="submit">Reset Password</button>
                                                </div>
                                            </div>

                                        </form>

                                        <div class="clearfix"></div>

                                        <div class="row m-t-40">
                                            <div class="col-sm-12 text-center">
                                                <p class="text-muted">Back to <a href="<?=site_url()."login"?>" class="text-dark m-l-5"><b>Sign In</b></a></p>
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
          <!-- END HOME -->

        <!-- Librerias  -->
        <script src="<?=base_url()?>assets/js/jquery.min.js"></script>
        <script src="<?=base_url()?>assets/js/bootstrap.min.js"></script>
        <script src="<?=base_url()?>js/jquery-validation-1.19.0/dist/jquery.validate.min.js"></script>
        <script src="<?=base_url()?>assets/plugins/sweet-alert2/sweetalert2.min.js"></script>
        <script>
            var site_url = '<?=site_url()?>';
            var base_url = '<?=base_url()?>';
        </script>
        <!-- Backend -->
        <script src="<?=base_url()?>assets/js/backend/security/forgot_password.js?v=<?=$this->config->item("curr_ver");?>"></script>

    </body>
</html>