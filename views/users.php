<?php
require_once __DIR__ . '/layout/header.php';
?>
<title><?php echo SITE ?> | Users</title>
<link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
<!-- Select 2 -->
<link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<?php require_once __DIR__ . '/layout/endheader.php'; ?>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php
        require_once __DIR__ . "/layout/navbar.php";
        require_once __DIR__ . "/layout/aside.php";
        ?>

        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Users</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="home">Home</a></li>
                                <li class="breadcrumb-item active">Users</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>


            <div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel">Add User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <!-- Tabs -->
                            <ul class="nav nav-tabs" id="formTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1"
                                        role="tab">User data</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab">Personal
                                        information</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3"
                                        role="tab">Avatar</a>
                                </li>
                            </ul>

                            <!-- Form -->
                            <form id="form" method="POST" enctype="multipart/form-data">
                                <div class="tab-content mt-3">

                                    <!-- Tab 1 -->
                                    <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" name="name" id="name" />
                                        </div>
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" name="username" id="username" />
                                        </div>
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" name="email" id="email" />
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="password">Password</label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control" name="password"
                                                        id="password" />
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            <span id="eyepassword" class="fas fa-eye"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="repassword">Retype Password</label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control" name="repassword"
                                                        id="repassword" />
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">
                                                            <span id="eyerepassword" class="fas fa-eye"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 2 -->
                                    <div class="tab-pane fade" id="tab2" role="tabpanel">
                                        <div class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="text" class="form-control" name="phone" id="phone" />
                                        </div>
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <input type="text" class="form-control" name="address" id="address" />
                                        </div>
                                        <div class="form-group">
                                            <label for="document_type">Document type</label>
                                            <select name="document_type_id" class="form-control" id="document_type">
                                            </select>
                                            <label for="document">Document</label>
                                            <input type="text" name="document" class="form-control" id="document" />
                                        </div>
                                    </div>

                                    <!-- Tab 3 -->
                                    <div class="tab-pane fade" id="tab3" role="tabpanel">
                                        <div class="form-group">
                                            <label>Add an image</label>
                                            <input type="file" id="image" name="image" class="form-control-file" />
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button id="save" type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gif loader -->
            <div id="loader-container">
                <div class="loader" style="display: none;">
                </div>
            </div>

            <!-- Full description modal -->
            <div class="modal fade" id="viewModalText">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-description">Full text</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="modal-body" style="word-wrap: break-word;">
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- /.modal -->

            <div class="modal fade" id="modal-edit-default">
                <div class="modal-dialog  modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit User</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Tabs -->
                            <ul class="nav nav-tabs" id="edit-formTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="edit-tab1-tab" data-toggle="tab" href="#edit-tab1"
                                        role="tab">User data</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="edit-tab2-tab" data-toggle="tab" href="#edit-tab2"
                                        role="tab">Email</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="edit-tab3-tab" data-toggle="tab" href="#edit-tab3"
                                        role="tab">Password</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="edit-tab4-tab" data-toggle="tab" href="#edit-tab4"
                                        role="tab">Personal identification</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="edit-tab5-tab" data-toggle="tab" href="#edit-tab5"
                                        role="tab">Avatar</a>
                                </li>
                            </ul>
                            <form name="form-edit" id="form-edit" method="POST" enctype="multipart/form-data">
                                <div class="tab-content mt-3">
                                    <input type="hidden" value="" id="user_id" />
                                    <!-- Tab 1 -->
                                    <div class="tab-pane fade show active" id="edit-tab1" role="tabpanel">
                                        <div class="form-group">
                                            <label for="edit-name">Name</label>
                                            <input type="text" class="form-control" name="name" id="edit-name" />
                                        </div>
                                        <div class="form-group">
                                            <label for="edit-phone">Phone</label>
                                            <input class="form-control" type="phone" class="form-control" name="phone"
                                                id="edit-phone" />
                                        </div>
                                        <div class="form-group">
                                            <label for="edit-username">Username</label>
                                            <input class="form-control" type="text" class="form-control" name="username"
                                                id="edit-username" />
                                        </div>
                                        <div class="form-group">
                                            <label for="edit-address">Address</label>
                                            <input class="form-control" type="address" class="form-control"
                                                name="address" id="edit-address" />
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch1" name="active" />
                                            <label class="custom-control-label" for="customSwitch1">Active</label>
                                        </div>
                                    </div>

                                    <!-- Tab 2 -->
                                    <div class="tab-pane fade" id="edit-tab2" role="tabpanel">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="edit-email">Email</label>
                                                <input class="form-control" type="text" class="form-control"
                                                    name="current-email" id="edit-email" readonly />
                                                <label for="edit-newemail">New Email</label>
                                                <input class="form-control" type="text" class="form-control"
                                                    placeholder="Enter your new email if you want to change it"
                                                    name="email" id="edit-newemail" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 3 -->
                                    <div class="tab-pane fade" id="edit-tab3" role="tabpanel">
                                        <div class="form-group">
                                            <label for="edit-password">Password</label>
                                            <div class="input-group mb-3">
                                                <input placeholder="Leave it in blank if you don't want to change it"
                                                    class="form-control" type="password" class="form-control"
                                                    name="password" id="edit-password" />
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span id="edit-eyepassword" class="fas fa-eye"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <label for="edit-repassword">Retype Password</label>
                                            <div class="input-group mb-3">
                                                <input class="form-control" type="password" class="form-control"
                                                    name="repassword" id="edit-repassword" />
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        <span id="edit-eyerepassword" class="fas fa-eye"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 4 -->
                                    <div class="tab-pane fade" id="edit-tab4" role="tabpanel">
                                        <div class="form-group">
                                            <label for="edit-document_type">Select Document</label>
                                            <select class="form-control" id="edit-document_type" name="document_type_id"
                                                class="form-control">
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="edit-document">Document</label>
                                            <input class="form-control" type="document" class="form-control"
                                                name="document" id="edit-document" />
                                        </div>
                                    </div>

                                    <!-- Tab 5 -->
                                    <div class="tab-pane fade" id="edit-tab5" role="tabpanel">
                                        <div class="form-group">
                                            <label for="edit-image">Upload or change image</label>
                                            <input class="form-control" type="file" id="edit-image" name="image"
                                                class="form-control-file" />
                                            <img width="160px" height="160px" alt="edit-img" id="edit-img" />
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button id="edit-save" type="submit" class="btn btn-primary">Save changes</button>
                                </div>

                            </form>
                        </div>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->


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
                                                    <h2 class="box-title">User <button class="btn btn-success"
                                                            id="addBtn" data-toggle="modal"
                                                            data-target="#modal-default"><i
                                                                class="fa fa-plus-circle"></i>
                                                            Add</button></h2>
                                                    <h3 class="card-title">Data</h3>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <div class="card-body" id="records">
                                                        <table id="tableUsers" class="table table-bordered table-hover">
                                                            <thead>
                                                                <th>Id</th>
                                                                <th>Name</th>
                                                                <th>Email</th>
                                                                <th>Username</th>
                                                                <th>Phone</th>
                                                                <th>Avatar</th>
                                                                <th>Address</th>
                                                                <th>Document</th>
                                                                <th>Document type</th>
                                                                <th>Verification token</th>
                                                                <th>Date of expiration verification token</th>
                                                                <th>Registration date</th>
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

    <script src="<?php echo ADMINLTE ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo ADMINLTE ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables  & Plugins -->
    <script src="<?php echo ADMINLTE ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/jszip/jszip.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/pdfmake/pdfmake.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/pdfmake/vfs_fonts.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <!-- Select2 -->
    <script src="<?php echo ADMINLTE ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo ADMINLTE ?>dist/js/adminlte.min.js"></script>
    <!-- Generic script for utilities -->
    <script type="text/javascript" src="views/js/helper/utils.js"></script>
    <!-- JQuery Validator -->
    <script src="<?php echo ADMINLTE ?>plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/jquery-validation/additional-methods.min.js"></script>
    <!-- Page specific script -->
    <script type="text/javascript" src="views/js/user.js"></script>
</body>

</html>