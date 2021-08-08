<!DOCTYPE html>
<html lang="en">
<head>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>

  <link rel="stylesheet" href="/lib/css/ui/v1.1/full.style.css">
  <script src="/lib/js/index.min.js"></script>
  <script src="/lib/js/bundles/keepalive.min.js"></script>
  <script src="/lib/js/vendor/vue/vue.min.js"></script>
  <script src="/lib/js/vendor/pusher/pusher.min.js"></script>
  <style>
    .container.width-view {
      display: grid;
      grid-template-columns: 1fr 300px;
      gap: 12px;
    }
    main {
      overflow-x: auto;
    }

    @media screen and (max-width: 767px) {
      .container.width-view {
        grid-template-columns: 1fr
      }
    }
    /* style */
    .article-media > div {
      margin: 10px 0;
    }
    .article-media {
      border: 1px blue solid;
      border-radius:4px;
    }
    .article-media .meta-info .title {
      font-size: 1.5rem;
      line-height: 2rem;
      font-weight: 700;
    }
    .article-media .cards {
      display: grid;
      gap: 10px;
    }
    .article-media .cards .card {
      border: 1px blue solid;
      border-radius:4px;
      max-width: 270px;
      padding: 8px;
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
    .card .kategory {
      grid-column: 1 / 1;
      grid-row: 1 / 3;
      place-self: center;

      font-size: 1.125rem;
      line-height: 1.75rem;
      text-transform: capitalize;
    }
    .card .kuota {
      font-size: 1.125rem;
      line-height: 1.75rem;
      font-weight: 600;
    }

    .card .tersedia {
      color: rgba(156, 163, 175, 1);
    }

    .media.note {
      color: rgba(156, 163, 175, 1);
    }
  </style>
</head>
<body>
  <header>
    <?php include(BASEURL . '/resources/components/header/header.php'); ?>
  </header>

  <div class="container width-view">
    <main>
      <div class="coit breadcrumb">
        <ul class="crumb">
          <li><a href="/">Home</a></li>
          <li>Kuota Vaksin</li>
        </ul>
      </div>

      <article id="app">
        <!-- header article -->
        <div class="article-header">
          <h1>Informasi Kuota Vaksin Puskesmas Lerep</h1>
          <div class="article breadcrumb">
            <div class="author">
              <img src="<?= $content->article['display_picture_small'] ?>" alt="@<?= $content->article['display_name'] ?>">
              <div class="author-name"><a href="/Ourteam"><?= $content->article['display_name'] ?></a></div>
            </div>
            <div class="time"><?= $content->article['article_create'] ?></div>
          </div>
        </div>

        <!-- media article -->
        <div class="article-media">
          <div class="meta-info">
            <div class="title" v-if="raw == null || raw == []">Hari ini tidak ada vaksin</div>
            <div class="title" v-else>Kuota vaksin hari ini</div>
            <div class="sub-title" v-if="raw != null" v-text="raw.date"></div>
          </div>
          <div class="cards" v-if="raw != null">
            <div class="card" v-for="data in raw.data">
              <div class="kategory" v-text="data.kategory"></div>
              <div class="kuota" v-text="data.kuota"></div>
              <div class="tersedia" v-text="data.kuota - data.dipakai + ` (tersedia)`"></div>
            </div>
          </div>
        </div>

        <div class="media note">
          <p>Bukan data real, data dapat berubah sewaktu-waktu</p>
        </div>

        <!-- article body -->
        <div class="article-body">

        </div>
      </article>

    </main>
    <aside class="right-side">
      <?php include(APP_FULLPATH['component'] . 'widget/stories.html') ?>
      <?php include(APP_FULLPATH['component'] . 'widget/trivia.html') ?>
    </aside>
  </div>

  <div class="gotop" onclick="gTop()"></div>
  <footer>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/footer/footer.html') ?>
  </footer>

  <!-- hidden -->
  <div id="modal">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/control/modal.html') ?>
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
      window.location.href = "/login?url=<?= $_SERVER['REQUEST_URI'] ?>&logout=true"
    },
    () => {
      // close fuction : just logout
      window.location.href = "/logout?url=<?= $_SERVER['REQUEST_URI'] ?>"
    }
  );

  // pusher
  Pusher.logToConsole = false;

  let pusher = new Pusher('6c9ed3de96e572726af9', {
    cluster: 'ap1'
  });

  let channel = pusher.subscribe('info');
  channel.bind('antrian-vaksin', function (respone) {
    app.raw = respone.data
  });

  // apps
  const app = new Vue({
    el: '#app',
    data: {
      raw: [],
    },
    mounted() {
      $json('/api/v1.0/KuotaVaksin/getKuota.json')
        .then(json => {
          if (json.code === 200) {
            this.raw = json.data
          }
        })
    },
  })
</script>
</html>
