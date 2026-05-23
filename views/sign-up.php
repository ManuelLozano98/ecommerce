    <?php
    require_once __DIR__ . '/layout/header.php';
    ?>
    <title><?php echo SITE ?> | Forgot Password</title>
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/index.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>dist/css/adminlte.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.css">
    <style>
        .fa-eye-slash,
        .fa-eye {
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
            <section class="main-content">
                <div class="container-fluid">
                    <div class="register-box">
                        <div class="register-logo">
                            <a href="<?php echo ROOT ?>">Home</a>
                        </div>

                        <div class="card">
                            <div class="card-body register-card-body">
                                <p class="login-box-msg">Register a new membership</p>

                                <form id="form" method="post">
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <input type="text" id="name" name="name" class="form-control" placeholder="Name">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user-circle"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <input type="text" id="username" name="username" class="form-control"
                                                placeholder="Username">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-user"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <input type="email" id="email" name="email" class="form-control" placeholder="Email">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span class="fas fa-envelope"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <input type="password" id="password" name="password" class="form-control"
                                                placeholder="Password">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span id="eyepassword" class="fas fa-eye"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <input type="password" id="repassword" name="repassword" class="form-control"
                                                placeholder="Retype password">
                                            <div class="input-group-append">
                                                <div class="input-group-text">
                                                    <span id="eyerepassword" class="fas fa-eye"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-8">
                                                <div class="icheck-primary">
                                                    <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                                                    <label for="agreeTerms">
                                                        I agree to the <a href="#">terms</a>
                                                    </label>
                                                </div>
                                            </div>
                                            <!-- /.col -->
                                            <div class="col-4">
                                                <button type="submit" class="btn btn-primary btn-block">Register</button>
                                            </div>
                                            <!-- /.col -->
                                        </div>
                                    </div>
                                </form>

                                <p>Already have an account? <a href="login" class="text-center">Sign in</a>
                            </div>
                            <!-- /.form-box -->
                        </div><!-- /.card -->
                    </div>
                    <!-- /.register-box -->
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
        <!-- Generic script for utilities -->
        <script src="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.js"></script>
        <script src="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.js"></script>
        <script src="<?php echo ADMINLTE ?>plugins/jquery-validation/jquery.validate.min.js"></script>
        <script src="<?php echo ADMINLTE ?>plugins/jquery-validation/additional-methods.min.js"></script>
        <script type="text/javascript" src="<?php echo ROOT . "/" ?>views/js/helper/utils.js"></script>
        <!-- Page specific script -->
        <script type="text/javascript" src="<?php echo ROOT . "/" ?>views/js/signUp.js"></script>
    </body>

    </html>