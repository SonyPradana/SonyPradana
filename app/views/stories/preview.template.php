<!DOCTYPE html>
<html lang="en">
<head>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>
  <style>
    body {
      background-color: #121212;
      margin: 0;
    }

    .stories-card {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow-x: hidden;
      transition: .2s;
    }

    .story-box {
      position: relative;
      margin: 0 4px;
    }
    .story-box:not(.active) {
      transform: scale(.85);
      transform-origin: center;
    }

    .story-box:not(.active):hover {
      transform: scale(.87);
      transition:cubic-bezier(0.6, -0.28, 0.735, 0.045);
    }

    .story-box:not(.active) .footer {
      display: none;
    }

    .body img, .v-img {
      width: 447px;
      width: 320px;
      height: 80vh;
      object-fit: cover;
    }

    .story-box:not(.active) .overlay {
      position: absolute;
      width: 100%;
      height: 80vh;
      top: 0;
      left: 0;
      background-color: #000000c4;
    }
    .story-box:not(.active) .overlay:hover {
      background-color: #000000a1;
    }

    .footer {
      position: absolute;
      bottom: 52px;
      transform: translateY(50px);
      background-color: #2d2d2dc2;
      width: 100%;
      height: 100px;
    }

    .footer p {
      color: #fff;
      text-align: center;
      font-size: 1.3rem;
      font-weight: 600;
      font-family: Arial, Helvetica, sans-serif;
      cursor:default;
    }

    @media screen and (max-width: 767px) {
      .body img {
        max-width: 767px;
        min-width: 447px;
      }
    }

    @media screen and (max-width: 447px) {
      .body img {
        max-width: 447px;
        min-width: 320px;
        max-height: 100vh;
      }
    }
    @media screen and (max-width: 320px) {
      .body img {
        min-width: 320px;
      }
    }
  </style>
</style>
</head>
<body>
  <?php if ($content->exist) : ?>
    <div class="content">
      <div class=""></div>
      <div class="stories-card">

        <?php if ($content->beforeExist): ?>
        <div class="story-box" onclick="navPage('<?= $content->storyID + 1 ?>')">
          <div class="body">
            <img src="/data/img/stories/original/<?= $content->imageBefore ?>" alt="">
          </div>
          <div class="overlay"></div>
          <div class="footer">
            <p><?= $content->captionBefore ?></p>
          </div>
        </div>
        <?php else: ?>
        <div class="story-box">
          <div class="body">
            <div class="v-img"></div>
          </div>
        </div>
        <?php endif; ?>

        <div class="story-box active">
          <div class="body">
            <img src="/data/img/stories/original/<?= $content->imageID ?>" alt="">
          </div>
          <div class="overlay"></div>
          <div class="footer">
            <p><?= $content->caption ?></p>
          </div>
        </div>

        <?php if ($content->afterExist): ?>
        <div class="story-box" onclick="navPage('<?= $content->storyID - 1 ?>')">
          <div class="body">
            <img src="/data/img/stories/original/<?= $content->imageAfter ?>" alt="">
          </div>
          <div class="overlay"></div>
          <div class="footer">
            <p><?= $content->captionAfter ?></p>
          </div>
        </div>
        <?php else: ?>
        <div class="story-box">
          <div class="body">
            <div class="v-img"></div>
          </div>
        </div>
        <?php endif; ?>

      </div>
      <div class="nav forward"></div>
    </div>
  <?php endif; ?>
</body>
<script>
  function navPage(storyID) {
    window.location.href = `/stories/view/${storyID}`;
  }
</script>
</html>
