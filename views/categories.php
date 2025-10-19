<?php
require_once __DIR__ . "/layout/header.php";
?>
<title><?php echo SITE ?> | Categories</title>
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
                            <h1>Categories</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="home">Home</a></li>
                                <li class="breadcrumb-item active">Categories</li>
                            </ol>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </section>

            <div class="modal fade" id="modal-default">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Category</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="form" id="form" method="POST">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="hidden" name="idcategory" id="idcategory">
                                            <input type="text" class="form-control" name="name" id="name"
                                                placeholder="Write a name for a category" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="description" class="form-label fw-semibold">Description</label>
                                        <textarea id="description" class="form-control" rows="4" maxlength="255" placeholder="Write here..."></textarea>
                                        <div class="text-end text-muted mt-1">
                                            <span id="counter">0/255</span>
                                        </div>
                                    </div>


                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button id="save" type="button" class="btn btn-primary">Save
                                        changes</button>
                                </div>

                            </form>
                        </div>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <div class="modal fade" id="modal-edit-default">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Category</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="form-edit" id="form-edit" method="POST">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="edit-name">Name</label>
                                            <input type="hidden" name="id" id="edit-idcategory">
                                            <input type="text" class="form-control" name="name" id="edit-name"
                                                placeholder="Write a name for a category" value="" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="edit-description" class="form-label fw-semibold">Description</label>
                                        <textarea name="description" id="edit-description" class="form-control" rows="4" maxlength="255" placeholder="Write here..." value=""></textarea>
                                        <div class="text-end text-muted mt-1">
                                            <span id="edit-counter">0/255</span>
                                        </div>
                                    </div>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" name="active" class="custom-control-input" id="customSwitch1">
                                        <label class="custom-control-label" for="customSwitch1">Active</label>
                                    </div>


                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button id="edit-save" type="button" class="btn btn-primary">Save changes</button>
                                </div>

                            </form>
                        </div>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <!-- Gif loader -->
            <div id="loader-container">
                <div class="loader" style="display: none;">
                </div>
            </div>


            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <div class="box-tools pull-right">
                                </div>
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="box-title">Category <button class="btn btn-success"
                                                            id="addBtn" data-toggle="modal"
                                                            data-target="#modal-default"><i
                                                                class="fa fa-plus-circle"></i>
                                                            Add</button></h2>
                                                    <h3 class="card-title">Data</h3>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <div class="card-body" id="records">
                                                        <table id="tableCategories"
                                                            class="table table-bordered table-hover">
                                                            <thead>
                                                                <th>Id</th>
                                                                <th>Name</th>
                                                                <th>Description</th>
                                                                <th>Active</th>
                                                                <th>Actions</th>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>


                                                </div><!-- /.card-body -->
                                            </div><!-- /.card -->
                                        </div><!-- /.col-12 -->
                                    </div><!-- /.row -->
                                </div><!-- /.container-fluid -->
                            </div><!-- /.box-header -->
                        </div><!-- /.box -->
                    </div><!-- /.cold-md-12 -->
                </div><!-- /.row -->
            </section><!-- /.content -->
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
    <script type="text/javascript" src="views/js/utils.js"></script>
    <!-- Page specific script -->
    <script type="text/javascript" src="views/js/category.js"></script>
</body>

</html>