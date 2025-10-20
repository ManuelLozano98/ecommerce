<?php
require_once __DIR__ . '/../../config/constants.php';
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
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">

            <div class="image">
                <?php if ($user): ?>
                    <img src="<?php echo  UPLOADS_USERS_IMAGES ?>/<?php echo $user->getImage() ?>" class="img-circle elevation-2" alt="User Image">
                <?php else: ?>
                    <img src="<?php echo  ADMINLTE ?>dist/img/avatar5.png" class="img-circle elevation-2" alt="No image">
                <?php endif; ?>
            </div>

            <?php if ($user): ?>
                <div class="info">
                    <a href="my-profile" class="d-block">
                        <?php echo $user->getUsername(); ?></a>
                </div>
            <?php endif; ?>
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="categories" class="nav-link">
                        <i class="nav-icon fas fa-th"></i>
                        <p>
                            Categories
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="products" class="nav-link">
                        <i class="nav-icon fas fa-table"></i>
                        <p>
                            Products
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users" class="nav-link">
                        <i class="nav-icon fas fa-user"></i>
                        <p>
                            Users
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="roles" class="nav-link">
                        <i class="nav-icon fas fa-key"></i>
                        <p>
                            Roles
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="reviews" class="nav-link">
                        <i class="nav-icon fas fa-star"></i>
                        <p>
                            Reviews
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="sales" class="nav-link">
                        <i class="nav-icon fas fa-comment-dollar"></i>
                        <p>
                            Sales
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>