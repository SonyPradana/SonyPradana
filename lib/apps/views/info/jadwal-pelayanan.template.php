<?php 
use Simpus\Auth\User;
use Simpus\Services\JadwalKia;

    $author = new User("angger");
    $imun   = new JadwalKia(date('m'), date('Y'));

    $portal = [
        "auth"    => $this->getMiddleware()['auth'],
        "meta"     => [
            "title"         => "Jadwal Pelayanan di Poli KIA - Simpus Lerep",
            "discription"   => "Jadwal pelayanan imunisasi anak di Poli KIA",
            "keywords"      => "simpus lerep, puskesmas lerep,jadwal imunisasi, imunusasi, kia anak, jadwal, BCG, Campak, Rubella (MR), Hib, HB, DPT, IPV"
        ],
        "header"   => [
            "active_menu"   => 'home',
            "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
        ],
        "contents" => [
            "article"    => [
                "display_name"          => $author->getDisplayName(),
                "display_picture_small" => $author->getSmallDisplayPicture()
            ],
            "raw_data"          => $imun->getData(),
            "avilable_month"    => $imun->getAvilabeMonth()
        ]
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>
    <meta name="robots" content="all,index,follow">
    
    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <link rel="stylesheet" href="/lib/css/ui/v1.1/cards.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
    <script src="/lib/js/vendor/vue/vue.min.js"></script>
    <style>
        /* costume main container */
        .container.width-view{
            display: grid;
            grid-template-columns: 1fr 300px;
        }
        main{
            overflow-x: hidden;
        }
        /* Templatebox container */
        .cards-box .box-container{
            overflow-x: auto;
            display: grid;
            grid-template-columns: minmax(300px, 320px) 16px minmax(300px, 320px);
        }
        
        .header-article{margin-bottom: 24px}
        .header-article h1{
            font-size: 2.3rem;
            font-weight: 700;
        }
        .header-article .article.breadcrumb{
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px; grid-gap: 8px;
        }
        .header-article .article.breadcrumb > div{ font-size: 1rem; color: #9aa6ad}

        /* .media-article{margin-right: 20px;} */
        .media-article{ padding: 0 16px;}
        .media.note p{color: #a2a2a2; margin: 0}
        .table-boxs{
            display: flex;
            justify-content: center;
            overflow-x: auto;
        }
        table.info-covid{max-width: 500px; min-width: 400px;}
        .article.body{margin: 16px 0;}
        .article.body h2{
            margin: 8px;
            text-align: center;
        }

        .gap{
            width: 16px; height: 16px;
            min-height: 16px; min-width: 16px;
        }
        @media screen and (max-width: 767px) {
            .container.width-view{grid-template-columns: 1fr}
            .table-boxs{justify-content: unset}
                        
            .cards-box .box-container{
                grid-template-columns: 1fr;
                grid-template-rows: auto 16px auto;
            }
        }
    </style>
</head>
<body>
    <header>
        <?php include(BASEURL . '/lib/components/header/header.php'); ?>
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
                            <img src="<?= $author->getSmallDisplayPicture() ?>" alt="@<?= $author->getDisplayName() ?>" srcset="">    
                            <div class="author-name"><a href="/Ourteam"><?= $author->getDisplayName() ?></a></div>
                        </div>
                        <div class="time">18 Juni 2020</div>
                    </div>
                </div>
                <div class="media-article">
                    <div class="cards-box blue">
                        <div class="box-title">Jadwal Bulan {{ raw.bulan }}</div>
                        <div class="box-container">
                            <div class="card event neum-blue neum-light neum-concave radius-small" id="jumat-pertama">
                                <div class="card-time">
                                    <div class="mount">{{ raw.jadwal[0].split(' ')[1] }}</div>
                                    <div class="day">{{ raw.jadwal[0].split(' ')[0] }}</div>
                                </div>
                                <div class="gab"></div>
                                <div class="card-content">
                                    <div class="title">Imunisasi
                                    </div>
                                    <div class="description">{{ raw['jumat pertama'].join(', ') }}</div>
                                </div>
                            </div>
                            <div class="gab"></div>
                            <div class="card event neum-blue neum-light neum-concave radius-small" id="jumat-ketiga">
                                <div class="card-time">
                                    <div class="mount">{{ raw.jadwal[2].split(' ')[1] }}</div>
                                    <div class="day">{{ raw.jadwal[2].split(' ')[0] }}</div>
                                </div>
                                <div class="gab"></div>
                                <div class="card-content">
                                    <div class="title">Imunisasi
                                    </div>
                                    <div class="description">{{ raw['jumat ketiga'].join(', ') }}</div>
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
                        <select name="pilih-bulan" id="input-pilih-bulan" v-on:change="onChange($event)">
                            <option hidden selected>Pilih Bulan</option>
                            <option v-for="date in month" v-bind:value="date" :key="date">{{ months[ Number( date ) - 1 ] }}</option>
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
                                    <td v-for="(jadwal, index) in raw.jadwal" :key="jadwal">Jumat {{ index + 1}} ~ {{ jadwal }}</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(tanggal, namaVaksin, index) in raw.data">
                                    <td>{{ index + 1}}</td>
                                    <td>{{ namaVaksin }}</td>
                                    <td v-for="jadwal in raw.jadwal" :key="jadwal">
                                        {{ tanggal.includes( jadwal ) ? 'Ya' : 'Tidak' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>  

            </article>
        </main>
        <aside class="right-side">

        </aside>
    </div>
    
    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>

    <!-- hidden -->
    <div id="modal">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
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
            raw     : <?= json_encode( $portal['contents']['raw_data'] ) ?>,
            month   : <?= json_encode( $portal['contents']['avilable_month'] ) ?>,
            months  : [ 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'Desember' ]
        },
        methods: {
            onChange(event) {
                this.first = false;
                $json(`/api/ver1.0/Jadwal-Pelayanan/Imunisasi.json?month=${event.target.value}`)
                    .then( json => this.raw = json )
            }
        }
    });
</script>
</html>
