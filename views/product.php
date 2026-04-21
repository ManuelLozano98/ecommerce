    <?php
    require_once __DIR__ . '/layout/header.php';
    ?>
    <title><?php echo SITE ?> | Product</title>
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/index.css">
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

        .buy-box {
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
        }

        .btn-cart {
            background: #FFD814;
            border-color: #FCD200;
        }

        .btn-buy {
            background: #FFA41C;
            border-color: #FF8F00;
        }
    </style>
    <?php require_once __DIR__ . '/layout/endheader.php'; ?>

    <body>

        <?php
        $product = $data['product']->product;
        $details = $data['product']?->details;
        $images = $data['product']?->images;
        $rating = $data['rating'];
        $warranty = $details?->getWarranty() ?? '';

        $price = $product->getPrice();
        $discount = (float)($details?->getDiscount() ?? 0);
        $total = round($price * (1 - $discount / 100), 2);
        $save = round($price - $total, 2);
        $stock = $product->getStock();
        $colors = $details?->getColorOptions();

        if ($colors !== null && !empty($colors)) {
            $colors = $colors['available_colors'];
        }
        $sizes = $details?->getSizeOptions();
        if ($sizes !== null && !empty($sizes)) {
            $sizes = $sizes['available_sizes'];
        }

        $basic_info = [];
        if ($brand = $details?->getBrand()) {
            $basic_info['Brand'] = $brand;
        }

        if ($manufacturer = $details?->getManufacturer()) {
            $basic_info['Manufacturer'] = $manufacturer;
        }

        if ($model = $details?->getModel()) {
            $basic_info['Model'] = $model;
        }
        $product_details = [];

        if ($dimensions = $details?->getDimensions()) {
            $product_details['Dimensions'] = $dimensions;
        }

        if ($weight = $details?->getWeight()) {
            $product_details['Weight'] = $weight;
        }

        if ($material = $details?->getMaterial()) {
            $product_details['Material'] = $material;
        }

        if ($color = $details?->getColor()) {
            $product_details['Color'] = $color;
        }

        if ($packageContents = $details?->getPackageContents()) {
            $packageContents = $packageContents['box_contents'];
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
        function renderStock($stock)
        {
            if ($stock > 10) {
                echo '<p class="text-success">In Stock</p>';
            } elseif ($stock > 0)
                echo "<p class='text-warning'>Only $stock left!</p>";
            else {
                echo '<p class="text-danger">Out of stock</p>';
            }
        }
        ?>


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
                <!-- Sidebar -->
                <div class="sidebar vh-100 overflow-auto position-sticky top-0 p-3">

                    <!-- Sidebar Menu -->
                    <nav class="mt-2" id="menu">
                        <h4>Categories</h4>
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <?php
                            foreach ($categories as $category): ?>
                                <li class="nav-item">
                                    <a href="<?php echo ROOT . "/" . strtolower($category->getName()) ?>" class="nav-link">
                                        <i class="nav-icon fa-solid fa-<?php echo lcfirst($category->getName()[0]); ?>"></i>
                                        <p>
                                            <?php echo $category->getName() ?>
                                        </p>
                                    </a>
                                </li>
                            <?php endforeach; ?>

                        </ul>
                    </nav>
                    <!-- /.sidebar-menu -->
                </div>
                <!-- /.sidebar -->
            </aside>
            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1>Product</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="<?php echo ROOT ?>">Home</a></li>
                                    <li class="breadcrumb-item">
                                        <a href="<?php echo ROOT . '/' . strtolower($product->getCategory()->getName()); ?>">
                                            <?php echo $product->getCategory()->getName() ?>
                                        </a>
                                    </li>
                                    <li class="breadcrumb-item active">
                                        <?php echo $product->getName(); ?>
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

                                <!-- GALLERY  -->
                                <div class="col-12 col-sm-6">
                                    <div class="col-8" style="max-width: 300px;">
                                        <img src="<?php echo UPLOADS_IMAGES . "/" . $product->getImage() ?>" class="product-image" alt="<?php echo $product->getName() ?>">
                                    </div>
                                    <?php if ($images && !empty($images)): ?>
                                        <div class="col-12 product-image-thumbs">
                                            <?php foreach ($images as $image): ?>
                                                <?php if ($image?->getType() === "main"): ?>
                                                    <div class="product-image-thumb active"><img src="<?php echo UPLOADS_IMAGES . "/" . $image?->getImage() ?>" alt="Active product image"></div>
                                                <?php elseif ($image?->getType() === "gallery"): ?>
                                                    <div class="product-image-thumb"><img src="<?php echo UPLOADS_IMAGES . "/" . $image?->getImage() ?>" alt="Product Image"></div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <!-- MAIN SECTION  -->
                                <div class="col-12 col-sm-6">

                                    <!-- PRODUCT NAME -> REVIEWS -> DESCRIPTION  -->
                                    <div id="box-product-id">
                                        <h3 class="my-3"><?php echo $product->getName() ?></h3>
                                        <div class="mb-2" id="product-rating">
                                            <?php
                                            renderStars($rating['average']);
                                            ?>
                                            <span class="text-primary"><?php echo $rating['total'] ?> reviews</span>
                                        </div>
                                        <p><?php echo $product->getDescription() ?></p>
                                        <hr>
                                    </div>
                                    <!-- PAY BOX -->
                                    <div class="row">
                                        <!-- OLD PRICE -> PRICE -> DISCOUNT-->
                                        <?php if ($discount > 0): ?>
                                            <div id="box-product-price" class="py-3 col-6">
                                                <h5 class="text-danger">
                                                    <del><?php echo $product->getPrice() ?>€</del>
                                                </h5>
                                                <h2 class="text-secondary font-weight-bold">
                                                    <span class="badge badge-danger"><?php echo $discount . "%" ?></span>
                                                    <?php echo $total; ?>€
                                                </h2>
                                                <div class="text-success">
                                                    You save <?php echo $save ?>€
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- BUY BOX -->
                                        <div class="col-6">
                                            <div class="buy-box p-3">
                                                <h4 class="price-red font-weight-bold">
                                                    <?php echo $total ?>€
                                                </h4>

                                                <?php
                                                renderStock($stock);
                                                ?>

                                                <div class="form-group">
                                                    <label>Quantity:</label>
                                                    <input type="number"
                                                        value="1"
                                                        min="1"
                                                        max="<?php echo $stock ?>"
                                                        class="form-control">
                                                </div>

                                                <button class="btn btn-cart fas fa-cart-plus btn-block mb-2 font-weight-bold" data-id="<?php echo $product->getId() ?>" data-product='<?php echo htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') ?>'>
                                                    Add to Cart
                                                </button>

                                                <button class="btn btn-buy btn-block">
                                                    Buy Now
                                                </button>

                                                <hr>

                                                <p><i class="fas fa-truck"></i> Free Delivery</p>
                                                <p><i class="fas fa-undo"></i> Free Returns</p>
                                                <?php if ($warranty) {
                                                    echo "<p><i class='fas fa-shield-alt'></i>$warranty</p>";
                                                } ?>


                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <!-- ABOUT THIS PRODUCT-->
                            <section class="content mt-4">
                                <div class="row">
                                    <?php if ($colors): ?>
                                        <!-- GALLERY VARIANT-->
                                        <div class="col-lg-4">
                                            <?php $first = true; ?>
                                            <h5>Available Colors</h5>
                                            <?php foreach ($colors as $color): ?>
                                                <?php if ($first): ?>
                                                    <div>
                                                        <p><?php echo $color ?>
                                                    </div>
                                                    <!--<div class="product-image-thumb active"><img src="<?php //echo UPLOADS_IMAGES . "/" . $color 
                                                                                                            ?>" alt="<?php //echo $color 
                                                                                                                        ?>"></div>-->
                                                <?php
                                                    $first = false;
                                                else: ?>
                                                    <div>
                                                        <p><?php echo $color ?>
                                                    </div>
                                                    <!-- <div class="product-image-thumb"><img src="<?php //echo UPLOADS_IMAGES . "/" . $color 
                                                                                                    ?>" alt="<?php //echo $color 
                                                                                                                ?>"></div>-->
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($sizes): ?>
                                        <!-- PRODUCT SIZES-->
                                        <div class="col-lg-4">
                                            <h5>Size</h5>
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                <?php foreach ($sizes as $size): ?>
                                                    <label class="btn btn-default text-center">
                                                        <input type="radio" name="color_option" id="color_option_<?php echo $size ?>" autocomplete="off">
                                                        <?php echo $size; ?>
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($basic_info)): ?>
                                        <!-- BASIC PRODUCT INFORMATION -->
                                        <div class="col-lg-4">
                                            <div class="row mt-4">
                                                <div class="card-body p-0">
                                                    <table class="table">
                                                        <tbody>
                                                            <?php foreach ($basic_info as $key => $value): ?>
                                                                <tr>
                                                                    <th><?php echo $key; ?></th>
                                                                    <td><?php echo $value; ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </section>

                            <section class="content">
                                <?php if ($details?->getFeatures() !== null): ?>
                                    <!-- PRODUCT FEATURES-->
                                    <div class="row mt-4">
                                        <h3>About this product</h3>
                                        <table class="table">
                                            <tbody>
                                                <?php foreach ($details->getFeatures() as $key => $value): ?>
                                                    <tr>
                                                        <th><?php echo ucfirst(str_replace('_', ' ', $key)); ?></th>
                                                        <td><?php echo $value; ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($product_details)): ?>
                                    <!-- PRODUCT DETAILS-->
                                    <div class="row mt-4">
                                        <div class="p-0">
                                            <ul class="list-unstyled">
                                                <?php foreach ($product_details as $key => $value): ?>
                                                    <li><?php echo $key;
                                                        echo ": " . $value; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                    <hr>
                                <?php endif; ?>
                                <?php if (!empty($packageContents)): ?>
                                    <!-- BOX CONTENT-->
                                    <h5>Box Contents</h5>
                                    <hr>
                                    <div class="row mt-4">
                                        <div class="p-0">
                                            <ul class="list-unstyled">
                                                <?php foreach ($packageContents as $key => $value): ?>
                                                    <li><?php echo ucfirst(str_replace('_', ' ', $key)) . ": " . $value; ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </section>
                            <?php if ($details?->getTechnicalDetails() !== null): ?>
                                <!-- TECHNICAL DETAILS-->
                                <div class="row mt-4">
                                    <div class="card-body p-0">
                                        <h3>Technical details</h3>
                                        <table class="table table-striped">
                                            <tbody>
                                                <?php foreach ($details->getTechnicalDetails() as $key => $value): ?>
                                                    <tr>
                                                        <th><?php echo ucfirst(str_replace('_', ' ', $key)); ?></th>
                                                        <td><?php echo $value; ?></td>
                                                    </tr>

                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <!-- DESCRIPTION AND REVIEWS-->
                            <div class="row mt-4">
                                <nav class="w-100">
                                    <div class="nav nav-tabs" id="product-tab" role="tablist">
                                        <a class="nav-item nav-link" id="product-desc-tab" data-toggle="tab" href="#product-desc" role="tab" aria-controls="product-desc" aria-selected="true">Description</a>
                                        <a class="nav-item nav-link active" id="product-comments-tab" data-toggle="tab" href="#product-comments" role="tab" aria-controls="product-comments" aria-selected="false">Comments</a>
                                    </div>
                                </nav>
                                <div class="tab-content p-3" id="nav-tabContent">
                                    <div class="tab-pane fade" id="product-desc" role="tabpanel" aria-labelledby="product-desc-tab"> <?php echo $product->getDescription() ?></div>
                                    <div class="tab-pane fade show active" id="product-comments" role="tabpanel" aria-labelledby="product-comments-tab">
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
                </section>
            </div>
            <!-- /.content-wrapper -->


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
        <script type="text/javascript" src="../views/js/helper/utils.js"></script>
        <!-- Page specific script -->
        <script type="text/javascript" src="../views/js/productView.js"></script>
    </body>

    </html>