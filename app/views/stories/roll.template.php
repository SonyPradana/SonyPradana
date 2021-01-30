<!DOCTYPE html>
<html lang="en">
<head>
  <?php include(APP_FULLPATH['component'] . 'meta/metatag.php') ?>
  <style>
    body {
      background-color: #121212;
      margin: 0;
    }

    .content {
      position: relative;
      overflow: hidden;
    }

    .content .scroll-indicator {
      position: absolute;
      top: 8px;
      left: 50%;
      transform: translateX(-50%);
      height: 16px;
      width: 320px;
      margin: 8px;
      display: grid;
      grid-template-columns: repeat(<?= $content->storiesCount ?>, 1fr);
      column-gap: 4px;
      z-index: 1;
    }

    .scroll-indicator > div {
      height: 4px;
      border-radius: 2px;
      background-color: #818181;
    }

    .scroll-indicator .active {
      background-color: #fff;
    }

    .stories-card {
      padding: 0 8px;
      display: flex;
      align-items: center;
      height: 100vh;
      overflow-x: hidden;
      transition: .2s;
    }

    .story-box {
      position: relative;
      margin: 0 4px;
      transition: .3s;
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
      width: 320px;
      max-width: 320px;
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

    /* btn */
    .btn:active:hover {
      background-color: #1e1e1e57;
    }
    .btn {
      position: absolute;
      width: 150px;
      height: 310px;
      top: 50%;
      transform: translateY(-50%);
      background-color: #0d0d0d00;
      border-radius: 310px;
      z-index: 2;
    }
    .btn-back {
      left: -75px;
    }
    .btn-forward {
      right: -75px;
    }

    @media screen and (max-width: 320px) {
      .content .scroll-indicator {
        left: 0;
        width: 95vw;
        transform: translateX(0);
      }
    }
  </style>
</style>
</head>
<body>
  <?php if ($content->exist) : ?>
    <div class="content">
      <div onclick="min(true)" class="btn btn-back"></div>
      <div onclick="plus(true)" class="btn btn-forward"></div>
      <div class="stories-card">

        <?php if ($content->stories): ?>
          <?php $index = -1; ?>
          <?php foreach ($content->stories as $story): ?>
            <?php $index += 1; ?>

        <div class="story-box <?= $content->fristItem ? 'active' : '' ?>" onclick="navClick(<?= $index ?>)">
          <div class="body">
            <img
              src="/data/img/stories/small/<?= $story['image_id'] ?>"
              data-src="/data/img/stories/original/<?= $story['image_id'] ?>"
            >
          </div>
          <div class="overlay"></div>
          <div class="footer">
            <p><?= $story['caption'] ?></p>
          </div>
        </div>

              <?php $content->fristItem = false ?>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($content->isOdd): ?>
        <div class="story-box">
          <div class="body">
            <div class="v-img"></div>
          </div>
        </div>
        <?php endif; ?>

      </div>
      <div class="scroll-indicator">
        <?php for ($i=0; $i < $content->storiesCount; $i++): ?>
        <div onclick="navClick(<?= $i ?>)"  <?= $i < 1 ? 'class="active"' : ''?>></div>
        <?php endfor; ?>
      </div>
    </div>
  <?php endif; ?>
</body>
<script>
  const slide = setInterval(() => {
    if (! plus()) {
      clearInterval(slide);
    }
  }, 5000);

  let curentPos = 0;
  const maxPos = <?= $content->storiesCount ?>;
  const getPosition = pos => -((pos * 320) + (pos * 8));

  const plus = (stopSlide = false) => {
    if (curentPos < maxPos - 1) {
      curentPos++;
      let setPos = `${getPosition(curentPos)}px`
      document.querySelector('.stories-card').style.marginLeft = setPos
      updateActivecard();
      if (stopSlide) {
        clearInterval(slide)
      }
      return true;
    }
    return false;
  }

  const min = (stopSlide = false) => {
    if (curentPos > 0) {
      curentPos--;
      let setPos = `${getPosition(curentPos)}px`;
      document.querySelector('.stories-card').style.marginLeft = setPos;
      updateActivecard();
      if (stopSlide) {
        clearInterval(slide)
      }
      return true;
    }
    return false;
  }

  const setPostion = (val = 1) => {
    val = val < 0 ? 0 : val;
    val = val > maxPos ? maxPos : val;

    curentPos = val;
    let setPos = `${getPosition(curentPos)}px`;
    document.querySelector('.stories-card').style.marginLeft = setPos;
    updateActivecard();

  }

  const updateActivecard = () => {
    const storyCard = document.querySelectorAll('.story-box')
    let i = 0;
    storyCard.forEach( e => {
      e.className = i == curentPos ? 'story-box active' : 'story-box';
      i++;
    })

    const indicatorbar = document.querySelectorAll('.scroll-indicator > div')
    let j = -1;
    indicatorbar.forEach( e => {
      e.className = j < curentPos ? 'active' : '';
      j++;
    })
  }


  // costume
  const navClick = (pos) => {
    setPostion(pos);
    // reset slide time
    clearInterval(slide);
  }

  document.onkeydown = e => {
    e = e || window.event;
    if (e.keyCode == 37) {
      min();
    }
    if (e.keyCode == 39) {
      plus();
    }
    if (e.keyCode == 36) {
      setPostion(0);
    }
    if (e.keyCode == 35) {
      setPostion(maxPos -1);
    }

    // reset slide time
    clearInterval(slide);
  }

  window.onload = () => {
    document.querySelectorAll("[data-src]").forEach(async function(el){
      let my_img = el.getAttribute("data-src");
      Promise.resolve( el.setAttribute("src", my_img) );
    });
  }


</script>
</html>
