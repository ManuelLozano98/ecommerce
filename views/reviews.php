<?php
require_once __DIR__ . '/layout/header.php';
?>
<title><?php echo SITE ?> | Reviews</title>
<link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
<!-- Select 2 -->
<link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?php echo ADMINLTE ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<style>
    .rating {
        --size: 30px;
        --mask: conic-gradient(from -18deg at 61% 34.5%, #0000 108deg, #000 0) 0 / var(--size),
            conic-gradient(from 270deg at 39% 34.5%, #0000 108deg, #000 0) 0 / var(--size),
            conic-gradient(from 54deg at 68% 56%, #0000 108deg, #000 0) 0 / var(--size),
            conic-gradient(from 198deg at 32% 56%, #0000 108deg, #000 0) 0 / var(--size),
            conic-gradient(from 126deg at 50% 69%, #0000 108deg, #000 0) 0 / var(--size);
        --bg: linear-gradient(90deg, #f39c12 calc(var(--size) * var(--val)), #ddd 0);
        height: var(--size);
        width: calc(var(--size) * 5);
        border: 0;
        /* Firefox adds a default border to ranges */
        -webkit-appearance: none;
        appearance: none;
        cursor: pointer;

        /* Chrome and Safari */
        &::-webkit-slider-runnable-track {
            height: 100%;
            mask: var(--mask);
            mask-composite: intersect;
            background: var(--bg);
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        &::-webkit-slider-thumb {
            opacity: 0;
        }

        /* Firefox */
        &::-moz-range-track {
            height: 100%;
            mask: var(--mask);
            mask-composite: intersect;
            background: var(--bg);
            print-color-adjust: exact;
        }

        &::-moz-range-thumb {
            opacity: 0;
        }
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
                            <h1>Reviews</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="./">Home</a></li>
                                <li class="breadcrumb-item active">Reviews</li>
                            </ol>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>

            <div class="modal fade" id="modal-default">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Add Review</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="form" id="form" method="POST">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <label for="title" class="form-label fw-semibold">Title</label>
                                        <textarea id="title" class="form-control" rows="4" maxlength="255"
                                            placeholder="Write here..." name="title"></textarea>
                                        <div class="text-end text-muted mt-1">
                                            <span id="counter-title">0/255</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="comment" class="form-label fw-semibold">Comment</label>
                                        <textarea id="comment" class="form-control" rows="4" maxlength="255"
                                            placeholder="Write here..." name="comment"></textarea>
                                        <div class="text-end text-muted mt-1">
                                            <span id="counter-comment">0/255</span>
                                        </div>
                                    </div>



                                    <div class="col-sm-12">
                                        <label for="users" class="form-label fw-semibold">Users to add the review
                                            to: <span style="color: red">*</span></label>
                                        <div class="select2-green">
                                            <select class="select2" multiple="multiple" data-placeholder="Select Users"
                                                data-dropdown-css-class="select2-green" style="width: 100%;"
                                                name="users[]" id="users">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <label for="products" class="form-label fw-semibold">Products to add the review
                                            to: <span style="color: red">*</span></label>
                                        <div class="select2-green">
                                            <select class="select2" multiple="multiple"
                                                data-placeholder="Select Products"
                                                data-dropdown-css-class="select2-green" style="width: 100%;"
                                                name="products[]" id="products">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <label for="rating" class="form-label fw-semibold">Rating</label>
                                        <div class="star-rating">
                                            <input id="rating" type="range" min="0.5" max="5" step="0.5" value="2.5"
                                                class="rating" style="--val:2.5"
                                                oninput="this.style='--val:'+this.value" name="rating">

                                        </div>
                                    </div>


                                </div>
                                <div class="modal-footer justify-content-between">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    <button id="save" type="submit" class="btn btn-primary">Save
                                        changes</button>
                                </div>

                            </form>
                        </div>

                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!-- /.modal -->

            <div class="modal fade" id="modal-edit-default">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Edit Review</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form name="form-edit" id="form-edit" method="POST">
                                <div class="row">
                                    <input type="hidden" name="id" id="edit-idreview">
                                    <input type="hidden" name="id" id="edit-iduser">
                                    <input type="hidden" name="id" id="edit-idproduct">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="edit-title">Title</label>
                                            <textarea id="edit-title" class="form-control" rows="4" maxlength="255"
                                                placeholder="Write here..." name="title"></textarea>
                                            <div class="text-end text-muted mt-1">
                                                <span id="edit-counter-title">0/255</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="edit-comment" class="form-label fw-semibold">Comment</label>
                                            <textarea id="edit-comment" class="form-control" rows="4" maxlength="255"
                                                placeholder="Write here..." name="comment"></textarea>
                                            <div class="text-end text-muted mt-1">
                                                <span id="edit-counter-comment">0/255</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <label for="rating" class="form-label fw-semibold">Rating</label>
                                        <div class="star-rating">
                                            <input type="range" min="0.5" max="5" step="0.5" value="2.5" class="rating"
                                                style="--val:2.5" oninput="this.style='--val:'+this.value"
                                                id="edit-rating" name="rating">
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id="edit-active" name="active">
                                            <label class="custom-control-label" for="edit-active">Active</label>
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
                                                    <h2 class="box-title">Review <button class="btn btn-success"
                                                            id="addBtn" data-toggle="modal"
                                                            data-target="#modal-default"><i
                                                                class="fa fa-plus-circle"></i>
                                                            Add</button></h2>
                                                    <h3 class="card-title">Data</h3>
                                                </div>
                                                <!-- /.card-header -->
                                                <div class="card-body">
                                                    <div class="card-body" id="records">
                                                        <table id="tableReviews"
                                                            class="table table-bordered table-hover">
                                                            <thead>
                                                                <th>Id</th>
                                                                <th>User</th>
                                                                <th>Product</th>
                                                                <th>Rating</th>
                                                                <th>Title</th>
                                                                <th>Comment</th>
                                                                <th>Created</th>
                                                                <th>Updated</th>
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
        </div>

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
    <script type="text/javascript" src="views/js/review.js"></script>
</body>

</html>