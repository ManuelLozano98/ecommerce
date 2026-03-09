<?php
require_once __DIR__ . '/layout/header.php';
?>
<title><?php echo SITE ?> | Gallery</title>
<link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">

<?php require_once __DIR__ . '/layout/endheader.php'; ?>

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
                            <h1>Gallery</h1>
                        </div>

                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="./">Home</a></li>
                                <li class="breadcrumb-item active">Gallery</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>


            <!-- ADD IMAGE MODAL -->
            <div class="modal fade" id="modal-default">

                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title">Add Image</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <form id="form" name="form" method="POST" enctype="multipart/form-data">

                            <div class="modal-body">

                                <div class="form-group">
                                    <label>Product</label>
                                    <select name="product_id" id="productSelect" class="form-control">
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Image</label>
                                    <input type="file" name="image" id="image" class="form-control-file" required>
                                    <img id="preview" width="200">
                                </div>

                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="type" class="form-control">
                                        <option value="gallery">Gallery</option>
                                        <option value="thumbnail">Thumbnail</option>
                                        <option value="main">Main</option>
                                    </select>
                                </div>

                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="activeSwitch" name="active">
                                    <label class="custom-control-label" for="activeSwitch">Active</label>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>


            <!-- EDIT MODAL -->

            <div class="modal fade" id="modal-edit-default">

                <div class="modal-dialog">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h4 class="modal-title">Edit Image</h4>
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>
                        </div>

                        <form id="form-edit" method="POST" enctype="multipart/form-data">

                            <input type="hidden" id="imageId" name="id">

                            <div class="modal-body">

                                <div class="form-group">
                                    <label>Product</label>
                                    <select name="product_id" id="edit-productSelect" class="form-control">
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Change Image</label>
                                    <input type="file" name="image" id="edit-image" class="form-control-file">

                                    <br>

                                    <img id="edit-preview" width="200" height="200" style="object-fit:cover;">
                                </div>

                                <div class="form-group">
                                    <label>Type</label>
                                    <select name="type" id="edit-type" class="form-control">
                                        <option value="gallery">Gallery</option>
                                        <option value="thumbnail">Thumbnail</option>
                                        <option value="main">Main</option>
                                    </select>
                                </div>

                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="edit-active" name="active">
                                    <label class="custom-control-label" for="edit-active">Active</label>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>

                        </form>

                    </div>
                </div>
            </div>


            <!-- MAIN CONTENT -->

            <section class="content">

                <div class="row">

                    <div class="col-12">

                        <div class="card">

                            <div class="card-header">

                                <h3 class="card-title">

                                    Gallery Images

                                    <button class="btn btn-success" data-toggle="modal" data-target="#modal-default" id="addBtn">
                                        <i class="fa fa-plus-circle"></i> Add
                                    </button>

                                </h3>

                            </div>


                            <div class="card-body">
                                <table id="tableGallery" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product</th>
                                            <th>Image</th>
                                            <th>Type</th>
                                            <th>Active</th>
                                            <th>Created</th>
                                            <th>Updated</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>


        <?php
        require_once __DIR__ . "/layout/footer.php";
        ?>

    </div>


    <script src="<?php echo ADMINLTE ?>plugins/jquery/jquery.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo ADMINLTE ?>plugins/sweetalert2/sweetalert2.min.js"></script>

    <script src="<?php echo ADMINLTE ?>plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>

    <script src="<?php echo ADMINLTE ?>dist/js/adminlte.min.js"></script>
    <!-- Generic script for utilities -->
    <script type="text/javascript" src="views/js/helper/utils.js"></script>

    <script src="views/js/gallery.js"></script>

</body>

</html>