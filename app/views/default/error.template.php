<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $portal['meta']['title'] ?></title>  
  <meta name="description" content="<?= $portal['meta']['discription'] ?>">
  <meta name="keywords" content="<?= $portal['meta']['keywords'] ?>">
  <style>
    h1, h2, p {
      margin: 0;
      padding: 0;
      font-family: Arial, Helvetica, sans-serif;
    }
    html, body {
      margin: 0;
      padding: 0;
      background-color: #d9d9d9;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
    }
    .container {
      background-color: #fff;
      min-width: 100px;
      max-width: 400px;
      -webkit-box-shadow: 0 10px 6px -6px #ccc;
      -moz-box-shadow: 0 10px 6px -6px #ccc;
      box-shadow: 0 10px 6px -6px #ccc;
    }
    .boxs {
      display: flex;
      margin: 8px;
      gap: 12px;
    }
    .box-one {
      margin: 4px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .box-one h1 {
      font-size: 60px;      
      letter-spacing: .055em;
      color: #f42f2f;
    }
    .box-two {
      display: flex;
      flex-direction: column;
      margin: 4px;
      gap: 2px;
    }
    .box-two h2 {
      font-size: 24px;
      font-weight: 500;
      color: #3e3e3e;
    }
    .box-two p {
      color: #757474;
    }
    .box-two .box-links {
      display: inline-block;
    }
    .box-links a {
      margin-right: 4px;
    }
    @media screen and (max-width: 347px) {
      .container {
        max-width: 300px;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="boxs">
      <div class="box-one">
        <h1><?= $error->code ?></h1>
      </div>
      <div class="box-two">
        <h2><?= $error->message ?></h2>
        <?php if (isset($error->info)): ?>
          <p><?= $error->info ?></p>
        <?php endif; ?>
        <div class="box-links">
          <?php foreach($error->links as $link): ?>
            <a href="<?= $link[1] ?>"><?= $link[0] ?></a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
