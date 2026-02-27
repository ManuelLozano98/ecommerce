    <?php
    require_once __DIR__ . '/layout/header.php';
    ?>
    <title><?php echo SITE ?> | Product</title>
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
    <style>
        .ratings {
            display: flex;
            flex-direction: column;
        }

        .stars {
            display: flex;
            color: #f39c12;
            margin-right: 8px;
        }

        .stars i {
            font-size: 16px;
        }

        .reviews-count {
            font-size: 14px;
            color: #7f8c8d;
        }

        .fas.fa-star {
            color: #f39c12;
        }

        .fas.fa-star-half-alt {
            color: #f39c12;
        }

        .far.fa-star {
            color: #ccc;
        }
    </style>
    <?php require_once __DIR__ . '/layout/endheader.php'; ?>

    <body class="hold-transition sidebar-mini">
        <div class="wrapper">

            <?php
            require_once __DIR__ . "/layout/navbar.php";
            require_once __DIR__ . "/layout/aside.php";
            ?>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Products</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="<?php echo ROOT ?>">Home</a></li>
                                    <li class="breadcrumb-item">
                                        <a href="<?php echo ROOT . '/' . strtolower($data['product']->getCategory()->getName()); ?>">
                                            <?php echo $data['product']->getCategory()->getName() ?>
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        <?php echo $data['product']->getName(); ?>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <section class="content">
                    <!-- Default box -->
                    <div class="card card-solid">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="col-12">
                                        <img src="<?php echo UPLOADS_IMAGES . "/" . $data['product']->getImage() ?>" class="" alt="<?php echo $data['product']->getName() ?>">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <h3 class="my-3"><?php echo $data['product']->getName() ?></h3>
                                    <p>Raw denim you probably haven't heard of them jean shorts Austin. Nesciunt tofu stumptown aliqua butcher retro keffiyeh dreamcatcher synth. Cosby sweater eu banh mi, qui irure terr.</p>

                                    <hr>
                                    <h4>Rating</h4>
                                    <div class="ratings" id="product-rating">
                                        <span class="stars">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= floor($data['rating']['average'])) {
                                                    echo '<i class="fas fa-star"></i>';
                                                } elseif ($i - 1 < $data['rating']['average'] && $i > floor($data['rating']['average'])) {
                                                    echo '<i class="fas fa-star-half-alt"></i>';
                                                } else {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                            }
                                            ?>
                                        </span>
                                        <span class="reviews-count"><?php echo $data['rating']['total'] ?> reviews</span>
                                    </div>
                                    <h4>Available Colors</h4>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-default text-center active">
                                            <input type="radio" name="color_option" id="color_option_a1" autocomplete="off" checked>
                                            Green
                                            <br>
                                            <i class="fas fa-circle fa-2x text-green"></i>
                                        </label>
                                        <label class="btn btn-default text-center">
                                            <input type="radio" name="color_option" id="color_option_a2" autocomplete="off">
                                            Blue
                                            <br>
                                            <i class="fas fa-circle fa-2x text-blue"></i>
                                        </label>
                                        <label class="btn btn-default text-center">
                                            <input type="radio" name="color_option" id="color_option_a3" autocomplete="off">
                                            Purple
                                            <br>
                                            <i class="fas fa-circle fa-2x text-purple"></i>
                                        </label>
                                        <label class="btn btn-default text-center">
                                            <input type="radio" name="color_option" id="color_option_a4" autocomplete="off">
                                            Red
                                            <br>
                                            <i class="fas fa-circle fa-2x text-red"></i>
                                        </label>
                                        <label class="btn btn-default text-center">
                                            <input type="radio" name="color_option" id="color_option_a5" autocomplete="off">
                                            Orange
                                            <br>
                                            <i class="fas fa-circle fa-2x text-orange"></i>
                                        </label>
                                    </div>

                                    <h4 class="mt-3">Size <small>Please select one</small></h4>
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-default text-center">
                                            <input type="radio" name="color_option" id="color_option_b1" autocomplete="off">
                                            <span class="text-xl">S</span>
                                            <br>
                                            Small
                                        </label>
                                        <label class="btn btn-default text-center">
                                            <input type="radio" name="color_option" id="color_option_b2" autocomplete="off">
                                            <span class="text-xl">M</span>
                                            <br>
                                            Medium
                                        </label>
                                        <label class="btn btn-default text-center">
                                            <input type="radio" name="color_option" id="color_option_b3" autocomplete="off">
                                            <span class="text-xl">L</span>
                                            <br>
                                            Large
                                        </label>
                                        <label class="btn btn-default text-center">
                                            <input type="radio" name="color_option" id="color_option_b4" autocomplete="off">
                                            <span class="text-xl">XL</span>
                                            <br>
                                            Xtra-Large
                                        </label>
                                    </div>

                                    <div class="bg-light py-3 px-3 mt-4 rounded">
                                        <?php if ($data['product']->getPrice()): ?>
                                            <h5 class="text-muted">
                                                <del><?php echo $data['product']->getPrice(); ?>€</del>
                                            </h5>
                                        <?php endif; ?>

                                        <h2 class="text-primary font-weight-bold">
                                            <?php echo $data['product']->getPrice(); ?>€
                                        </h2>

                                        <p class="mb-0 text-success">
                                            <?php echo $data['product']->getStock(); ?> units available
                                        </p>
                                    </div>

                                    <div class="mt-3">
                                        <h4>Quantity</h4>
                                        <input type="number"
                                            name="quantity"
                                            value="1"
                                            min="1"
                                            max="<?php echo $data['product']->getStock(); ?>"
                                            class="form-control w-25">
                                    </div>

                                    <div class="mt-4">
                                        <div class="btn btn-primary btn-lg btn-flat">
                                            <i class="fas fa-cart-plus mr-2"></i>
                                            Add to Cart
                                        </div>

                                        <div class="btn btn-warning btn-lg btn-flat">
                                            <i class="fas fa-cart mr-2"></i>
                                            Buy now
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <p><i class="fas fa-truck"></i> Shipping in 24-48 hours</p>
                                        <p><i class="fas fa-undo"></i> Free returns within 30 days</p>
                                    </div>
                                    <p><i class="fas fa-shield-alt"></i> 2 years warranty</p>

                                </div>
                            </div>
                            <div class="row mt-4">
                                <nav class="w-100">
                                    <div class="nav nav-tabs" id="product-tab" role="tablist">
                                        <a class="nav-item nav-link active" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">Description</a>
                                        <a class="nav-item nav-link" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">Comments</a>
                                    </div>
                                </nav>
                                <div class="tab-content p-3" id="nav-tabContent">
                                    <div class="tab-pane fade show active" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab"> <?php echo $data['product']->getDescription() ?> </div>
                                    <div class="tab-pane fade" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab">
                                        <?php foreach ($data['reviews'] as $review): ?>
                                            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                                                <div class="image">
                                                    <img src="<?php echo UPLOADS_IMAGES . "/" . $review['user']->getImage() ?>" class="img-circle elevation-2" alt="User Image">
                                                </div>
                                                <div class="info">
                                                    <strong><?php echo $review['user']->getUsername(); ?></strong>
                                                    <span class="stars">
                                                        <?php
                                                        for ($i = 1; $i <= 5; $i++) {
                                                            if ($i <= floor($data['rating']['average'])) {
                                                                echo '<i class="fas fa-star"></i>';
                                                            } elseif ($i - 1 < $data['rating']['average'] && $i > floor($data['rating']['average'])) {
                                                                echo '<i class="fas fa-star-half-alt"></i>';
                                                            } else {
                                                                echo '<i class="far fa-star"></i>';
                                                            }
                                                        }
                                                        ?>
                                                    </span>
                                                    <p class="text-muted">Reviewed in <?php echo $review['review']->getCreatedAt(); ?><span>
                                                            <?php if ($review['review']->getCreatedAt() !== $review['review']->getUpdatedAt()): ?>
                                                                <?php echo "Updaded in " . $review['review']->getUpdatedAt(); ?>
                                                        </span>
                                                    <?php endif ?>

                                                    </p>
                                                    <p><?php echo $review['review']->getComment(); ?></p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                </section>
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
        <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
        <!-- Select2 -->
        <script src="<?php echo ADMINLTE ?>plugins/select2/js/select2.full.min.js"></script>
        <!-- AdminLTE App -->
        <script src="<?php echo ADMINLTE ?>dist/js/adminlte.min.js"></script>
        <!-- Generic script for utilities -->
        <script type="text/javascript" src="views/js/helper/utils.js"></script>
        <!-- Page specific script -->

    </body>

    </html>