<?php
require_once __DIR__ . "/../config/constants.php";
require_once __DIR__ . "/layout/header.php";
?>


<title><?php echo SITE ?> | Thanks for your order!</title>
<link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
<link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/index.css">
<script src="<?php echo ROOT . "/" ?>views/js/helper/utils.js" defer></script>
<!-- Scripts -->
<script src="<?php echo ADMINLTE ?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo ADMINLTE ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo ADMINLTE ?>dist/js/adminlte.min.js"></script>
<?php require_once __DIR__ . "/layout/endheader.php"; ?>

<style>
  .error-page {
    padding-top: 30px;
    margin: auto;
  }

  .error-content,
  h2.headline,
  footer {
    text-align: center;
  }

  h2.headline {
    font-size: 100px;
  }

  .content {
    height: 90vh;
  }
</style>
</head>

<body class="sidebar-collapse">
  <?php
  require_once __DIR__ . "/layout/navbar.php";
  require_once __DIR__ . '/../config/constants.php';
  $user = $_SESSION["user"] ?? NULL;
  ?>
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo ROOT ?>" class="brand-link">
      <img src="<?php echo ADMINLTE ?>dist/img/AdminLTELogo.png" alt="AdminLTE Logo"
        class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light"><?php echo SITE ?></span>
    </a>
  </aside>
  <!-- Main content -->
  <div class="content-wrapper">
    <section class="content">
      <?php if ($session->payment_status === 'paid'): ?>
        <div class="success">
          <h2>Payment successful</h2>
          <p>
            We appreciate your business! A confirmation email will be sent to
            <span id="customer-email"><?= $customer_email ?></span>. If you have any questions, please
            email <a href="mailto:orders@example.com">orders@example.com</a>.
          </p>
        </div>

      <?php elseif ($session->status === 'expired'): ?>
        <div class="error">
          <h2>Payment session expired</h2>
          <p>Your checkout session has expired before completion.</p>
          <p>Please try again to complete your purchase.</p>
        </div>

      <?php elseif ($session->payment_status === 'unpaid'): ?>

        <div class="warning">
          <h2>Payment not completed</h2>
          <p>Your payment has not been completed yet.</p>
          <p>You can try again to finish your order.</p>

        </div>


      <?php else: ?>

        <div class="info">
          <h2>Payment status unknown</h2>
          <p>Please contact support if you believe this is an error.</p>
        </div>

      <?php endif; ?>

      <div>
        <p>
          <a href="<?php echo ROOT ?>">Return to main page</a>
        </p>
      </div>
      <!-- /.error-content -->
    </section>
    <!-- /.content -->
  </div>

  <?php require_once __DIR__ . '/layout/footer.php' ?>

  <!-- ./wrapper -->
  </div>

</body>

</html>