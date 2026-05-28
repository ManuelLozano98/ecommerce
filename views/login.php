<?php
require_once __DIR__ . '/layout/header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo SITE ?> | Log in</title>
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/index.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>dist/css/adminlte.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
    <style>
        .fa-lock,
        .fa-unlock {
            cursor: pointer;
        }
    </style>
</head>

<body class="sidebar-collapse">
    <?php
    require_once __DIR__ . "/layout/navbar.php";
    require_once __DIR__ . '/../config/constants.php';
    $user = $_SESSION["user"] ?? NULL;
    ?>
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <!-- Brand Logo -->
        <a href="./" class="brand-link">
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
                    <div class="login-logo">
                        <a href="<?php echo ROOT ?>"><?php echo SITE ?></a>
                    </div>
                    <div class="card">
                        <div class="card-body login-card-body">
                            <p class="login-box-msg">Sign in to start your session</p>
                            <form id="loginForm" method="post">
                                <div class="input-group mb-3">
                                    <input id="username" type="text" class="form-control" placeholder="Email or username" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span class="fas fa-envelope"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="input-group mb-3">
                                    <input id="password" type="password" class="form-control" placeholder="Password" required>
                                    <div class="input-group-append">
                                        <div class="input-group-text">
                                            <span id="passwordlock" class="fas fa-lock"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-8">
                                        <div class="icheck-primary">
                                            <input type="checkbox" id="remember">
                                            <label for="remember">
                                                Remember Me
                                            </label>
                                        </div>
                                    </div>
                                    <!-- /.col -->
                                    <div class="col-4">
                                        <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                                    </div>
                                    <!-- /.col -->
                                </div>
                            </form>


                            <p class="mb-1">
                                <a href="<?php echo ROOT ?>/forgot-password">I forgot my password</a>
                            </p>
                            <p class="mb-0">
                                Dont't have an account?
                                <a href="<?php echo ROOT ?>/sign-up" class="text-center">Sign up</a>
                            </p>
                        </div>
                        <!-- /.login-card-body -->
                    </div>
                </div>
                <!-- /.login-box -->
            </div>
        </section>
    </div>
    <?php
    require_once __DIR__ . "/layout/footer.php";
    ?>

    <!-- jQuery -->
    <script src="<?php echo ADMINLTE ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo ADMINLTE ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo ADMINLTE ?>dist/js/adminlte.min.js"></script>
    <script src="<?php echo ROOT . "/" ?>views/js/helper/utils.js"></script>
    <script src="<?php echo ROOT . "/" ?>views/js/login.js"></script>
</body>

</html>