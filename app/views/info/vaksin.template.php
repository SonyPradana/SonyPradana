<!DOCTYPE html>
<html lang="en">
<head>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>

  <link rel="stylesheet" href="/lib/css/ui/v1.1/full.style.css">
  <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
  <script src="/lib/js/index.min.js"></script>
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
          <h1 class="text-gray-900">Info Vaksin Jumlah dan Sasaran - Indonesia</h1>
          <div class="article breadcrumb">
            <div class="author">
              <img src="<?= $content->article['display_picture_small'] ?>" alt="@<?= $content->article['display_name'] ?>" srcset="">
              <div class="author-name"><a href="/Ourteam"><?= $content->article['display_name'] ?></a></div>
            </div>
            <div class="time">8 Maret 2021</div>
          </div>
        </div>

        <div id="media-app" class="media-article">
          <div class="vaksin-card">
            <div class="box-card sasaran">
              <div class="box-title">Sasaran Vaksin</div>
              <div class="box-body">
                <div class="card sasaran-lansia">
                  <div class="title">lansia</div>
                  <div class="data">
                    {{ lastest.sasaran_vaksinasi_lansia }}
                  </div>
                </div>
                <div class="card sasaran-petugas-publik">
                  <div class="title">petugas publik</div>
                  <div class="data">
                    {{ lastest.sasaran_vaksinasi_petugas_publik }}
                  </div>
                </div>
                <div class="card sasaran-sdmk">
                  <div class="title">petugas sdmk</div>
                  <div class="data">
                    {{ lastest.sasaran_vaksinasi_sdmk }}
                  </div>
                </div>
                <div class="card sasaran-total">
                  <div class="title">total</div>
                  <div class="data">
                    {{ lastest.total_sasaran_vaksinasi }}
                  </div>
                </div>
              </div>
            </div>

            <div class="box-card progress">
              <div class="box-title">Progres Vaksin</div>
              <div class="box-body">
                <!-- lansia -->
                <div class="card progress progress-lansia">
                  <div class="title">lasia</div>
                  <div class="data">
                    <div class="sub-data tahap-1">
                      <div class="title">tahap pertama</div>
                      <div class="data">
                        {{ lastest.tahapan_vaksinasi.lansia.sudah_vaksin1 }} +
                        {{ lastest.tahapan_vaksinasi.lansia.tertunda_vaksin1 }} =
                        {{ lastest.tahapan_vaksinasi.lansia.total_vaksinasi1 }}
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">tahap kedua</div>
                      <div class="data">
                        {{ lastest.tahapan_vaksinasi.lansia.sudah_vaksin2 }} +
                        {{ lastest.tahapan_vaksinasi.lansia.tertunda_vaksin2 }} =
                        {{ lastest.tahapan_vaksinasi.lansia.total_vaksinasi2 }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- petugas publik  -->
                <div class="card progress progress-petugas-publik">
                  <div class="title">petugas publik</div>
                  <div class="data">
                    <div class="sub-data tahap-1">
                      <div class="title">tahap pertama</div>
                      <div class="data">
                        {{ lastest.tahapan_vaksinasi.petugas_publik.sudah_vaksin1 }} +
                        {{ lastest.tahapan_vaksinasi.petugas_publik.tertunda_vaksin1 }} =
                        {{ lastest.tahapan_vaksinasi.petugas_publik.total_vaksinasi1 }}
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">tahap kedua</div>
                      <div class="data">
                        {{ lastest.tahapan_vaksinasi.petugas_publik.sudah_vaksin2 }} +
                        {{ lastest.tahapan_vaksinasi.petugas_publik.tertunda_vaksin2 }} =
                        {{ lastest.tahapan_vaksinasi.petugas_publik.total_vaksinasi2 }}
                      </div>
                    </div>
                  </div>
                </div>

                <!-- petugas sdm kesehatan -->
                <div class="card progress progress-sdm kesehatan">
                  <div class="title">sdm Kesehatan</div>
                  <div class="data">
                    <div class="sub-data tahap-1">
                      <div class="title">tahap pertama</div>
                      <div class="data">
                        {{ lastest.tahapan_vaksinasi.sdm_kesehatan.sudah_vaksin1 }} +
                        {{ lastest.tahapan_vaksinasi.sdm_kesehatan.tertunda_vaksin1 }} =
                        {{ lastest.tahapan_vaksinasi.sdm_kesehatan.total_vaksinasi1 }}
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">tahap kedua</div>
                      <div class="data">
                        {{ lastest.tahapan_vaksinasi.sdm_kesehatan.sudah_vaksin2 }} +
                        {{ lastest.tahapan_vaksinasi.sdm_kesehatan.tertunda_vaksin2 }} =
                        {{ lastest.tahapan_vaksinasi.sdm_kesehatan.total_vaksinasi2 }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="media note text-gray-500">
            <p>Terakhir di Perbarui {{ lastest.date }}</p>
            <p>Sumber:cekdiri.id/vaksinasi/</p>
          </div>
        </div>

        <div class="article body">
          <h2 class="text-gray-800">Sasaran vaksinasi wilayah puskesamas lerep</h2>
          <h3 class="text-gray-700">Jadwal vaksiasi lansia</h3>
          <table>
            <thead>
              <tr>
                <td>No</td>
                <td>Tanggal</td>
                <td>Sasaran</td>
                <td>Desa / Kelurahan</td>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>6 Maret 2021</td>
                <td>140</td>
                <td>Lerep dan Keji</td>
              </tr>
              <tr>
                <td>2</td>
                <td>8 Maret 2021</td>
                <td>140</td>
                <td>Bandarjo dan Branjang</td>
              </tr>
              <tr>
                <td>3</td>
                <td>9 Maret 2021</td>
                <td>140</td>
                <td>kalisidi dan Nyatnyono</td>
              </tr>
            </tbody>
          </table>

          <p>Jadwal vaksin yang lain akan di infokan di halam ini</p>
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
        lastest: {}
      }
    },
    mounted() {
      $json('https://cekdiri.id/vaksinasi/')
        .then(json => {
          let data = json.monitoring
          console.log(data.pop())
          this.lastest = data.pop()
        })
    },
  })
</script>
</html>
