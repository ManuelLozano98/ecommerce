<?php
require_once __DIR__ . '/../../config/constants.php';
$cartItems = $cart ?? $_SESSION['cart'] ?? [];
$totalItems = 0;
foreach ($cartItems as $item) {
  $totalItems += $item->getQuantity();
}
$user = $_SESSION['user'] ?? NULL;
$admin = $user && !empty($user['roles']) && in_array('admin', $user['roles'], true);
?>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <?php if ($admin): ?>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="<?php echo ROOT . '/admin' ?>" class="nav-link">Admin</a>
      </li>
    <?php endif; ?>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <?php if (!$user): ?>
      <!-- Log in/Register menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <span class="line-1">Hello, sign in</span>
          <i class="fas fa-user ml-1"></i>
        </a>
        <!-- Dropdown -->
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-2" style="min-width: 300px;">
          <a class="" href="<?php echo ROOT ?>/login"><button class="btn-primary fas btn-block mb-2 font-weight-bold p-2">Log In</button></a>
          <p>New customer? <a href="<?php echo ROOT ?>/register">Start here</a></p>
        </div>
      </li>
    <?php else: ?>
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <span class="line-1">Hello, <?php echo $user["data"]->getUsername() ?></span>
          <i class="fas fa-user ml-1"></i>
        </a>
        <!-- Dropdown -->
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-2" style="min-width: 250px;">
          <a class="" href="<?php echo ROOT ?>/profile"><button class="btn-primary fas btn-block mb-2 font-weight-bold p-2">My profile</button></a>
        </div>
      </li>
    <?php endif; ?>
    <!-- Cart menu -->
    <li class="nav-item dropdown">
      <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="fas fa-shopping-cart"></i>
        <span class="badge badge-danger navbar-badge"><?php echo $totalItems ?></span>
      </a>
      <?php if ($cartItems): ?>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-2" style="min-width: 300px;">
          <?php foreach ($cartItems as $item): ?>
            <a href="<?php echo ROOT . '/' . $item->getProduct()->getCategory()->getSlug() . "/" . $item->getProduct()->getSlug() ?>"
              class="dropdown-item px-2 py-2">
              <div class="d-flex align-items-center gap-2">
                <img
                  src="<?php echo UPLOADS_IMAGES . '/' . $item->getProduct()->getImage() ?>"
                  alt="<?php echo $item->getProduct()->getName() ?>"
                  class="rounded"
                  style="width: 50px; height: 50px; object-fit: cover;">
                <div class="flex-grow-1 overflow-hidden">
                  <div class="fw-semibold text-truncate">
                    <?php echo $item->getProduct()->getName() ?>
                  </div>

                  <small class="text-muted">
                    <?php echo $item->getQuantity() ?> x <?php echo $item->getProduct()->getPrice() ?>€
                  </small>
                </div>

              </div>
            </a>

            <div class="dropdown-divider my-1"></div>
          <?php endforeach; ?>

          <div class="dropdown-divider"></div>

          <a href="<?php echo ROOT . '/my-cart' ?>" class="dropdown-item text-center fw-bold">
            See cart
          </a>

        </div>
      <?php endif; ?>
    </li>
    <!-- Notifications Dropdown Menu -->
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
    <?php if ($user): ?>
      <li class="nav-item">
        <a href="<?php echo ROOT . '/logout' ?>" class="nav-link" role="button">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </li>
    <?php endif ?>
  </ul>
</nav>
<!-- /.navbar -->