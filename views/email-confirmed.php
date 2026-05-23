    <?php
    require_once __DIR__ . '/layout/header.php';
    ?>
    <title><?php echo SITE ?> | Confirm email</title>
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/index.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>dist/css/adminlte.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.css">
    <style>

    </style>
    <?php require_once __DIR__ . '/layout/endheader.php'; ?>

    <body class="sidebar-collapse">
        <?php
        require_once __DIR__ . "/layout/navbar.php";
        require_once __DIR__ . '/../config/constants.php';
        $user = $_SESSION["user"] ?? NULL;
        ?>
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="<?php echo ROOT ?>" class="brand-link">
                <img src="<?php echo ADMINLTE ?>dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light"><?php echo SITE ?></span>
            </a>
        </aside>
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper login-page">
            <section class="main-content">
                <div class="container-fluid">
                    <div class="login-box">
                        <!-- /.login-logo -->
                        <div class="card">
                            <div class="login-logo">
                                <a href="<?php echo ROOT ?>">Home</a>
                            </div>
                            <div class="card-body login-card-body">
                                <?php if (!isset($error)): ?>
                                    <div class="container text-center" id="verified">
                                        <img src="<?php echo ROOT . '/' ?>views/images/success-email.png" width="125px" height="125px" />
                                        <h2>Email Verified</h2>
                                        <p>Your email address was successfully verified.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="container text-center" id="not-verified">
                                        <img src="<?php echo ROOT . '/' ?>views/images/error-email.png" width="100px" height="100px" />
                                        <h2>Email could not be verified</h2>
                                        <p>The email request link is invalid or has expired.</p>
                                        <p>Please make sure that you have entered the information we have sent you correctly.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <!-- /.login-card-body -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </section>
        </div>
        <?php
        require_once __DIR__ . "/layout/footer.php";
        ?>

        <script src="<?php echo ADMINLTE ?>plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="<?php echo ADMINLTE ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- AdminLTE App -->
        <script src="<?php echo ADMINLTE ?>dist/js/adminlte.min.js"></script>
        <script src="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.js"></script>
        <script src="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    </body>

    </html>