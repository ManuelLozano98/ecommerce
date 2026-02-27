    <?php
    require_once __DIR__ . '/layout/header.php';
    ?>
    <title><?php echo SITE ?></title>
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
    <!-- Ion Slider -->
    <link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/ion-rangeslider/css/ion.rangeSlider.min.css">
    <link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/index.css">

    <?php require_once __DIR__ . '/layout/endheader.php'; ?>

    <body>
        <div class="wrapper">
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
                <!-- Sidebar -->
                <div class="sidebar">

                    <!-- Sidebar Menu -->
                    <nav class="mt-2" id="menu">
                        <h4>Categories</h4>
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <?php
                            foreach ($categories as $category): ?>
                                <li class="nav-item">
                                    <a href="<?php echo strtolower($category->getName()) ?>" class="nav-link">
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
                                <h1>Products</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="<?php echo ROOT ?>">Home</a></li>
                                    <li class="breadcrumb-item active">
                                        <?php echo $data['category']->getName(); ?>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div><!-- /.container-fluid -->
                </section>
                <section class="content">
                    <div class="container-fluid">
                        <form id="filter-form">
                            <div class="row mb-2">
                                <div class="col-12 col-md-6">
                                    <h3>Search</h3>
                                    <div id="search">
                                        <input type="text" class="form-control search-form search-product" name="search" placeholder="Search any product" value="">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <h3>Filters</h3>
                                    <div class="filters d-flex flex-wrap gap-2 align-items-center">
                                        <!-- Advanced price filter -->
                                        <div class="dropdown d-inline-block">
                                            <button
                                                class="px-3 py-2 rounded-pill text-nowrap bg-secondary border small fw-medium text-center dropdown-toggle text-white"
                                                type="button"
                                                data-toggle="dropdown"
                                                aria-expanded="false">
                                                Price
                                            </button>
                                            <ul class="dropdown-menu p-3" style="min-width: 220px;">
                                                <input id="slider" type="text" name="range_price[]" value="">
                                            </ul>
                                        </div>

                                        <!-- Advanced review filter -->
                                        <div class="dropdown d-inline-block">
                                            <button
                                                class="px-3 py-2 rounded-pill text-nowrap bg-secondary border small fw-medium text-center dropdown-toggle text-white"
                                                type="button"
                                                data-toggle="dropdown"
                                                aria-expanded="false">
                                                Score
                                            </button>
                                            <ul class="dropdown-menu p-3" style="min-width: 220px;">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <li>
                                                        <div class="form-check">
                                                            <input class="custom-control-input custom-control-input-secondary custom-control-input-outline" type="checkbox" name="scores[]" value="<?php echo $i ?>" id="review<?php echo $i ?>">
                                                            <label for="review<?php echo $i ?>" class="custom-control-label font-weight-normal"> <?php echo $i ?> stars</label>
                                                        </div>
                                                    </li>
                                                <?php endfor; ?>
                                            </ul>
                                        </div>

                                        <input type="hidden" name="sort" id="sort-input">

                                        <!-- Lowest price filter -->
                                        <div class="d-inline-block">
                                            <button id="lowest-price-filter" class="px-3 py-2 rounded-pill text-nowrap bg-secondary border small fw-medium text-center sort-btn" data-sort="price_asc" style="cursor: pointer;" value="">Lowest price</button>
                                        </div>

                                        <!-- Higher price filter -->
                                        <div class="d-inline-block">
                                            <button id="higher-price-filter" class="px-3 py-2 rounded-pill text-nowrap bg-secondary border small fw-medium text-center sort-btn" data-sort="price_desc" style="cursor: pointer;">Higher price</button>
                                        </div>

                                        <!-- Top rated filter -->
                                        <div class="d-inline-block">
                                            <button id="top-rated-filter" class="px-3 py-2 rounded-pill text-nowrap bg-secondary border small fw-medium text-center sort-btn" data-sort="top_rated" style="cursor: pointer;">Top rated</button>
                                        </div>

                                        <!-- Top sellers filter -->
                                        <div class="d-inline-block">
                                            <button id="top-sellers-filter" class="px-3 py-2 rounded-pill text-nowrap bg-secondary border small fw-medium text-center sort-btn" data-sort="top_sellers" style="cursor: pointer;">Top sellers</button>
                                        </div>

                                        <!-- Reset button -->
                                        <div class="d-inline-block float-right">
                                            <button id="reset" class="px-3 py-2 text-nowrap bg-black border small fw-medium text-center" style="cursor: pointer;">Reset</button>
                                        </div>
                                    </div>
                                </div>
                        </form>
                    </div><!-- /.container-fluid -->
                </section>
                <section class="main-content">
                    <div class="row" id="content-products">
                        <?php foreach ($products as $product): ?>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4" id="products">
                                <div class="product-card w-75 h-100">
                                    <a href="<?php echo ROOT . "/" . strtolower($product->getCategory()->getName()) . "/" . $product->getSlug() ?>" class="product-link">
                                        <div class="card-header h-20 bg-light">
                                            <?php if ($product->getImage()) : ?>
                                                <img src="<?php echo UPLOADS_IMAGES . '/' . $product->getImage() ?>"
                                                    alt="<?php echo $product->getName() ?>" class="card-img-center h-100 w-100">
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-header">
                                            <div class="product-price">
                                                <span><?php echo $product->getPrice() ?>€</span>
                                            </div>
                                            <div class="ratings">
                                                <span class="stars">
                                                    <?php
                                                    $averageRating = $reviews[$product->getId()]['average'];
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= floor($averageRating)) {
                                                            echo '<i class="fas fa-star"></i>';
                                                        } elseif ($i - 1 < $averageRating && $i > floor($averageRating)) {
                                                            echo '<i class="fas fa-star-half-alt"></i>';
                                                        } else {
                                                            echo '<i class="far fa-star"></i>';
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                                <span class="reviews-count"><?php echo $reviews[$product->getId()]['total'] ?> reviews</span>
                                            </div>
                                        </div>
                                        <div class="card-body bg-white">
                                            <div class="product-title"><?php echo $product->getName() ?></div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <section class="pagination-content">
                    <div class="container-fluid">
                        <div class="row" id="pagination-card">
                            <?php if ($totalPages > 1): ?>
                                <?php
                                $end = $currentPage !== 1 ? $records * $currentPage : $records;
                                $start = $currentPage !== 1 ? $end - $records + 1 : 1;

                                if ($totalPages === $currentPage) {
                                    $end = $totalProducts;
                                    $start = $totalProducts - $records + 1;
                                }
                                ?>
                                <div class="col-md-6">
                                    <div class="dataTables_info" id="tableProducts_info" role="status" aria-live="polite">
                                        Showing <?php echo $start ?> to <?php $end; ?> of <?php echo $totalProducts; ?> entries
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="tableProducts_paginate">
                                        <ul class="pagination">
                                            <!-- Previous page -->
                                            <?php if ($currentPage > 1): ?>
                                                <li class="paginate_button page-item previous">
                                                    <a href="#" data-page="<?php echo $currentPage - 1; ?>" aria-controls="tableProducts" class="page-link">Previous</a>
                                                </li>
                                            <?php else: ?>
                                                <li class="paginate_button page-item previous disabled">
                                                    <a href="#" aria-controls="tableProducts" class="page-link">Previous</a>
                                                </li>
                                            <?php endif; ?>

                                            <!-- Numerated pages -->
                                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                <li class="paginate_button page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                                                    <a href="#" data-page="<?php echo $i; ?>" aria-controls="tableProducts" class="page-link"><?php echo $i; ?></a>
                                                </li>
                                            <?php endfor; ?>

                                            <!-- Next page -->
                                            <?php if ($currentPage < $totalPages): ?>
                                                <li class="paginate_button page-item next">
                                                    <a href="#" data-page="<?php echo $currentPage + 1; ?>" aria-controls="tableProducts" class="page-link">Next</a>
                                                </li>
                                            <?php else: ?>
                                                <li class="paginate_button page-item next disabled">
                                                    <a href="#" aria-controls="tableProducts" class="page-link">Next</a>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
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
        <!-- AdminLTE App -->
        <script src="<?php echo ADMINLTE ?>dist/js/adminlte.min.js"></script>
        <!-- Generic script for utilities -->
        <script type="text/javascript" src="views/js/helper/utils.js"></script>
        <!-- Ion Slider -->
        <script src="<?php echo ADMINLTE ?>plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
        <!-- Page specific script -->
        <script type="text/javascript" src="views/js/categoryView.js"></script>
    </body>

    </html>