<!DOCTYPE html>
<html lang="en">
<head>
  <?php include(component_path(true, 'meta/metatag.php')) ?>

  <link rel="stylesheet" href="/lib/css/ui/v1.1/full.style.css">
  <script src="/lib/js/index.min.js"></script>
  <script src="/lib/js/bundles/message.js"></script>
  <script src="/lib/js/bundles/keepalive.min.js"></script>
  <script src="/lib/js/vendor/vue/vue.min.js"></script>
  <style>
    .container.width-view {
      display: grid;
      grid-template-columns: 1fr 300px;
    }
    main {
      overflow-x: auto;
    }

    @media screen and (max-width: 767px) {
      .container.width-view {
        grid-template-columns: 1fr
      }
    }
  </style>
</head>
<body>
  <header>
    <?php include(component_path(true, 'header/header.php')); ?>
  </header>

  <div class="container width-view">
    <main></main>
    <aside class="right-side"></aside>
  </div>

  <div class="gotop" onclick="gTop()"></div>
  <footer>
    <?php include(component_path(true,  'footer/footer.html')) ?>
  </footer>

  <!-- hidden -->
  <div id="modal">
    <?php include(component_path(true, 'control/modal.html')) ?>
</div>
</body>
<script src="/lib/js/index.end.js"></script>
<script>
  // sticky header
  window.onscroll = function(){
    stickyHeader('.container', '82px', '32px')
  }

    // keep alive
  keepalive(
    () => {
      // ok function : redirect logout and then redirect to login page to accses this page
      window.location.href = "/login?url=<?= request()->getUrl() ?>&logout=true"
    },
    () => {
      // close fuction : just logout
      window.location.href = "/logout?url=<?= request()->getUrl() ?>"
    }
  );
</script>
</html>
