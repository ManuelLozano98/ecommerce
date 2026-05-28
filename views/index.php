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
                <div class="sidebar vh-100 overflow-auto position-sticky top-0 p-3">

                    <!-- Sidebar Menu -->
                    <nav class="mt-2" id="menu">
                        <h4>Categories</h4>
                        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                            <?php
                            foreach ($categories as $category): ?>
                                <li class="nav-item">
                                    <a href="<?php echo $category->getSlug() ?>" class="nav-link">
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
                        <form id="filter-form">
                            <div class="row align-items-center mb-3">
                                <div class="col-12 col-md-6">
                                    <h1 class="fw-bold mb-2">Products</h1>
                                    <div id="search">
                                        <input type="search" class="form-control search-form search-product rounded-pill ps-5" name="search" placeholder="Search any product" value="" aria-label="Search" />
                                        <i class="fas fa-search search-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="filters d-flex flex-wrap gap-2 mb-3">
                                    <!-- Category filter -->
                                    <div class="dropdown">
                                        <button class="btn btn-outline-dark rounded-pill dropdown-toggle"
                                            data-toggle="dropdown"
                                            aria-expanded="false">
                                            Category
                                        </button>

                                        <ul id="menuCategories" class="dropdown-menu p-3 shadow-sm" style="min-width: 220px; max-height: 300px; overflow-y: auto;">
                                            <div class="mb-2">
                                                <input type="search" id="search-category" class="form-control form-control-sm" placeholder="Search...">
                                            </div>
                                            <?php for ($i = 0; $i < count($categories); $i++): ?>
                                                <li>
                                                    <div class="form-check">
                                                        <input class="custom-control-input custom-control-input-secondary custom-control-input-outline" type="checkbox" name="categories[]" value="<?php echo $categories[$i]->getId() ?>" id="category<?php echo $i ?>">
                                                        <label class="custom-control-label font-weight-normal" for="category<?php echo $i ?>">
                                                            <?php echo $categories[$i]->getName() ?>
                                                        </label>
                                                    </div>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    </div>

                                    <!-- Advanced price filter -->
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-outline-dark rounded-pill dropdown-toggle"
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
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-outline-dark rounded-pill dropdown-toggle"
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
                                    <div>
                                        <button id="lowest-price-filter" class="btn btn-outline-primary rounded-pill sort-btn" data-sort="price_asc" style="cursor: pointer;" value=""><i class="fas fa-arrow-down"></i> Lowest price</button>
                                    </div>

                                    <!-- Highest price filter -->
                                    <div>
                                        <button id="higher-price-filter" class="btn btn-outline-primary rounded-pill sort-btn" data-sort="price_desc" style="cursor: pointer;"><i class="fas fa-arrow-up"></i> Highest price</button>
                                    </div>

                                    <!-- Top rated filter -->
                                    <div>
                                        <button id="top-rated-filter" class="btn btn-outline-warning rounded-pill sort-btn" data-sort="top_rated" style="cursor: pointer;"><i class="fas fa-star"></i> Top rated</button>
                                    </div>

                                    <!-- Top sellers filter -->
                                    <div>
                                        <button id="top-sellers-filter" class="btn btn-outline-success rounded-pill sort-btn" data-sort="top_sellers" style="cursor: pointer;"><i class="far fa-heart"></i> Top sellers</button>
                                    </div>
                                    <!-- Reset button -->
                                    <div>
                                        <button id="reset" class="btn btn-outline-secondary rounded-pill"></i>
                                            Reset
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div><!-- /.container-fluid -->
                </section>
                <section class="main-content">
                    <div class="container-fluid">
                        <div class="row" id="content-products">
                            <?php foreach ($products as $product): ?>
                                <?php $real_price = $product->real_price; $product = $product->product;?>
                                <div class="col-6 col-md-4 col-lg-3 col-xl-2 mb-4">
                                    <div class="card h-100 shadow-sm border-0">
                                        <!-- IMAGE -->
                                        <a href="<?php echo ROOT . "/" . $product->getCategory()->getSlug() . "/" . $product->getSlug() ?>">
                                            <div class="ratio ratio-1x1 bg-light">
                                                <?php if ($product->getImage()) : ?>
                                                    <img src="<?php echo UPLOADS_IMAGES . '/' . $product->getImage() ?>"
                                                        class="card-img-top object-fit-cover"
                                                        alt="<?php echo $product->getName() ?>">
                                                <?php endif; ?>
                                            </div>
                                        </a>

                                        <div class="card-body d-flex flex-column">

                                            <!-- PRICE -->
                                            <?php if ($real_price != $product->getPrice()): ?>
                                                <h5 class="text-danger">
                                                    <del><?php echo $product->getPrice() ?>€</del>
                                                </h5>
                                                <h5 class="fw-bold mb-1 d-inline">
                                                    <?php echo $real_price ?>€
                                                </h5>
                                            <?php else: ?>
                                                <h5 class="fw-bold mb-1">
                                                    <?php echo $product->getPrice() ?>€
                                                </h5>
                                            <?php endif; ?>

                                            <!-- RATING -->
                                            <div class="text-warning mb-1">
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
                                                <span class="text-muted small">
                                                    (<?php echo $reviews[$product->getId()]['total'] ?>)
                                                </span>
                                            </div>

                                            <p class="card-text text-muted flex-grow-1">
                                                <?php echo $product->getName() ?>
                                            </p>

                                            <a href="<?php echo ROOT . "/" . $product->getCategory()->getSlug() . "/" . $product->getSlug() ?>"
                                                class="btn btn-dark w-100 btn-sm rounded-pill">
                                                View product
                                            </a>

                                        </div>

                                    </div>

                                </div>
                            <?php endforeach; ?>

                        </div>
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
        <script type="text/javascript" src="<?php echo ROOT . "/" ?>views/js/helper/utils.js"></script>
        <!-- Ion Slider -->
        <script src="<?php echo ADMINLTE ?>plugins/ion-rangeslider/js/ion.rangeSlider.min.js"></script>
        <!-- Page specific script -->
        <script type="text/javascript" src="<?php echo ROOT . "/" ?>views/js/index.js"></script>
    </body>

    </html>