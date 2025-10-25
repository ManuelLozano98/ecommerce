<?php
require_once __DIR__ . '/layout/header.php';
?>
<title><?php echo SITE ?> | Products</title>
<link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
<!-- Select 2 -->
<link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

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
                                <li class="breadcrumb-item"><a href="./">Home</a></li>
                                <li class="breadcrumb-item active">Products</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>


            <div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="modalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title" id="modalLabel">Add Product</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <!-- Tabs -->
                            <ul class="nav nav-tabs" id="formTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1"
                                        role="tab">Product data</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2"
                                        role="tab">Categories</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3"
                                        role="tab">Images</a>
                                </li>
                            </ul>

                            <!-- Form -->
                            <form id="form" method="POST" name="form" enctype="multipart/form-data">
                                <div class="tab-content mt-3">
                                    <!-- Tab 1 -->
                                    <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                                        <div class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                placeholder="Write a name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea id="description" name="description" class="form-control" rows="4"
                                                maxlength="255" placeholder="Write here..."></textarea>
                                            <small class="form-text text-muted text-right">
                                                <span id="counter">0/255</span>
                                            </small>
                                        </div>
                                        <div class="form-group">
                                            <label for="price">Price</label>
                                            <div class="input-group">
                                                <!-- <input type="text" inputmode="decimal" pattern="^\d+(\.\d{1,2})?$"
                                                name="price" class="form-control" id="price" /> -->
                                                <input type="number" min="0" step="0.01" class="form-control" id="price"
                                                    name="price" placeholder="Enter price">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">€</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="stock">Stock</label>
                                                <input type="number" min="0" class="form-control" name="stock"
                                                    id="stock" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="code">Product Code</label>
                                                <button type="button" id="generate-code" style="margin-left: 5px"
                                                    class="btn btn-primary btn-sm"><i class="fas fa-sync mr-2"></i>
                                                    Generate code
                                                </button>
                                                <input type="number" min="0" class="form-control" name="code" id="code"
                                                    required>
                                                <svg id="barcode"></svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tab 2 -->
                                    <div class="tab-pane fade" id="tab2" role="tabpanel">
                                        <div class="select2-green">
                                            <label for="categorySelect">Select Category</label>
                                            <select id="categorySelect" name="category_id" class="select2 form-control" data-dropdown-css-class="select2-green">
                                            </select>
                                        </div>
                                        <a id="categoriesModal" style="display:none"
                                            href="categories?openModal=true" target="_blank">Don't have any
                                            categories? Add one</a>
                                    </div>

                                    <!-- Tab 3 -->
                                    <div class="tab-pane fade" id="tab3" role="tabpanel">
                                        <div class="form-group">
                                            <label>Add an image</label>
                                            <input type="file" id="image" name="image" class="form-control-file">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button id="save" type="submit" class="btn btn-primary">Save changes</button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- /.modal -->

        <div class="modal fade" id="modal-edit-default">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Edit Product</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs" id="edit-formTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="edit-tab1-tab" data-toggle="tab" href="#edit-tab1"
                                    role="tab">Product data</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="edit-tab2-tab" data-toggle="tab" href="#edit-tab2"
                                    role="tab">Categories</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="edit-tab3-tab" data-toggle="tab" href="#edit-tab3"
                                    role="tab">Images</a>
                            </li>
                        </ul>
                        <form name="form-edit" id="form-edit" method="POST" enctype="multipart/form-data">
                            <div class="tab-content mt-3">
                                <input type="hidden" value="" id="productId" name="id" />
                                <!-- Tab 1 -->
                                <div class="tab-pane fade show active" id="edit-tab1" role="tabpanel">
                                    <div class="form-group">
                                        <label for="edit-name">Name</label>
                                        <input type="text" class="form-control" name="name" id="edit-name"
                                            placeholder="Write a name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-description">Description</label>
                                        <textarea id="edit-description" name="description" class="form-control"
                                            rows="4" maxlength="255" placeholder="Write here..."></textarea>
                                        <small class="form-text text-muted text-right">
                                            <span id="edit-counter">0/255</span>
                                        </small>
                                    </div>
                                    <div class="form-group">
                                        <label for="edit-price">Price</label>
                                        <div class="input-group">
                                            <input type="number" min="0" step="0.01" class="form-control"
                                                id="edit-price" name="price" placeholder="Enter price">
                                            <div class="input-group-append">
                                                <span class="input-group-text">€</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                            <label for="edit-stock">Stock</label>
                                            <input type="number" min="0" class="form-control" name="stock"
                                                id="edit-stock" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label for="edit-code">Product Code</label>
                                            <button type="button" id="edit-generate-code" style="margin-left: 5px"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-sync mr-2"></i>Generate code
                                            </button>
                                            <input type="number" min="0" class="form-control" name="code"
                                                id="edit-code" required>
                                            <svg id="edit-barcode"></svg>
                                        </div>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="customSwitch1" name="active">
                                            <label class="custom-control-label" for="customSwitch1">Active</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab 2 -->
                                <div class="tab-pane fade" id="edit-tab2" role="tabpanel">
                                    <div class="select2-green">
                                        <select id="edit-categorySelect" class="select2 form-control" data-placeholder="Select Category"
                                            data-dropdown-css-class="select2-green" style="width: 100%;" name="category_id">
                                        </select>
                                    </div>
                                </div>

                                <!-- Tab 3 -->
                                <div class="tab-pane fade" id="edit-tab3" role="tabpanel">
                                    <div class="form-group">
                                        <label for="edit-image">Upload or change image</label>
                                        <input type="file" id="edit-image" name="image" class="form-control-file">
                                        <img width="200px" height="200px" alt="edit-img" id="edit-img" style="object-fit: cover;" />
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button id="edit-save" type="submit" class="btn btn-primary">Save changes</button>
                            </div>

                        </form>
                    </div>

                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->


        <!-- Gif loader -->
        <div id="loader-container">
            <div class="loader" style="display: none;">
            </div>
        </div>

        <!-- Full description modal -->
        <div class="modal fade" id="viewModalText">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-description">Full text</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="modal-body" style="word-wrap: break-word;">
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
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
                                                <h2 class="box-title">Product <button class="btn btn-success"
                                                        id="addBtn" data-toggle="modal"
                                                        data-target="#modal-default"><i
                                                            class="fa fa-plus-circle"></i>
                                                        Add</button></h2>
                                                <h3 class="card-title">Data</h3>
                                            </div>
                                            <!-- /.card-header -->
                                            <div class="card-body">
                                                <div class="card-body" id="records">
                                                    <table id="tableProducts"
                                                        class="table table-bordered table-hover">
                                                        <thead>
                                                            <th>Id</th>
                                                            <th>Name</th>
                                                            <th>Description</th>
                                                            <th>Code</th>
                                                            <th>Image</th>
                                                            <th>Stock</th>
                                                            <th>Price</th>
                                                            <th>Category</th>
                                                            <th>Date</th>
                                                            <th>Active</th>
                                                            <th>Actions</th>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>


                                            </div><!-- /.card-body -->
                                        </div><!-- /.card -->
                                    </div><!-- /.col-12 -->
                                </div><!-- /.row -->
                            </div><!-- /.container-fluid -->
                        </div><!-- /.box-header -->
                    </div><!-- /.box -->
                </div><!-- /.cold-md-12 -->
            </div><!-- /.row -->
        </section><!-- /.content -->
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
    <script type="text/javascript" src="views/js/product.js"></script>
</body>

</html>