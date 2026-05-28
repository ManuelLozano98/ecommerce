    <?php
    require_once __DIR__ . '/layout/header.php';
    ?>
    <title><?php echo SITE ?> | Recover Password</title>
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/index.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>dist/css/adminlte.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.css">
    <style>
        .fa-lock,
        .fa-unlock {
            cursor: pointer;
        }
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
            <div class="login-box">
                <section class="main-content">
                    <div class="container-fluid">
                        <div class="login-logo">
                            <a href="<?php echo ROOT ?>">Home</a>
                        </div>
                        <!-- /.login-logo -->
                        <div class="card">
                            <div class="card-body login-card-body">
                                <p class="login-box-msg">You are only one step a way from your new password, recover your password now.
                                </p>

                                <form name="form" method="post">
                                    <div class="input-group mb-3">
                                        <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span id="passwordlock" class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3">
                                        <input type="password" id="confirmpassword" name="confirmPassword" class="form-control" placeholder="Confirm Password">
                                        <div class="input-group-append">
                                            <div class="input-group-text">
                                                <span id="confirmpasswordlock" class="fas fa-lock"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div id="message"></div>
                                            <button type="submit" class="btn btn-primary btn-block">Change password</button>
                                        </div>
                                        <!-- /.col -->
                                    </div>
                                </form>

                                <p class="mt-3 mb-1">
                                    <a href="<?php echo ROOT ?>/login">Login</a>
                                </p>
                            </div>
                            <!-- /.login-card-body -->
                        </div>
                    </div>
                    <!-- /.login-box -->
                </section>
            </div>
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
        <!-- Generic script for utilities -->
        <script type="text/javascript" src="<?php echo ROOT . "/" ?>views/js/helper/utils.js"></script>
        <!-- Page specific script -->
        <script type="text/javascript" src="<?php echo ROOT . "/" ?>views/js/recoverPassword.js"></script>
    </body>

    </html>