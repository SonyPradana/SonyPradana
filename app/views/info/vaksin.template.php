<!DOCTYPE html>
<html lang="en">
<head>
  <?php
include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>

  <link rel="stylesheet" href="/lib/css/ui/v1.1/full.style.css">
  <script src="/lib/js/index.min.js"></script>
  <script src="/lib/js/bundles/keepalive.min.js"></script>
  <script src="/lib/js/vendor/vue/vue.min.js"></script>

  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "datePublished": "2021-03-08T08:00:00+08:00",
      "dateModified": "2021-08-04T09:20:00+08:00"
    }
  </script>

  <style>
    .container.width-view {
      display: grid;
      grid-template-columns: 1fr 300px;
      gap: 12px;
    }
    main {
      overflow-x: auto;
    }

    /* media style */
    .box-card .box-title {
      color: rgb(55, 65, 81);
      font-size: 1.25rem;
      font-weight: 700;
      line-height: 2.5rem;
    }

    .box-card  .box-body {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 12px;
    }

    .box-body .card {
      border-radius: 12px;
      border: solid 1px aqua;
      padding: 8px;
    }

    .box-body .card .title {
      font-size: 1rem;
      line-height: 2rem;
      font-weight: 600;
    }

    .box-body .card .data .title {
      color: rgb(75, 85, 99);
    }

    .box-body .card .data.text {
      font-size: 1.25rem;
      line-height: 2.5rem;
      font-weight: 700;
      color: #121FCF;
    }

    /* article */
    .article.body p {
      font-size: 1.25rem;
      line-height: 2rem;
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
    <?php include(BASEURL . '/resources/components/header/header.php'); ?>
  </header>

  <div class="container width-view">
    <main>

      <div class="coit breadcrumb">
        <ul class="crumb">
          <li><a href="/">Home</a></li>
          <li>Info</li>
          <li>Vaksin</li>
        </ul>
      </div>

      <article>
        <div class="article-header">
          <h1 class="text-gray-900"><?= $portal['meta']['title'] ?></h1>
          <div class="article breadcrumb">
            <div class="author">
              <img src="<?= $content->article['display_picture_small'] ?>" alt="@<?= $content->article['display_name'] ?>" srcset="">
              <div class="author-name"><a href="/Ourteam"><?= $content->article['display_name'] ?></a></div>
            </div>
            <div class="time">Posted 08 Maret 2021</div>
          </div>
        </div>

        <div id="media-app" class="media-article">
          <div class="vaksin-card">
            <div class="box-card progress">
              <div class="box-title">Progres Vaksin - Puskesmas Lerep (sumber external)</div>
              <div class="box-body">

                <!-- loop data -->
                <div class="card progress progress-lansia" v-for="vaksin in data_vaksin">
                  <div class="title">{{ vaksin.kategory }}</div>
                  <div class="data">
                    <div class="sub-data detail">
                      <div class="title">Tanggal</div>
                      <div class="data text">
                        {{ vaksin.date }}
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">Tahap Pertama</div>
                      <div class="data text">
                        {{ vaksin.vaksin_1 }}
                      </div>
                    </div>
                    <div class="sub-data tahap-2">
                      <div class="title">Tahap Kedua</div>
                      <div class="data text">
                      {{ vaksin.vaksin_2 }}
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>

          <div class="media note text-gray-500">
            <p>Sumber data: https://kipi.covid19.go.id</p>
            <p>Data terupdate otomatis</p>
          </div>
        </div>

        <div class="article body">
          <h2>Update jadwal vaksin</h2>
          <p>Untuk mengetahui update terbaru kuota vaksin dapat dilihat pada chanel telegram di <a href="https://t.me/vaksinpuskesmaslerep">t.me/vaksinpuskesmaslerep</a>, atau di halaman khusus kuota vaksin (halaman percobaan) <a href="/vaksinasi" rel=”follow”>simpuslerep.com/vaskiniasai.</a></p>
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
  window.onscroll = function() {
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

  // for this page
  new Vue({
    el: '#media-app',
    data() {
      return {
        data_vaksin: [
          // kategory vaksin
          {
            "kategory": "--",
            "date": "--",
            "total_vaksin": 0,
            "vaksin_1": 0,
            "vaksin_2": 0,
            "total_tunda": 0,
            "tunda_1": 0,
            "tunda_2": 0,
          },
        ],
        data_blank : {
          "kategory": "--",
          "date": "--",
          "total_vaksin": 0,
          "vaksin_1": 0,
          "vaksin_2": 0,
          "total_tunda": 0,
          "tunda_1": 0,
          "tunda_2": 0,
        }
      }
    },

    mounted() {
      $json('https://kipi.covid19.go.id/api/get-faskes-vaksinasi?skip=0&city=KAB.%20SEMARANG')
        .then(json => {
          if (json.success) {
            json.data.forEach(element => {
              // id lerep 10638
              if (element.id == 10638) {
                let new_data = []
                let total_vaksin = this.data_blank
                element.detail.forEach(data => {
                  if (data.batch == "TAHAP 3") {
                    return
                  }

                  new_data.push({
                    "kategory": data.batch,
                    "date": data.tanggal,
                    "total_vaksin": data.divaksin,
                    "vaksin_1": data.divaksin_1,
                    "vaksin_2": data.divaksin_2,
                    "total_tunda": data.pending_vaksin,
                    "tunda_1": data.pending_vaksin_1,
                    "tunda_2": data.pending_vaksin_2,
                  })

                  total_vaksin.kategory = "TOTAL"
                  total_vaksin.total_vaksin += data.divaksin
                  total_vaksin.vaksin_1 += data.divaksin_1
                  total_vaksin.vaksin_2 += data.divaksin_2
                  total_vaksin.total_tunda += data.total_tunda
                  total_vaksin.tunda_1 += data.pending_vaksin_1
                  total_vaksin.tunda_2 += data.pending_vaksin_2
                })
                // push to data vaksin
                if (new_data.length > 0) {
                  this.data_vaksin = new_data
                  this.data_vaksin.push(total_vaksin)
                }
              }
            });
          }
          this.data_vaksin
        })
    },
  })
</script>
</html>
