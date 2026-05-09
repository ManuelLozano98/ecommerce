<?php
require_once __DIR__ . '/../config/constants.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE ?> | My Profile</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <!-- <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"> -->
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>dist/css/adminlte.min.css">
    <!-- Datatable -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.css">
    <!-- Select 2 -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <!-- Tempus Dominus -->
    <link rel="stylesheet"
        href="<?php echo ADMINLTE ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php
        require_once __DIR__ . "/layout/navbar.php";
        require_once __DIR__ . "/layout/aside.php";
        ?>

        <?php
        function timeAgo($datetime)
        {
            $timestamp = strtotime($datetime);
            $diff = time() - $timestamp;

            if ($diff < 60) return 'just now';
            if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
            if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
            if ($diff < 604800) return floor($diff / 86400) . ' days ago';
            if ($diff < 2592000) return floor($diff / 604800) . ' weeks ago';
            if ($diff < 31536000) return floor($diff / 2592000) . ' months ago';

            return floor($diff / 31536000) . ' years ago';
        }

        function renderStars($rating)
        {
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= floor($rating)) {
                    echo '<i class="fas fa-star text-warning"></i>';
                } elseif ($i - 1 < $rating) {
                    echo '<i class="fas fa-star-half-alt text-warning"></i>';
                } else {
                    echo '<i class="far fa-star text-warning"></i>';
                }
            }
        }

        ?>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>My Profile</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="<?php echo ROOT ?>">Home</a></li>
                                <li class="breadcrumb-item active">My Profile</li>
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
                                                        name="id_user" id="users">
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="datetime">Date and time:</label>
                                                <div class="input-group date" id="reservationdatetime"
                                                    data-target-input="nearest">
                                                    <input type="text" id="datetime"
                                                        class="form-control datetimepicker-input"
                                                        data-target="#reservationdatetime" name="created_at">
                                                    <div class="input-group-append" data-target="#reservationdatetime"
                                                        data-toggle="datetimepicker">
                                                        <div class="input-group-text"><i class="fa fa-calendar"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="payment_method" class="form-label">Payment method</label>
                                                <select name="payment_method" class="form-control" id="payment_method">
                                                    <option value="Credit card">Credit/Debit Card</option>
                                                    <option value="Paypal">PayPal</option>
                                                    <option value="Apple_Google_pay">Apple Pay / Google Pay</option>
                                                    <option value="Cash">Cash</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-control" data-placeholder="Select status"
                                                    name="status" id="status">
                                                    <option name="Pending">Pending</option>
                                                    <option name="Completed">Completed</option>
                                                    <option name="Processing">Processing</option>
                                                    <option name="Canceled">Canceled</option>
                                                    <option name="Refunded">Refunded</option>
                                                    <option name="Failed">Failed</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="total" class="form-label">Total</label>
                                                <div class="input-group">
                                                    <input type="text" inputmode="decimal" pattern="^\d+([.,]\d{1,2})?$"
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
                                                    name="id_article" id="articles">
                                                </select>
                                            </div>
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
                                                    name="id_user" id="edit-users">
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
                                                    <option value="Credit card">Credit/Debit Card</option>
                                                    <option value="Paypal">PayPal</option>
                                                    <option value="Apple_Google_pay">Apple Pay / Google Pay</option>
                                                    <option value="Cash">Cash</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="edit-status" class="form-label">Status</label>
                                                <select class="form-control" data-placeholder="Select status"
                                                    name="status" id="edit-status">
                                                    <option name="Pending">Pending</option>
                                                    <option name="Completed">Completed</option>
                                                    <option name="Processing">Processing</option>
                                                    <option name="Canceled">Canceled</option>
                                                    <option name="Refunded">Refunded</option>
                                                    <option name="Failed">Failed</option>
                                                </select>
                                            </div>
                                            <div class="col-sm-12 form-group">
                                                <label for="edit-total" class="form-label">Total</label>
                                                <div class="input-group">
                                                    <input type="text" inputmode="decimal" pattern="^\d+([.,]\d{1,2})?$"
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
                                            <button class="btn btn-success" id="addProductBtn"><i
                                                    class="fa fa-plus-circle"></i>
                                                Add</button>
                                        </div>

                                    </div>

                                </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button id="edit-save" type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                    </form>
                </div>

            </div>

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-md-3">
                        <!-- Profile Image -->
                        <div class="card card-primary card-outline">
                            <div class="card-body box-profile">
                                <div class="text-center">
                                    <?php if ($userData->getImage()): ?>
                                        <img class="profile-user-img img-fluid img-circle"
                                            src="<?php echo UPLOADS_IMAGES . "/" . $userData->getImage() ?>"
                                            alt="User profile picture">
                                    <?php else : ?>
                                        <img class="profile-user-img img-fluid img-circle"
                                            src="images/default-user.jpg"
                                            alt="User profile picture">
                                    <?php endif; ?>

                                </div>

                                <h3 class="profile-username text-center"><?php echo $userData->getUsername() ?></h3>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->

                        <!-- Review Box -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">About Me</h3>
                            </div>
                            <!-- /.card-header -->
                            <div class="card-body">
                                <strong><i class="fas fa-user mr-1"></i> Name</strong>

                                <p class="text-muted">
                                    <?php echo $userData->getName() ?>
                                </p>

                                <hr>

                                <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
                                <?php if ($userData->getAddress()): ?>
                                    <p class="text-muted"><?php echo $userData->getAddress() ?></p>
                                <?php else : ?>
                                    <p class="text-muted">No specified yet</p>
                                <?php endif; ?>


                                <hr>

                                <strong><i class="fas fa-phone-alt mr-1"></i> Phone</strong>
                                <?php if ($userData->getPhone()): ?>
                                    <p class="text-muted"><?php echo $userData->getPhone() ?></p>
                                <?php else : ?>
                                    <p class="text-muted">No specified yet</p>
                                <?php endif; ?>


                                <hr>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                    <div class="col-md-9">
                        <div class="card">
                            <div class="card-header p-2">
                                <ul class="nav nav-pills">
                                    <li class="nav-item"><a class="nav-link active" href="#reviews" data-toggle="tab">Reviews</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#orders" data-toggle="tab">Orders</a></li>
                                    <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                                </ul>
                            </div><!-- /.card-header -->
                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="active tab-pane" id="reviews">
                                        <?php if (empty($reviews)): ?>
                                            <div class="alert alert-info">
                                                You don't have any reviews yet
                                            </div>
                                        <?php endif; ?>
                                        <?php foreach ($reviews as $review): ?>
                                            <!-- Post -->
                                            <div class="post">
                                                <div class="review-block">
                                                    <img style="width:200px" src="<?php echo UPLOADS_IMAGES . "/" . $review['image'] ?>" alt="product image">
                                                    <span class="product-name">
                                                        <a href="<?php echo $review['slug'] ?>"><?php echo $review['name'] ?></a>
                                                    </span>
                                                    <span class="description">Shared publicly - <?php echo timeAgo($review['created_at']) ?></span>
                                                </div>
                                                <!-- /.review-block -->
                                                <?php renderStars($review['rating']); ?>
                                                <p>
                                                    <strong><?php echo $review['title'] ?></strong><br />
                                                    <?php echo $review['comment'] ?>
                                                </p>
                                            </div>
                                            <!-- /.post -->
                                        <?php endforeach; ?>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane fade" id="orders">

                                        <?php if (empty($orders)): ?>
                                            <div class="alert alert-info">
                                                You have no orders yet.
                                            </div>
                                        <?php endif; ?>

                                        <?php foreach ($orders as $order): ?>
                                            <div class="card mb-4 shadow-sm">

                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>Order #<?php echo $order->sales['id'] ?></strong>
                                                        <span class="text-muted">
                                                            <?php echo date('d/m/Y H:i', strtotime($order->sales['created_at'])) ?>
                                                        </span>
                                                    </div>
                                                    <div>
                                                        <span class="badge bg-success">Success</span>
                                                    </div>
                                                </div>

                                                <div class="card-body">

                                                    <div class="d-flex justify-content-between mb-3">

                                                        <span>
                                                            <strong>Total:</strong>
                                                            <?php echo number_format($order->sales['total_amount'], 2) ?> €
                                                        </span>
                                                    </div>

                                                    <div class="row">
                                                        <?php foreach ($order->items as $item): ?>
                                                            <div class="col-md-4 col-lg-3 mb-3">
                                                                <div class="card h-100">

                                                                    <img src="<?php echo UPLOADS_IMAGES . "/" . $item['image'] ?>"
                                                                        class="card-img-top"
                                                                        style="height:150px; object-fit:cover;"
                                                                        alt="Product">

                                                                    <div class="card-body d-flex flex-column">

                                                                        <h6 class="card-title">
                                                                            <?php echo $item['name'] ?>
                                                                        </h6>

                                                                        <p class="card-text mb-1">
                                                                            <small class="text-muted">
                                                                                Quantity: <?php echo (int)$item['quantity'] ?>
                                                                            </small>
                                                                        </p>

                                                                        <p class="card-text mb-1">
                                                                            Price: <?php echo number_format($item['price'], 2) ?> €
                                                                        </p>

                                                                        <p class="card-text mt-auto">
                                                                            <strong>Subtotal:</strong>
                                                                            <?php echo number_format($item['subtotal'], 2) ?> €
                                                                        </p>

                                                                    </div>

                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>

                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>
                                    <!-- /.tab-pane -->

                                    <div class="tab-pane" id="settings">
                                        <form class="form-horizontal">
                                            <div class="form-group row">
                                                <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                                <div class="col-sm-10">
                                                    <input type="email" class="form-control" id="inputName" placeholder="Name">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail" class="col-sm-2 col-form-label">Phone</label>
                                                <div class="col-sm-10">
                                                    <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputName2" class="col-sm-2 col-form-label">Address</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="inputName2" placeholder="Name">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputExperience" class="col-sm-2 col-form-label">Avatar</label>
                                                <div class="col-sm-10">
                                                    <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputSkills" class="col-sm-2 col-form-label">Skills</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control" id="inputSkills" placeholder="Skills">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="offset-sm-2 col-sm-10">
                                                    <div class="checkbox">
                                                        <label>
                                                            <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="offset-sm-2 col-sm-10">
                                                    <button type="submit" class="btn btn-danger">Submit</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <!-- /.tab-pane -->
                                </div>
                                <!-- /.tab-content -->
                            </div><!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                    </div>



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
    <!-- Select2 -->
    <script src="<?php echo ADMINLTE ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- Moment js -->
    <script src="<?php echo ADMINLTE ?>plugins/moment/moment.min.js"></script>
    <!-- Tempus Dominus -->
    <script src="<?php echo ADMINLTE ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo ADMINLTE ?>dist/js/adminlte.min.js"></script>
    <!-- Generic script for utilities -->
    <script type="text/javascript" src="views/js/utils.js"></script>
    <!-- Page specific script -->
    <script type="text/javascript" src="views/js/sale.js"></script>
</body>

</html>