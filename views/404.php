<?php
session_start();
require_once __DIR__ . "/../config/constants.php";
require_once __DIR__ . "/layout/header.php";
?>


<title><?php echo SITE ?> | 404 Page not found</title>
<?php require_once __DIR__ . "/layout/endheader.php"; ?>

<style>
    .error-page {
        padding-top: 30px;
        margin: auto;
    }

    .error-content,
    h2.headline,
    footer {
        text-align: center;
    }

    h2.headline {
        font-size: 100px;
    }

    .content {
        height: 90vh;
    }
</style>
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Main content -->
        <section class="content">
            <h2 class="headline text-warning">Error 404</h2>
            <div class="error-content">
                <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Page not found.</h3>
                <h5>We can't find the page you're looking for.</h5>
                <p>
                    Meanwhile, you may
                    <?php if (isset($_SESSION["user"])): ?>
                        <a href="<?php echo ROOT . "/home" ?>">return to dashboard</a>
                    <?php endif; ?>
                    <a href="<?php echo ROOT . "/" ?>">return to main page</a>
                </p>
                <div class="error-image">
                    <img src="<?php echo ROOT ?>/views/images/ups-error-404-ilustracion-concepto-robot-roto_114360-5529.avif" alt="error-404" height="300px" />
                </div>
            </div>
            <!-- /.error-content -->
        </section>
        <!-- /.content -->
        <?php require_once __DIR__ . '/layout/footer.php' ?>

        <!-- ./wrapper -->
    </div>
</body>

</html>