<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>
    <meta name="robots" content="all,index,follow">

    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <link rel="stylesheet" href="/lib/css/pages/v1.1/jadwal-kia.css">
    <link rel="stylesheet" href="/lib/css/ui/v1.1/full.style.css">
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
                    <li>Jadwal Pelayanan</li>
                </ul>
            </div>
            <article id="app">
                <div class="header-article">
                    <H1>Jadwal Pelayanan Poli KIA Anak (Imunisasi) Setiap Hari Jumat</H1>
                    <div class="article breadcrumb">
                        <div class="author">
                            <img src="<?= $content->article['display_picture_small'] ?>" alt="@<?= $content->article['display_name'] ?>" srcset="">
                            <div class="author-name"><a href="/Ourteam"><?= $content->article['display_name'] ?></a></div>
                        </div>
                        <div class="time">18 Juni 2020</div>
                    </div>
                </div>
                <div class="media-article">
                    <div class="cards-box blue">
                        <div class="box-title" v-text="`Jadwal Bulan ${raw.bulan}`">Jadwal Bulan </div>
                        <div class="box-container">
                            <div class="card event neum-blue neum-light neum-concave radius-small" id="jumat-pertama">
                                <div class="card-time">
                                    <div class="mount" v-text="raw.jadwal[0].split(' ')[1]"></div>
                                    <div class="day" v-text="raw.jadwal[0].split(' ')[0]"></div>
                                </div>
                                <div class="gab"></div>
                                <div class="card-content">
                                    <div class="title">Imunisasi
                                    </div>
                                    <div class="description" v-text="raw['jumat pertama'].join(', ')"></div>
                                </div>
                            </div>
                            <div class="gab"></div>
                            <div class="card event neum-blue neum-light neum-concave radius-small" id="jumat-ketiga">
                                <div class="card-time">
                                    <div class="mount" v-text="raw.jadwal[2].split(' ')[1]"></div>
                                    <div class="day" v-text="raw.jadwal[2].split(' ')[0]"></div>
                                </div>
                                <div class="gab"></div>
                                <div class="card-content">
                                    <div class="title">Imunisasi
                                    </div>
                                    <div class="description" v-text="raw['jumat ketiga'].join(', ')"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="media note">
                        <p>Jadwal Imunisasi Anak </p>
                        <p>Sumber: Puskesmas Lerep</p>
                    </div>
                </div>
                <div class="article body">
                    <div class="form-box">
                        <label for="input-pilih-bulan">Lihat Imunisasi Bulan Lainnya: </label>
                        <select name="pilih-bulan" id="input-pilih-bulan"
                          v-on:change="onChange($event)">
                            <option hidden selected>Pilih Bulan</option>
                            <option
                              v-for="date in month"
                              v-bind:value="`${date.date_mont} ${date.date_year}`"
                              :key="date.id"
                              v-text="`${date.date_mont_string} ${date.date_year}`">
                            </option>
                        </select>
                    </div>
                    <h2>Jadwal Pelayanan</h2>
                    <div class="table-boxs">
                        <table class="jadwal-imunisasi">
                            <thead>
                                <tr>
                                    <td>No</td>
                                    <td>Jenis Vaksin</td>
                                    <!-- fleksible header -->
                                    <td v-for="(jadwal, index) in raw.jadwal" :key="jadwal" v-text="`Jumat ${index + 1} ~ ${jadwal}`"></td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(tanggal, namaVaksin, index) in raw.data">
                                    <td v-text="index + 1"></td>
                                    <td v-text="namaVaksin"></td>
                                    <td v-for="jadwal in raw.jadwal" :key="jadwal" v-text="tanggal.includes( jadwal ) ? 'Ya' : 'Tidak' "></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </article>
        </main>
        <aside class="right-side">
          <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/widget/stories.html') ?>
          <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/widget/trivia.html') ?>
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

    let table = new Vue({
        el: '#app',
        data: {
          first   : true,
          raw     : <?= json_encode( $content->raw_data ) ?>,
          month   : <?= json_encode( $content->avilable_month ) ?>
        },
        methods: {
          onChange(event) {
            const dates = event.target.value;
            const date  = dates.split(' ');
            this.first = false;
            $json(`/api/ver1.0/Jadwal-Pelayanan/Imunisasi.json?month=${date[0]}&year=${date[1]}`)
            .then( json => {
              if (json.status = 'oke') {
                this.raw = json.data;
              }
            })
          }
        }
    });
</script>
</html>
