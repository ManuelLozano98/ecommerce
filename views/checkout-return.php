<?php
require_once __DIR__ . "/../config/constants.php";
require_once __DIR__ . "/layout/header.php";
?>


<title><?php echo SITE ?> | Thanks for your order!</title>
<script src="<?php echo ROOT . "/" ?>views/js/checkoutReturn.js" defer></script>
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

<body class="hold-transition sidebar-mini">
  <div class="wrapper">
    <!-- Main content -->
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
    <?php require_once __DIR__ . '/layout/footer.php' ?>

    <!-- ./wrapper -->
  </div>

</body>

</html>