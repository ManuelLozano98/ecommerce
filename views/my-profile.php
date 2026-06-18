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
    <style>
        .fa-eye-slash,
        .fa-eye {
            cursor: pointer;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php
        require_once __DIR__ . "/layout/navbar.php";
        ?>
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?php echo ROOT ?>" class="brand-link">
                <img src="<?php echo ADMINLTE ?>dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light"><?php echo SITE ?></span>
            </a>
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">

                    <div class="image">
                        <?php if ($userData?->getImage()): ?>
                            <img src="<?php echo UPLOADS_IMAGES . "/" . $userData->getImage() ?>" class="img-circle elevation-2" alt="User Image">
                        <?php else: ?>
                            <img src="<?php echo ROOT . "/views/images/default-user.jpg" ?>" class="img-circle elevation-2" alt="No image">
                        <?php endif; ?>
                    </div>

                    <?php if ($userData): ?>
                        <div class="info">
                            <a href="<?php echo ROOT ?>/profile" class="d-block">
                                <?php echo $userData->getUsername(); ?></a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </aside>
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

            <!-- Main content -->
            <section class="content">
                <div class="row ml-3">
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
                                            src="<?php echo ROOT . "/views/images/default-user.jpg" ?>"
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
                                    <li class="nav-item"><a class="nav-link" href="#security" data-toggle="tab">Security</a></li>
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
                                            <div class="post border rounded p-3 mb-4 shadow-sm">

                                                <div class="d-flex flex-column flex-md-row gap-3">

                                                    <!-- Product image -->
                                                    <div class="flex-shrink-0 text-center">
                                                        <img
                                                            src="<?php echo UPLOADS_IMAGES . "/" . $review['image'] ?>"
                                                            alt="product image"
                                                            class="img-fluid rounded"
                                                            style="width: 180px; object-fit: cover;">
                                                    </div>

                                                    <!-- Content -->
                                                    <div class="flex-grow-1 ml-5">

                                                        <div class="d-flex justify-content-between align-items-start flex-wrap">
                                                            <div>
                                                                <h5 class="text-primary"><?php echo $review['name'] ?></h5>

                                                                <small class="text-muted">
                                                                    <span class="text-bold">Shared publicly</span> · <?php echo timeAgo($review['created_at']) ?>
                                                                </small>
                                                            </div>

                                                            <div class="mt-2 mt-md-0">
                                                                <?php renderStars($review['rating']); ?>
                                                            </div>
                                                        </div>

                                                        <div class="mt-3">
                                                            <h6 class="fw-bold mb-2">
                                                                <?php echo $review['title'] ?>
                                                            </h6>

                                                            <p class="mb-0 text-break">
                                                                <?php echo $review['comment'] ?>
                                                            </p>
                                                        </div>

                                                    </div>

                                                </div>

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
                                        <div class="card">
                                            <div class="card-body register-card-body">
                                                <form class="form-horizontal" name="form-settings" enctype="multipart/form-data">
                                                    <div class="form-group row">
                                                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="name" class="form-control" id="inputName" placeholder="Name">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputPhone" class="col-sm-2 col-form-label">Phone</label>
                                                        <div class="col-sm-10">
                                                            <input type="number" name="phone" class="form-control" id="inputPhone" placeholder="Phone">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputAddress" class="col-sm-2 col-form-label">Address</label>
                                                        <div class="col-sm-10">
                                                            <input type="text" name="address" class="form-control" id="inputAddress" placeholder="Address">
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label for="inputAvatar" class="col-sm-2 col-form-label">Avatar</label>
                                                        <div class="col-sm-10">
                                                            <div class="custom-file">
                                                                <input type="file" name="image" class="custom-file-input" id="customFile">
                                                                <label class="custom-file-label" for="customFile">Choose file</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <div id="settingsMessage"></div>
                                                            <button type="submit" class="btn btn-danger">Update Settings</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /.tab-pane -->
                                    <div class="tab-pane" id="security">
                                        <div class="card">
                                            <div class="card-body register-card-body">
                                                <form class="form-horizontal" name="form-security">

                                                    <div class="form-group row">
                                                        <label for="inputCurrentEmail" class="col-sm-2 col-form-label">Current Email</label>
                                                        <div class="col-sm-10">
                                                            <input type="email" class="form-control" id="inputCurrentEmail" disabled placeholder="Email" value="<?= $userData->getEmail() ?>">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                                        <div class="col-sm-10">
                                                            <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Email">
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="currentPassword" class="col-sm-2 col-form-label">
                                                            Current Password
                                                            <span class="text-red font-bold" title="Required field">*</span>
                                                        </label>
                                                        <div class="col-sm-10">
                                                            <div class="input-group mb-3">
                                                                <input type="password" name="password" class="form-control" id="currentPassword" placeholder="Current Password">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <span id="eyepassword" class="fas fa-eye"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="newPassword" class="col-sm-2 col-form-label">
                                                            New Password
                                                            <span class="text-red font-bold" title="Required field">*</span>
                                                        </label>
                                                        <div class="col-sm-10">
                                                            <div class="input-group mb-3">
                                                                <input type="password" name="new-password" class="form-control" id="newPassword" placeholder="New Password">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <span id="eyenewpassword" class="fas fa-eye"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group row">
                                                        <label for="confirmPassword" class="col-sm-2 col-form-label">
                                                            Confirm Password
                                                            <span class="text-red font-bold" title="Required field">*</span>
                                                        </label>
                                                        <div class="col-sm-10">
                                                            <div class="input-group mb-3">
                                                                <input type="password" name="re-password" class="form-control" id="confirmPassword" placeholder="Confirm Password">
                                                                <div class="input-group-append">
                                                                    <div class="input-group-text">
                                                                        <span id="eyerepassword" class="fas fa-eye"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <div class="offset-sm-2 col-sm-10">
                                                            <div id="securityMessage"></div>
                                                            <button type="submit" class="btn btn-danger">
                                                                Update Security
                                                            </button>
                                                        </div>
                                                    </div>


                                                </form>
                                            </div>
                                        </div>
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
    <script type="text/javascript" src="<?php echo ROOT . "/" ?>views/js/helper/utils.js"></script>
    <!-- Page specific script -->
    <script type="text/javascript" src="<?php echo ROOT . "/" ?>views/js/profile.js"></script>
</body>

</html>