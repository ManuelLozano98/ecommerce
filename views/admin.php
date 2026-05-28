<?php
require_once __DIR__ . "/layout/header.php";
?>
<title><?php echo SITE ?> | Admin</title>
<link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
<?php require_once __DIR__ . "/layout/endheader.php"; ?>


<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php
        require_once __DIR__ . "/layout/navbar.php";
        require_once __DIR__ . "/layout/aside.php";
        ?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Admin</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo ROOT?>">Home</a></li>
                                <li class="breadcrumb-item active">Admin</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </section>





        </div><!-- /.content-wrapper -->


        <?php
        require_once __DIR__ . "/layout/footer.php";
        ?>


    </div>

    <script src="<?php echo ADMINLTE ?>/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo ADMINLTE ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="<?php echo ADMINLTE ?>/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/jszip/jszip.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/pdfmake/pdfmake.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/pdfmake/vfs_fonts.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo ADMINLTE ?>/dist/js/adminlte.min.js"></script>
    <!-- Generic script for utilities -->
    <script type="text/javascript" src="views/js/helper/utils.js"></script>
    <!-- Page specific script -->

</body>

</html>