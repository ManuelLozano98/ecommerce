    <?php
    require_once __DIR__ . '/layout/header.php';
    ?>
    <title><?php echo SITE ?></title>
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
    <!-- Ion Slider -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/ion-rangeslider/css/ion.rangeSlider.min.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/index.css">

    <?php require_once __DIR__ . '/layout/endheader.php'; ?>

    <body class="sidebar-collapse">
        <div class="wrapper">
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
            <div class="content-wrapper">
                <div class="container mt-4">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="mb-4">Shopping Cart</h4>

                                    <?php if (!empty($cart)): ?>
                                        <?php
                                        $total = 0;

                                        foreach ($cart as $item):
                                            $subTotal = $item->getProduct()->getPrice() * $item->getQuantity();
                                            $total += $subTotal;
                                        ?>
                                        <div class="product-data" data-name="<?=$item->getProduct()->getName()?>" 
                                        data-description="<?= $item->getProduct()->getDescription()?>"
                                         data-quantity="<?=$item->getQuantity()?>" 
                                         data-image="<?= $item->getProduct()->getImage()?>"
                                         data-price="<?= $item->getProduct()->getPrice() ?>"
                                         data-id="<?= $item->getProduct()->getId() ?>"
                                         data-checkout_type="cart"
                                         >
                                            <div class="row align-items-center border-bottom py-3">
                                                <div class="col-md-2 text-center">
                                                    <img src="<?= UPLOADS_IMAGES . "/". $item->getProduct()->getImage() ?>" class="img-fluid">
                                                </div>
                                                <div class="col-md-6">
                                                    <a href="<?php echo $item->getProduct()->getCategory()->getSlug() . "/" . $item->getProduct()->getSlug() ?>" class="fw-semibold text-decoration-none text-primary">
                                                        <?= htmlspecialchars($item->getProduct()->getName()) ?>
                                                    </a>
                                                    <p class="text-muted mb-1">
                                                        <?= htmlspecialchars($item->getProduct()->getDescription()) ?>
                                                    </p>
                                                    <span class="text-success small">In stock</span>
                                                    <div class="mt-2 small">
                                                        Quantity: <?= $item->getQuantity() ?>
                                                    </div>
                                                    <button class="btn btn-sm btn-outline-danger mt-2">
                                                        Delete
                                                    </button>
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <strong>
                                                        <?= number_format($item->getProduct()->getPrice(), 2) ?> €
                                                    </strong>
                                                </div>

                                            </div>
                                        </div>

                                        <?php endforeach; ?>

                                        <div class="text-end mt-3">
                                            <h5>
                                                Subtotal (<?= count($cart) ?> products):
                                                <strong><?= number_format($total, 2) ?> €</strong>
                                            </h5>
                                        </div>

                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            Your shooping cart is empty.
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <p>
                                        Subtotal:
                                        <strong>
                                            <?= isset($total) ? number_format($total, 2) : "0.00" ?> €
                                        </strong>
                                    </p>
                                    <button id="checkout" class="btn btn-warning w-100">
                                        Proceed to checkout
                                    </button>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- /.content-wrapper -->


            <?php
            require_once __DIR__ . "/layout/footer.php";
            ?>


        </div>

        <script src="<?php echo ADMINLTE ?>plugins/jquery/jquery.min.js"></script>
        <!-- Bootstrap 4 -->
        <script src="<?php echo ADMINLTE ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <!-- AdminLTE App -->
        <script src="<?php echo ADMINLTE ?>dist/js/adminlte.min.js"></script>
        <script src="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.js"></script>
        <script src="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.js"></script>
        <!-- Generic script for utilities -->
        <script type="text/javascript" src="<?php echo ROOT . "/" ?>views/js/helper/utils.js"></script>
        <!-- Ion Slider -->
        <script src="<?php echo ADMINLTE ?>plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
        <!-- Page specific script -->
        <script type="text/javascript" src="<?php echo ROOT . "/" ?>views/js/cart.js"></script>
    </body>

    </html>