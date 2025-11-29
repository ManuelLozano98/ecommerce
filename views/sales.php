<?php
require_once __DIR__ . '/layout/header.php';
?>
<title><?php echo SITE ?> | Sales</title>
<link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
<!-- Select 2 -->
<link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<!-- Tempus Dominus -->
<link rel="stylesheet"
    href="<?php echo ADMINLTE ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
<style>
    .sales {
        cursor: pointer;
    }

    .sales:hover {
        background-color: #dc3545 !important;
    }
</style>

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
                            <h1>Sales</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="home">Home</a></li>
                                <li class="breadcrumb-item active">Sales</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <div class="modal fade" id="modal-default">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Sale</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <ul class="nav nav-tabs" id="formTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1"
                                        role="tab">Sales data</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2"
                                        role="tab">Products</a>
                                </li>
                            </ul>
                            <form name="form" id="add-sale" method="POST">
                                <div class="tab-content mt-3">
                                    <!-- Tab 1 -->
                                    <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                        <div class="row">
                                            <div class="col-sm-12 form-group">
                                                <label for="users" class="form-label fw-semibold">Users to add to the
                                                    sale</label>
                                                <div class="select2-green">
                                                    <select class="select2" multiple="multiple"
                                                        data-placeholder="Select Users"
                                                        data-dropdown-css-class="select2-green" style="width: 100%;"
                                                        name="user_id[]" id="users">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="datetime">Date and time:</label>
                                                <div class="input-group date" id="datetime"
                                                    data-target-input="nearest">
                                                    <input type="text" id="datetime"
                                                        class="form-control datetimepicker-input"
                                                        data-target="#datetime" name="created_at">
                                                    <div class="input-group-append" data-target="#datetime"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="payment_method" class="form-label">Payment method</label>
                                                <select name="payment_method" class="form-control" id="payment_method">
                                                    <option value="credit_card">Credit Card</option>
                                                    <option value="debit_card">Debit Card</option>
                                                    <option value="paypal">PayPal</option>
                                                    <option value="apple_pay">Apple Pay</option>
                                                    <option value="google_pay">Google Pay</option>
                                                    <option value="cash">Cash</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-control" data-placeholder="Select status"
                                                    name="status" id="status">
                                                    <option value="pending">Pending</option>
                                                    <option value="completed">Completed</option>
                                                    <option value="processing">Processing</option>
                                                    <option value="canceled">Canceled</option>
                                                    <option value="refunded">Refunded</option>
                                                    <option value="failed">Failed</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="total" class="form-label">Total</label>
                                                <div class="input-group">
                                                    <input type="number" inputmode="decimal" step="0.01" pattern="^\d+([.,]\d{1,2})?$"
                                                        name="total_amount" class="form-control" id="total" />
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">€</span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="tab2" role="tabpanel">
                                        <div class="form-group">
                                            <label for="articles" class="form-label fw-semibold">Products to add to the
                                                sale</label>
                                            <div class="select2-green">
                                                <select class="select2" multiple="multiple"
                                                    data-placeholder="Select articles"
                                                    data-dropdown-css-class="select2-green" style="width: 100%;"
                                                    name="product_id[]" id="articles">
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button id="save" type="submit" class="btn btn-primary">Save
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

            <div class="modal fade" id="modal-view">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Sale Details</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="modal-body">
                        </div>
                        <div class="modal-footer">
                            <div class="pagination" id="pagination-container"></div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="modal-edit-default">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Sale</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <ul class="nav nav-tabs" id="edit-formTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="edit-tab1-tab" data-toggle="tab" href="#edit-tab1"
                                        role="tab">Sales data</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="edit-tab2-tab" data-toggle="tab" href="#edit-tab2"
                                        role="tab">Products</a>
                                </li>
                            </ul>
                            <form name="form-edit" id="form-edit" method="POST">
                                <input type="hidden" name="id" id="edit-idsale">
                                <div class="tab-content mt-3">
                                    <!-- Tab 1 -->
                                    <div class="tab-pane fade show active" id="edit-tab1" role="tabpanel">
                                        <div class="row">
                                            <div class="col-sm-12 form-group">
                                                <label for="users" class="form-label fw-semibold">Edit user</label>
                                                <select class="form-control" data-placeholder="Select User"
                                                    name="user_id" id="edit-users">
                                                </select>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="edit-datetime">Date and time:</label>
                                                <div class="input-group date" id="edit-datetime"
                                                    data-target-input="nearest">
                                                    <input type="text" id="edit-datetime"
                                                        class="form-control datetimepicker-input"
                                                        data-target="#edit-datetime" name="created_at">
                                                    <div class="input-group-append" data-target="#edit-datetime"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="edit-payment_method" class="form-label">Payment
                                                    method</label>
                                                <select name="payment_method" class="form-control"
                                                    id="edit-payment_method">
                                                    <option value="credit_card">Credit Card</option>
                                                    <option value="debit_card">Debit Card</option>
                                                    <option value="paypal">PayPal</option>
                                                    <option value="apple_pay">Apple Pay</option>
                                                    <option value="google_pay">Google Pay</option>
                                                    <option value="cash">Cash</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="edit-status" class="form-label">Status</label>
                                                <select class="form-control" data-placeholder="Select status"
                                                    name="status" id="edit-status">
                                                    <option value="pending">Pending</option>
                                                    <option value="completed">Completed</option>
                                                    <option value="processing">Processing</option>
                                                    <option value="canceled">Canceled</option>
                                                    <option value="refunded">Refunded</option>
                                                    <option value="failed">Failed</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="edit-total" class="form-label">Total</label>
                                                <div class="input-group">
                                                    <input type="number" inputmode="decimal" step="0.01" pattern="^\d+([.,]\d{1,2})?$"
                                                        name="total_amount" class="form-control" id="edit-total" />
                                                    <!-- <input type="number" min="0" step="1.99" class="form-control" id="total"
                                                name="total" placeholder="Enter total"> -->
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">€</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="edit-tab2" role="tabpanel">
                                        <div class="header">
                                            <h4 class="title">Add Product</h4>
                                            <button class="btn btn-success" id="addProductBtn" type="button"><i
                                                    class="fa fa-plus-circle"></i>
                                                Add</button>
                                        </div>

                                    </div>

                                </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button id="edit-save" type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                    </form>
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
                                                    <h2 class="box-title">Sale <button class="btn btn-success"
                                                            id="addBtn" data-toggle="modal"
                                                            data-target="#modal-default"><i
                                                                class="fa fa-plus-circle"></i>
                                                            Add</button></h2>
                                                    <h3 class="card-title">Data</h3>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <div class="card-body" id="records">
                                                        <table id="tableSales" class="table table-bordered table-hover">
                                                            <thead>
                                                                <th>Id</th>
                                                                <th>User</th>
                                                                <th>Date</th>
                                                                <th>Payment method</th>
                                                                <th>Status</th>
                                                                <th>Total</th>
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



            <div class="modal fade" id="modal-add-user-sales">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Sales to User</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="form" id="form" method="POST">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="users-sales">Users</label>
                                        <div class="select2-green">
                                            <select class="select2" multiple="multiple" data-placeholder="Select Users"
                                                data-dropdown-css-class="select2-green" style="width: 100%;"
                                                name="users[]" id="users-sales">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="sales-users">Sales</label>
                                            <input type="hidden" name="sale_id" id="idsale">
                                            <div class="select2-green">
                                                <select class="select2" multiple="multiple"
                                                    data-placeholder="Select Sales"
                                                    data-dropdown-css-class="select2-green" style="width: 100%;"
                                                    name="sales[]" id="sales-users">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button id="saveUser" type="submit" class="btn btn-primary">Save
                                        changes</button>
                                </div>

                            </form>
                        </div>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>


            <div class="modal fade" id="modal-add-default">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Sales to this User</h4>
                            <input type="hidden" value="" id="thisUser" />
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="form" id="formSalesUser" method="POST">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="sales-users2">Sale</label>
                                            <input type="hidden" name="sale_id" id="idsale">
                                            <div class="select2-green">
                                                <select class="select2" multiple="multiple"
                                                    data-placeholder="Select Sales"
                                                    data-dropdown-css-class="select2-green" style="width: 100%;"
                                                    name="sales[]" id="sales-users2">
                                                </select>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button id="saveSale" type="submit" class="btn btn-primary">Save
                                        changes</button>
                                </div>

                            </form>
                        </div>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
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
    <!-- Select2 -->
    <script src="<?php echo ADMINLTE ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo ADMINLTE ?>dist/js/adminlte.min.js"></script>
    <!-- Generic script for utilities -->
    <script type="text/javascript" src="views/js/helper/utils.js"></script>
    <!-- JQuery Validator -->
    <script src="<?php echo ADMINLTE ?>plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/jquery-validation/additional-methods.min.js"></script>
    <!-- Moment js -->
    <script src="<?php echo ADMINLTE ?>plugins/moment/moment.min.js"></script>
    <!-- Tempus Dominus -->
    <script src="<?php echo ADMINLTE ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Page specific script -->
    <script type="text/javascript" src="views/js/sale.js"></script>
</body>

</html>