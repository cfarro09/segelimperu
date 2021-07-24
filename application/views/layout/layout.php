<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Adminox - Responsive Web App Kit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?=base_url()?>assets/images/favicon.ico">

    <!-- Spinkit css -->
    <link href="<?=base_url()?>assets/css/spinkit.css" rel="stylesheet" />

    <!-- C3 charts css -->
    <link href="<?=base_url()?>assets/plugins/c3/c3.min.css" rel="stylesheet" type="text/css"  />
    <link href="<?=base_url()?>assets/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css"  />

    <!-- Toast css-->
    <link href="<?=base_url()?>assets/plugins/jquery-toastr/jquery.toast.min.css" rel="stylesheet" />

    <!-- DatePicker -->
    <link href="<?=base_url()?>assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="<?=base_url()?>assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
    
    <!-- Sweet Alert -->
    <link href="<?=base_url()?>assets/plugins/sweet-alert2/sweetalert2.min.css" rel="stylesheet">
    
    <!-- App css -->
    <link href="<?=base_url()?>assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url()?>assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url()?>assets/css/metismenu.min.css" rel="stylesheet" type="text/css" />
    <link href="<?=base_url()?>assets/css/style.css" rel="stylesheet" type="text/css" />
</head>

<script src="<?=base_url()?>assets/js/modernizr.min.js"></script>
<!-- jQuery  -->
<script src="<?=base_url()?>assets/js/jquery.min.js"></script>
<script src="<?=base_url()?>assets/js/popper.min.js"></script><!-- Popper for Bootstrap -->
<script src="<?=base_url()?>assets/js/bootstrap.min.js"></script>
<script src="<?=base_url()?>assets/js/metisMenu.min.js"></script>
<script src="<?=base_url()?>assets/js/waves.js"></script>
<script src="<?=base_url()?>assets/js/jquery.slimscroll.js"></script>
<!-- DatePicker -->
<script src="<?=base_url()?>assets/plugins/timepicker/bootstrap-timepicker.js"></script>
<script src="<?=base_url()?>assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<!-- Select2 -->
<script src="<?=base_url()?>assets/plugins/select2/js/select2.min.js"></script>
<!-- Toast -->
<script src="<?=base_url()?>assets/plugins/jquery-toastr/jquery.toast.min.js" type="text/javascript"></script>
<body>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Top Bar Start -->
        <div class="topbar">

            <!--content header -->
            <?php require_once('header.php'); ?>
            <!--content header -->
        </div>

        <div class="left side-menu">
            <!-- content menu -->
            <?php require_once('menu.php'); ?>
            <!-- content menu -->
        </div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="content-page">
            <!-- Start content -->
            <?=$content_for_layout?>
            <!-- End content -->


            <!-- footer content -->
            <?php require_once('footer.php'); ?>
            <!-- /footer content -->

        </div>
        <!-- ============================================================== -->
        <!-- End Right content here -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- Counter js  -->
    <script src="<?=base_url()?>assets/plugins/waypoints/jquery.waypoints.min.js"></script>
    <script src="<?=base_url()?>assets/plugins/counterup/jquery.counterup.min.js"></script>

    <!--C3 Chart-->
    <script type="text/javascript" src="<?=base_url()?>assets/plugins/d3/d3.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>assets/plugins/c3/c3.min.js"></script>

    <!--Echart Chart-->
    <script src="<?=base_url()?>assets/plugins/echart/echarts-all.js"></script>
    
    <!-- Sweet Alert -->
    <script src="<?=base_url()?>assets/plugins/sweet-alert2/sweetalert2.min.js"></script>

    <!-- Dashboard init -->
    <script src="<?=base_url()?>assets/pages/jquery.dashboard.js"></script>

    <!-- App js -->
    <script src="<?=base_url()?>assets/js/jquery.core.js"></script>
    <script src="<?=base_url()?>assets/js/jquery.app.js"></script>
    <script src="<?=base_url()?>assets/js/init.js"></script>
    <script>
        <?php if ($this->session->flashdata('error')): ?>
            swal('¡Oh, hubo un error','<?= $this->session->flashdata('error'); ?>','error');
        <?php endif ?>
        const site_url = '<?=site_url()?>';
        const base_url = '<?=base_url()?>';
    </script>

</body>
</html>
