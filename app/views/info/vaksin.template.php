<!DOCTYPE html>
<html lang="en">
<head>
  <?php

use Model\JadwalVaksin\JadwalVaksins;

include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>

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
      font-size: 1rem;
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
          <h1 class="text-gray-900">Info Vaksin Jumlah dan Sasaran</h1>
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
            <div class="box-card progress">
              <div class="box-title">Progres Vaksin - Nasional</div>
              <div class="box-body">
                <!-- lansia -->
                <div class="card progress progress-lansia">
                  <div class="title">Lansia</div>
                  <div class="data">
                    <div class="sub-data sasaran">
                      <div class="title">Sasaran</div>
                      <div class="data text">
                        {{ lastest.sasaran_vaksinasi_lansia }}
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">Tahap Pertama</div>
                      <div class="data text">
                        {{ lastest.tahapan_vaksinasi.lansia.sudah_vaksin1 }}
                        ({{ lastest.cakupan.lansia_vaksinasi1 }})
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">Tahap Kedua</div>
                      <div class="data text">
                        {{ lastest.tahapan_vaksinasi.lansia.sudah_vaksin2 }}
                        ({{ lastest.cakupan.lansia_vaksinasi2 }})
                      </div>
                    </div>
                  </div>
                </div>

                <!-- petugas publik  -->
                <div class="card progress progress-petugas-publik">
                  <div class="title">Petugas Publik</div>
                  <div class="data">
                    <div class="sub-data sasaran">
                      <div class="title">Sasaran</div>
                      <div class="data text">
                        {{ lastest.sasaran_vaksinasi_petugas_publik }}
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">Tahap Pertama</div>
                      <div class="data text">
                        {{ lastest.tahapan_vaksinasi.petugas_publik.sudah_vaksin1 }}
                        ({{ lastest.cakupan.petugas_publik_vaksinasi1 }})
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">Tahap Kedua</div>
                      <div class="data text">
                        {{ lastest.tahapan_vaksinasi.petugas_publik.sudah_vaksin2 }}
                        ({{ lastest.cakupan.petugas_publik_vaksinasi2 }})
                      </div>
                    </div>
                  </div>
                </div>

                <!-- petugas sdm kesehatan -->
                <div class="card progress progress-sdm-kesehatan">
                  <div class="title">SDM Kesehatan</div>
                  <div class="data">
                    <div class="sub-data sasaran">
                      <div class="title">Sasaran</div>
                      <div class="data text">
                        {{ lastest.sasaran_vaksinasi_sdmk }}
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">Tahap Pertama</div>
                      <div class="data text">
                        {{ lastest.tahapan_vaksinasi.sdm_kesehatan.sudah_vaksin1 }}
                        ({{ lastest.cakupan.sdm_kesehatan_vaksinasi1 }})
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">Tahap Kedua</div>
                      <div class="data text">
                        {{ lastest.tahapan_vaksinasi.sdm_kesehatan.sudah_vaksin2 }}
                        ({{ lastest.cakupan.sdm_kesehatan_vaksinasi2 }})
                      </div>
                    </div>
                  </div>
                </div>

                <!-- total sasaran -->
                <div class="card progress progress-total">
                  <div class="title">Total</div>
                  <div class="data">
                    <div class="sub-data sasaran">
                      <div class="title">Sasaran</div>
                      <div class="data text">
                        {{ lastest.total_sasaran_vaksinasi }}
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">Tahap Pertama</div>
                      <div class="data text">
                        {{ lastest.vaksinasi1 }}
                        ({{ lastest.cakupan.vaksinasi1 }})
                      </div>
                    </div>
                    <div class="sub-data tahap-1">
                      <div class="title">Tahap Kedua</div>
                      <div class="data text">
                        {{ lastest.vaksinasi2 }}
                        ({{ lastest.cakupan.vaksinasi2 }})
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
          <h2 class="text-gray-800">Sasaran Vaksinasi Wilayah Puskesamas Lerep</h2>
          <h3 class="text-gray-700">Jadwal vaksiasi lansia</h3>
          <table id="jadwal-lansia">
            <thead>
              <tr>
                <td>No</td>
                <td>Tanggal</td>
                <td>Sasaran</td>
                <td>Desa / Kelurahan</td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(event, index) in events" :key="event.id">
                <td v-text="index + 1"></td>
                <td v-text="event.tanggal"></td>
                <td v-text="event.jumlah"></td>
                <td v-text="event.desa"></td>
              </tr>
            </tbody>
          </table>

          <p>Jadwal vaksin yang lain akan di infokan di halam ini</p>
          <h3 class="text-gray-700">Jadwal vaksin kedua</h3>
          <p>Untuk mengetahui Jadwal vaksin kedua (setelah mendapat vaksin pertama), ada beberapa cara anatara lain;</p>
          <ol>
            <li>
              <p>
                Menghitung manual, dengan menjumlahkan 28 hari sejak tanggal vaksin pertama. Bila vaksin jatuh pada hari jumat atau minggu vaksin dapat diundur pada hari berikutnya.
                Tanggal vaksin pertama dapat dilihat pada kartu vaksin yang telah diperoleh sebelumnya.
              </p>
              <p>Contoh: pasien mendapat vakin pertama pada tanggal 1 Maret, maka pasien datang kembali pada tanggal 29 Maret</p>
            </li>
            <li>
              <p>
                Mengujungi situs resmi pemerintah <a href="https://pedulilindungi.id/" target="_blank" rel="noopener noreferrer">pedulilindungi.com</a>, kemudian masukan nama lengkap dan nik yang terdaftar. Secara otomatis akan muncul inforamsi vaksinasi Anda.
              </p>
              <img src="/data/img/article/contoh-pedulilindungi.png" alt="contoh pedulilindungi" width="470px">

            </li>
            <li>
              <p>
                Menghubungi petugas vaksinasi. Anda dapat bertanya secara langsung kepada kami di puskesmas Lerep atau menghubungi <a href="tel:+64">Petugas vaksin</a> atau bidan desa setempat.
              </p>
            </li>
          </ol>
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
        lastest: {
          "cakupan": {
            "lansia_vaksinasi1": "0%",
            "lansia_vaksinasi2": "0%",
            "petugas_publik_vaksinasi1": "0%",
            "petugas_publik_vaksinasi2": "0%",
            "sdm_kesehatan_vaksinasi1": "0%",
            "sdm_kesehatan_vaksinasi2": "0%",
            "vaksinasi1": "0%",
            "vaksinasi2": "0%"
          },
          "date": "2021-04-07",
          "sasaran_vaksinasi_lansia": 0,
          "sasaran_vaksinasi_petugas_publik": 0,
          "sasaran_vaksinasi_sdmk": 0,
          "tahapan_vaksinasi": {
            "lansia": {
              "sudah_vaksin1": 0,
              "sudah_vaksin2": 0,
              "tertunda_vaksin1": 0,
              "tertunda_vaksin2": 0,
              "total_vaksinasi1": 0,
              "total_vaksinasi2": 0
            },
            "petugas_publik": {
              "sudah_vaksin1": 0,
              "sudah_vaksin2": 0,
              "tertunda_vaksin1": 0,
              "tertunda_vaksin2": 0,
              "total_vaksinasi1": 0,
              "total_vaksinasi2": 0
            },
            "sdm_kesehatan": {
              "sudah_vaksin1": 0,
              "sudah_vaksin2": 0,
              "tertunda_vaksin1": 0,
              "tertunda_vaksin2": 0,
              "total_vaksinasi1": 0,
              "total_vaksinasi2": 0
            }
          },
          "total_sasaran_vaksinasi": 0,
          "vaksinasi1": 0,
          "vaksinasi2": 0
        }
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

  new Vue({
    el: '#jadwal-lansia',
    data() {
      return {
        events: {}
      }
    },
    mounted() {
      $json('/api/ver1.1/JadwalVaksin/lansia.json')
        .then(json => {
          this.events = json.data
        })
    },
  })
</script>
</html>
