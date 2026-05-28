<?php
require_once __DIR__ . "/../config/constants.php";
require_once __DIR__ . "/layout/header.php";
?>


<title><?php echo SITE ?> | Thanks for your order!</title>
<link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/web.css">
<link rel="stylesheet" href="<?php echo ROOT ?>/assets/styles/index.css">
<script src="<?php echo ROOT . "/" ?>views/js/helper/utils.js" defer></script>
<script src="<?php echo ROOT . "/" ?>views/js/checkoutReturn.js" defer></script>
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
    <section class="content hidden" id="success">
      <p>
        We appreciate your business! A confirmation email will be sent to
        <span id="customer-email"></span>. If you have any questions, please
        email <a href="mailto:orders@example.com">orders@example.com</a>.
      </p>
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