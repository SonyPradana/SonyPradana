<!DOCTYPE html>
<html lang="en">
<head>
  <?php include(APP_FULLPATH['component'] . 'meta/metatag.php') ?>
  <link rel="stylesheet" href="/vue/css/app.css">
</head>
<body class="antilaliased font-sans bg-gray-50 dark:bg-gray-800">

  <main id="app" class="md:container md:mx-auto sm:max-w-sm h-screen max-w-md px-4 py-2 grid grid-rows-dashbord grid-cols-dashbord gap-4">
    <navbar class="row-start-1 row-end-1 col-start-2 col-end-2"></navbar>

    <navigation class="row-start-1 row-end-3 col-start-1 col-end-1"></navigation>

    <div class="bg-gray-50 dark:bg-gray-800 row-start-2 row-end-2 col-start-2 col-end-2 m-10">
      <div class="py-4">
        <router-view></router-view>
      </div>
    </div>
  </main>

  <script src="/vue/app.js"></script>
</body>
</html>
