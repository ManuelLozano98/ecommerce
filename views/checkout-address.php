    <?php
    require_once __DIR__ . '/layout/header.php';
    ?>
    <title><?php echo SITE ?> | Address</title>
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/index.css">
    <?php require_once __DIR__ . '/layout/endheader.php'; ?>

    <body>
        <div class="wrapper">
            <?php
            require_once __DIR__ . "/layout/navbar.php";
            require_once __DIR__ . '/../config/constants.php';
            ?>
            <aside class="main-sidebar sidebar-dark-primary elevation-4">
                <!-- Brand Logo -->
                <a href="<?php echo ROOT ?>" class="brand-link">
                    <img src="<?php echo ADMINLTE ?>dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
                        class="brand-image img-circle elevation-3" style="opacity: .8">
                    <span class="brand-text font-weight-light"><?php echo SITE ?></span>
                </a>
            </aside>

            <!-- Content -->
            <div class="content-wrapper">
                <div class="content-header">
                    <div class="container">
                        <h1 class="m-0">Shipping Address</h1>
                    </div>
                </div>

                <div class="content">
                    <div class="container">

                        <div class="row">
                            <div class="col-md-7">

                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Shipping Details</h3>
                                    </div>

                                    <form method="POST" id="form">
                                        <div class=" card-body">
                                            <div class="form-group">
                                                <label for="fullname">Full Name</label>
                                                <input type="text" name="full_name" id="fullname" class="form-control"
                                                    value="<?= $address?->getFullName() ?? $userData->getName() ?>" required>
                                            </div>


                                            <div class="form-group">
                                                <label for="phone">Phone</label>
                                                <input type="text" name="phone" id="phone" inputmode="numeric" pattern="[0-9]+" oninput="this.value=this.value.replace(/[^0-9]/g,'')" class="form-control"
                                                    value="<?= $address?->getPhone() ?? $userData->getPhone() ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="address">Address</label>
                                                <input type="text" name="address" id="address" class="form-control"
                                                    value="<?= $address?->getAddress() ?? $userData->getAddress() ?>" required>
                                            </div>

                                            <div class="form-group">
                                                <label for="city">City</label>
                                                <input type="text" name="city" id="city" class="form-control"
                                                    value="<?= $address?->getCity() ?? '' ?>" required>
                                            </div>

                                            <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="postal_code">Postal code</label>
                                                    <input type="text" id="postal_code" inputmode="numeric" pattern="[0-9]+" oninput="this.value=this.value.replace(/[^0-9]/g,'')" name="postal_code" class="form-control"
                                                        value="<?= $address?->getPostalCode() ?? '' ?>" required>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label for="province">State / Province</label>
                                                    <input type="text" id="province" name="province" class="form-control"
                                                        value="<?= $address?->getProvince() ?? '' ?>" required>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="card-footer text-right">
                                            <input id="checkout" type="submit" class="btn btn-primary" value="Continue to Payment" />
                                        </div>

                                    </form>
                                </div>

                            </div>

                            <!-- Order Summary -->
                            <div class="col-md-5">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">Order Summary</h3>
                                    </div>

                                    <div class="card-body">
                                        <?php $total = 0;
                                        $subTotal = 0; ?>
                                        <?php foreach ($products as $product):
                                            $subTotal = $product['price'] * ($product['quantity'] ?? 1);
                                            $total += $subTotal;
                                        ?>
                                            <div class="align-items-center border-bottom py-3">
                                                <div class="row">
                                                    <div class="col-md-6 text-center">
                                                        <img src="<?= UPLOADS_IMAGES . "/" . $product['image'] ?>" class="img-fluid">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="lead mb-1">
                                                            <?= htmlspecialchars($product['description']) ?>
                                                        </p>
                                                        <div class="mt-2">
                                                            <p>Quantity: <?= $product['quantity'] ?></p>
                                                            <p>Price: <?= $product['price'] ?>€</p>
                                                            <p>Subtotal: <strong>
                                                                    <?= number_format($subTotal, 2) ?> €
                                                                </strong></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php endforeach; ?>
                                    </div>

                                    <div class="text-end mt-3">
                                        <h5>
                                            Total
                                            <strong><?= number_format($total, 2) ?> €</strong>
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>

        </div>

        <!-- Scripts -->
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
        <script src="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.js"></script>
        <script src="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.js"></script>
        <!-- AdminLTE App -->
        <script src="<?php echo ADMINLTE ?>dist/js/adminlte.min.js"></script>
        <!-- Generic script for utilities -->
        <script type="text/javascript" src="../views/js/helper/utils.js"></script>
        <script type="text/javascript" src="../views/js/checkoutAddress.js"></script>

    </body>

    </html>