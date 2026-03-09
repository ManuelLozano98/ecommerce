<?php
require_once __DIR__ . "/layout/header.php";
?>
<title><?php echo SITE ?> | Product Information</title>
<link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
<link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<?php require_once __DIR__ . "/layout/endheader.php"; ?>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">

        <?php
        require_once __DIR__ . "/layout/navbar.php";
        require_once __DIR__ . "/layout/aside.php";
        ?>

        <div class="content-wrapper">

            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Product Information</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="home">Home</a></li>
                                <li class="breadcrumb-item active">Product Information</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ADD MODAL -->
            <div class="modal fade" id="modal-default">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Product Information</h4>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>

                        <div class="modal-body">
                            <form id="form">

                                <!-- NAV TABS -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#basicTab">Basic Info</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#pricingTab">Pricing & Status</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#detailsTab">Details</a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-3">

                                    <!-- ================= BASIC INFO ================= -->
                                    <div class="tab-pane fade show active" id="basicTab">
                                        <div class="row">


                                            <div class="col-md-4">
                                                <label for="product_id">Product</label>
                                                <select class="form-control" id="product_id" name="product_id"></select>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="brand">Brand</label>
                                                <input id="brand" type="text" name="brand" class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="manufacturer">Manufacturer</label>
                                                <input id="manufacturer" type="text" name="manufacturer" class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="model">Model</label>
                                                <input id="model" type="text" name="model" class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="dimensions">Dimensions</label>
                                                <input id="dimensions" type="text" name="dimensions" class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="color">Color</label>
                                                <input id="color" type="text" name="color" class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="weight">Weight</label>
                                                <input id="weight" type="text" name="weight" class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="material">Material</label>
                                                <input id="material" type="text" name="material" class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="warranty">Warranty</label>
                                                <input id="warranty" type="text" name="warranty" class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="releaseDate">Release Date</label>
                                                <input id="releaseDate" type="date" name="release_date" class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="expirationDate">Expiration Date</label>
                                                <input id="expirationDate" type="date" name="expiration_date" class="form-control">
                                            </div>

                                        </div>
                                    </div>

                                    <!-- ================= PRICING ================= -->
                                    <div class="tab-pane fade" id="pricingTab">
                                        <div class="row">

                                            <div class="col-md-4">
                                                <label for="ratingAverage">Rating Average</label>
                                                <input id="ratingAverage" type="number" step="0.01" name="rating_average" class="form-control">
                                            </div>

                                            <div class="col-md-4">
                                                <label for="discount">Discount (%)</label>
                                                <input id="discount" type="number" step="0.01" name="discount" class="form-control">
                                            </div>

                                            <div class="col-md-4 mt-4">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="is_featured" class="custom-control-input" id="featuredSwitch">
                                                    <label class="custom-control-label" for="featuredSwitch">Featured</label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <!-- ================= DETAILS ================= -->
                                    <div class="tab-pane fade" id="detailsTab">

                                        <input type="hidden" name="package_contents" id="packageContentsInput">
                                        <input type="hidden" name="features" id="featuresInput">
                                        <input type="hidden" name="tags" id="tagsInput">
                                        <input type="hidden" name="color_options" id="colorOptionsInput">
                                        <input type="hidden" name="size_options" id="sizeOptionsInput">
                                        <input type="hidden" name="technical_details" id="technicalDetailsInput">

                                        <!-- PACKAGE CONTENTS -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Package Contents</strong></div>
                                            <div class="card-body">
                                                <div id="packageList"></div>

                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <input type="text" id="newPackageName" class="form-control" placeholder="Property">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" id="newPackageValue" class="form-control" placeholder="Value">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button type="button" class="btn btn-primary btn-block" onclick="addPackage()">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- FEATURES -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Features</strong></div>
                                            <div class="card-body">
                                                <div id="featuresList"></div>
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <input type="text" id="newFeatureName" class="form-control" placeholder="Property">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" id="newFeatureValue" class="form-control" placeholder="Value">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button type="button" class="btn btn-primary btn-block" onclick="addFeature()">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TAGS -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Tags</strong></div>
                                            <div class="card-body">
                                                <div id="tagsList"></div>
                                                <input type="text" id="newTag" class="form-control mt-2" placeholder="Press Enter to add tag">
                                            </div>
                                        </div>

                                        <!-- COLORS -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Available Colors</strong></div>
                                            <div class="card-body">
                                                <div id="colorList"></div>
                                                <input type="text" id="newColor" class="form-control mt-2" placeholder="Press Enter to add color">
                                            </div>
                                        </div>

                                        <!-- SIZES -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Available Sizes</strong></div>
                                            <div class="card-body">
                                                <div id="sizeList"></div>
                                                <input type="text" id="newSize" class="form-control mt-2" placeholder="Press Enter to add size">
                                            </div>
                                        </div>

                                        <!-- TECHNICAL DETAILS -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Technical Details</strong></div>
                                            <div class="card-body">
                                                <div id="technicalList"></div>

                                                <div class="row mt-2">
                                                    <div class="col-md-5">
                                                        <input type="text" id="newTechKey" class="form-control" placeholder="Property">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="text" id="newTechValue" class="form-control" placeholder="Value">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-primary btn-block" onclick="addTechnical()">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="mt-3 text-right">
                                    <button type="submit" class="btn btn-success">Save Product</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EDIT MODAL -->
            <div class="modal fade" id="modal-edit-default">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4>Edit Product Information</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <form id="form-edit">

                                <input type="hidden" name="id" id="edit-id">
                                <input type="hidden" name="product_id" id="edit-productId">

                                <!-- NAV TABS -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab" href="#editBasicTab">Basic Info</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#editPricingTab">Pricing & Status</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#editDetailsTab">Details</a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-3">

                                    <!-- ================= BASIC INFO ================= -->
                                    <div class="tab-pane fade show active" id="editBasicTab">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="edit-brand">Brand</label>
                                                <input type="text" name="brand" id="edit-brand" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit-manufacturer">Manufacturer</label>
                                                <input type="text" name="manufacturer" id="edit-manufacturer" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit-model">Model</label>
                                                <input type="text" name="model" id="edit-model" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit-dimensions">Dimensions</label>
                                                <input type="text" name="dimensions" id="edit-dimensions" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit-color">Color</label>
                                                <input type="text" name="color" id="edit-color" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit-weight">Weight</label>
                                                <input type="text" name="weight" id="edit-weight" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit-material">Material</label>
                                                <input type="text" name="material" id="edit-material" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit-warranty">Warranty</label>
                                                <input type="text" name="warranty" id="edit-warranty" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit-releaseDate">Release Date</label>
                                                <input type="date" name="release_date" id="edit-releaseDate" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit-expirationDate">Expiration Date</label>
                                                <input type="date" name="expiration_date" id="edit-expirationDate" class="form-control">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ================= PRICING ================= -->
                                    <div class="tab-pane fade" id="editPricingTab">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="edit-ratingAverage">Rating Average</label>
                                                <input type="number" step="0.01" name="rating_average" id="edit-ratingAverage" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="edit-discount">Discount (%)</label>
                                                <input type="number" step="0.01" name="discount" id="edit-discount" class="form-control">
                                            </div>
                                            <div class="col-md-4 mt-4">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" name="is_featured" class="custom-control-input" id="edit-featuredSwitch">
                                                    <label class="custom-control-label" for="edit-featuredSwitch">Featured</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ================= DETAILS ================= -->
                                    <div class="tab-pane fade" id="editDetailsTab">

                                        <input type="hidden" name="package_contents" id="edit-packageContentsInput">
                                        <input type="hidden" name="features" id="edit-featuresInput">
                                        <input type="hidden" name="tags" id="edit-tagsInput">
                                        <input type="hidden" name="color_options" id="edit-colorOptionsInput">
                                        <input type="hidden" name="size_options" id="edit-sizeOptionsInput">
                                        <input type="hidden" name="technical_details" id="edit-technicalDetailsInput">

                                        <!-- PACKAGE CONTENTS -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Package Contents</strong></div>
                                            <div class="card-body">
                                                <div id="editPackageList"></div>
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <input type="text" id="editNewPackageName" class="form-control" placeholder="Property">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" id="editNewPackageValue" class="form-control" placeholder="Value">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button type="button" class="btn btn-primary btn-block" onclick="editPackages()">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- FEATURES -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Features</strong></div>
                                            <div class="card-body">
                                                <div id="editFeaturesList"></div>
                                                <div class="row mt-2">
                                                    <div class="col-md-6">
                                                        <input type="text" id="editNewFeatureName" class="form-control" placeholder="Property">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" id="editNewFeatureValue" class="form-control" placeholder="Value">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <button type="button" class="btn btn-primary btn-block" onclick="editFeatureList()">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- TAGS -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Tags</strong></div>
                                            <div class="card-body">
                                                <div id="editTagsList"></div>
                                                <input type="text" id="editNewTag" class="form-control mt-2" placeholder="Press Enter to add tag">
                                            </div>
                                        </div>

                                        <!-- COLORS -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Available Colors</strong></div>
                                            <div class="card-body">
                                                <div id="editColorList"></div>
                                                <input type="text" id="editNewColor" class="form-control mt-2" placeholder="Press Enter to add color">
                                            </div>
                                        </div>

                                        <!-- SIZES -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Available Sizes</strong></div>
                                            <div class="card-body">
                                                <div id="editSizeList"></div>
                                                <input type="text" id="editNewSize" class="form-control mt-2" placeholder="Press Enter to add size">
                                            </div>
                                        </div>

                                        <!-- TECHNICAL DETAILS -->
                                        <div class="card mb-3">
                                            <div class="card-header"><strong>Technical Details</strong></div>
                                            <div class="card-body">
                                                <div id="editTechnicalList"></div>
                                                <div class="row mt-2">
                                                    <div class="col-md-5">
                                                        <input type="text" id="editNewTechKey" class="form-control" placeholder="Property">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <input type="text" id="editNewTechValue" class="form-control" placeholder="Value">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-primary btn-block" onclick="editTechnicalDetails()">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" form="form-edit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gif loader -->
            <div id="loader-container">
                <div class="loader" style="display: none;"></div>
            </div>

            <!-- TABLE -->
            <section class="content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <div class="box-tools pull-right">
                                </div>
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h2 class="box-title">Product Information <button class="btn btn-success"
                                                            id="addBtn" data-toggle="modal"
                                                            data-target="#modal-default"><i
                                                                class="fa fa-plus-circle"></i>
                                                            Add</button></h2>
                                                    <h3 class="card-title">Data</h3>
                                                </div>
                                                <!-- /.card-header -->

                                                <div class="card-body" id="records">
                                                    <table id="tableProductInformation" class="table table-bordered table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Product</th>
                                                                <th>Brand</th>
                                                                <th>Manufacturer</th>
                                                                <th>Model</th>
                                                                <th>Dimensions</th>
                                                                <th>Color</th>
                                                                <th>Weight</th>
                                                                <th>Material</th>
                                                                <th>Warranty</th>
                                                                <th>Release Date</th>
                                                                <th>Expiration Date</th>
                                                                <th>Rating Avg</th>
                                                                <th>Discount (%)</th>
                                                                <th>Featured</th>
                                                                <th>Package Contents</th>
                                                                <th>Features</th>
                                                                <th>Tags</th>
                                                                <th>Color Options</th>
                                                                <th>Size Options</th>
                                                                <th>Technical Details</th>
                                                                <th>Created At</th>
                                                                <th>Updated At</th>
                                                                <th>Actions</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>

                                            </div>
                                        </div>
            </section>

        </div>
        <?php require_once __DIR__ . "/layout/footer.php"; ?>


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
    <script src="<?php echo ADMINLTE ?>plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?php echo ADMINLTE ?>/dist/js/adminlte.min.js"></script>
    <!-- Generic script for utilities -->
    <script type="text/javascript" src="views/js/helper/utils.js"></script>
    <script src="views/js/productInformation.js"></script>

</body>

</html>