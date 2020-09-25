<?php
    require_once BASEURL . '/lib/apps/services/CovidKabSemarangService.php';
    use Simpus\Auth\User;

    $data_covid = new CovidKabSemarangService();

    $track_record   = $data_covid->track_record(["toString" => true])['data'];
    $data_record    = $data_covid->tracker(['range_waktu' => $track_record]);
    krsort($data_record);
    // data: konirmasi covid
    $date_record    = json_encode( array_values(array_column($data_record, "time")) );
    $posi_record    = json_encode( array_values(array_column($data_record, "kasus_posi")) );
    $meni_record    = json_encode( array_values(array_column($data_record, "kasus_meni")) );
    // data: suspek covid
    $suspek                = json_encode( array_values(array_column($data_record, "suspek")) );
    $suspek_disc_record    = json_encode( array_values(array_column($data_record, "suspek_discharded")) );
    $suspek_meni_record    = json_encode( array_values(array_column($data_record, "suspek_meninggal")) );

    $author = new User("angger");
    $portal = [
        "auth"    => $this->getMiddleware()['auth'],
        "meta"     => [
            "title"         => "Info Covid 19 Ungaran Barat",
            "discription"   => "Data Pasien Dalam Pengawasan dan Positif di Wilayah Kecamtan Ungaran Barat",
            "keywords"      => "simpus lerep, info covid, kawal covid, covid ungaran, covid branjang, wilyah ungran, Suspek, Discharded, Meninggal, Symptomatik, Asymptomatik, Sembuh, Meninggal, Terkomfirmasi"
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
            "last_index"                => 1,
            "date_record"               => $date_record,
            "kasus_posi"                => $posi_record,
            "kasus_meni"                => $meni_record,
            "suspek"                    => $suspek,
            "suspek_disc"               => $suspek_disc_record,
            "suspek_meni"               => $suspek_meni_record,
        ]
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <link rel="stylesheet" href="/lib/css/ui/v1.1/cards.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <script src="/lib/js/vendor/vue/vue.min.js"></script>
    <script src="/lib/js/vendor/chart/Chart.min.js"></script>
    <style>
        /* costume main container */
        .container.width-view{
            display: grid;
            grid-template-columns: 1fr 300px;
        }
        main{
            overflow-x: hidden;
        }

        /* prototipe article - tamplate */
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
        .box.cards{
            overflow-x: visible;
            display: flex;
            flex-direction: row;
            justify-content: center;
            margin-bottom: 12px;
        }
        .box.cards .gap-space{
            min-width: 16px;
        }
        .media.note p{color: #a2a2a2; margin: 0}
        .table-boxs{
            display: flex;
            justify-content: center;
            overflow-x: auto;
            margin-bottom: 32px;
        }
        table.info-covid{max-width: 500px; min-width: 400px;}
        .article.body{margin: 16px 0;}
        .article.body ul, ol, p{font-size: 20px;}
        .article.body ul { list-style: disc; }
        .article.body h2, .article.body h3,.article.body p{margin-bottom: 12px;}
        article .info {
            background-color: #ace1ff;
            padding: 8px 4px;
            border-left: solid 8px #2aaffb;
        }

        /* tablet vie view */
        @media screen and (max-width: 767px) {
            .container.width-view{grid-template-columns: 1fr}
            .box.cards{justify-content: unset}
            .table-boxs{justify-content: unset}
        }
        @media screen and (max-width: 1000px) {
            .box.cards{
                overflow: auto;
            }
        }
        
        /* hai youtube */
    </style>
</head>
<body>
    <header>
        <?php include(BASEURL . '/lib/components/header/header.php'); ?>
    </header>
    <div id="modal">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    </div>
    
    <div class="container width-view">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Info</li>
                    <li>Covid Kabupaten Semarang</li>
                </ul>
            </div>
            <article>
                <div class="header-article">
                    <H1>Info Covid Kabupaten Semarang (Kec Ungaran Barat)</H1>
                    <div class="article breadcrumb">
                        <div class="author">
                            <img src="<?= $portal['contents']['article']['display_picture_small'] ?>" alt="@<?= $portal['contents']['article']['display_name'] ?>" srcset="">    
                            <div class="author-name"><a href="/Ourteam"><?= $portal['contents']['article']['display_name'] ?></a></div>
                        </div>
                        <div class="time">11 April 2020</div>
                    </div>
                </div>
                <div class="media-article">
                    <div id="covid-card" class="box cards">
                        <div class="card covid-card grad-blue" id="card-positif" data-tooltips="Pasien Positif">
                            <div class="card title">Pasien Positif</div>
                            <div class="card content">{{ dirawat }}</div>
                            <div class="card note">Orang</div>
                        </div>
                        <div class="gap-space"></div>
                        <div class="card covid-card  grad-yellowtored" id="card-isolasi" data-tooltips="Pasien Isolasi">
                            <div class="card title">Pasien Isolasi</div>
                            <div class="card content">{{ isolasi }}</div>
                            <div class="card note">Orang</div>
                        </div>
                        <div class="gap-space"></div>
                        <div class="card covid-card grad-pinktoyellow" id="card-sembuh" data-tooltips="Pasien Sembuh">
                            <div class="card title">Pasien Sembuh</div>
                            <div class="card content">{{ sembuh }}</div>
                            <div class="card note">Orang</div>
                        </div>
                        <div class="gap-space"></div>
                        <div class="card covid-card grad-yellowtored"  id="card-meninggal" data-tooltips="Pasien Meninggal">
                            <div class="card title">Pasien Meninggal</div>
                            <div class="card content">{{ meninggal }}</div>
                            <div class="card note">Orang</div>
                        </div>
                    </div>
                    <div class="media note">
                        <p>Data Pasien Wilayah Kabupaten Semarang (update pukul <span id="last-index"></span> )</p>
                        <p>Sumber: corona.semarangkab.go.id </p>
                    </div>
                </div>
                <div class="article body">
                    <h2>Data Sebaran Di Desa</h2>
                    <div class="table-boxs">
                        <table id=covid-table>
                            <thead>
                                <tr>
                                    <td rowspan="2">No</td>
                                    <td rowspan="2">Desa/Kelurahan</td>
                                    <td colspan="3">Kasus Suspek</td>
                                    <td colspan="4">Terkomfirmasi</td>
                                </tr>
                                <tr>
                                    <td>Suspek</td>
                                    <td>Discharded</td>
                                    <td>Meninggal</td>
                                    <td>Symptomatik </td>
                                    <td>Asymptomatik</td>
                                    <td>Sembuh</td>
                                    <td>Meninggal</td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in rows.data" :key="rows.data.desa">
                                    <td>{{ index + 1}}</td>
                                    <td>{{ row.desa }}</td>
                                    <td>{{ row.pdp.dirawat }}</td>
                                    <td>{{ row.pdp.sembuh }}</td>
                                    <td>{{ row.pdp.meninggal }}</td>
                                    <td>{{ row.positif.dirawat }}</td>
                                    <td>{{ row.positif.isolasi }}</td>
                                    <td>{{ row.positif.sembuh }}</td>
                                    <td>{{ row.positif.meninggal }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">Jumlah</td>
                                    <td>{{ rows.suspek }}</td>
                                    <td>{{ rows.suspek_discharded }}</td>
                                    <td>{{ rows.suspek_meninggal }}</td>
                                    <td>{{ rows.kasus_posi }}</td>
                                    <td>{{ rows.kasus_isol }}</td>
                                    <td>{{ rows.kasus_semb }}</td>
                                    <td>{{ rows.kasus_meni }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                <div class="charts">
                    <h2>Perkembangan Covid Kabupaten Semarang (Kumulatif)</h2>
                    <h3>Positif dan Supek Covid</h3>
                    <div class="chart">
                        <canvas id="chartjs-0" 
                                width="400" height="200" 
                                aria-label="grafik positif dan suspek covid" 
                                role="img">
                            </canvas>
                    </div>
                    <h3>Meninggal</h3>
                    <div class="chart">
                        <canvas id="chartjs-1" 
                                width="400" height="200" 
                                aria-label="grafik meninggal covid dan suspek" 
                                role="img">
                            </canvas>
                    </div>
                    
                    <div class="info">
                        <p>Kami memohon maaf, atas telah kehilangan data dari 14/09 s/d 23/9 untuk kasus <strong>suspek covid</strong></p>
                    </div>
                </div>

                    <h2>Istilah-istilah Terkait</h2>
                    <ol>
                        <li>
                            <h3>Kasus Suspek</h3>
                            <p>Kasus Suspek Seseorang yang memiliki salah satu dari kriteria berikut:</p>
                            <ul>
                                <li>Orang dengan Infeksi Saluran Pernapasan Akut (ISPA) dan pada 14 hari terakhir sebelum timbul gejala memiliki <strong>riwayat perjalanan</strong> atau tinggal di negara/wilayah Indonesia yang melaporkan transmisi local.</li>
                                <li>Orang dengan salah satu gejala/tanda ISPA, dan pada 14 hari terakhir sebelum timbul gejala memiliki <strong>riwayat kontak</strong> dengan kasus konfirmasi/probable COVID-19.</li>
                                <li>Orang dengan ISPA berat/pneumonia berat yang <strong>membutuhkan perawatan</strong> di rumah sakit dan tidak ada penyebab lain berdasarkan gambaran klinis yang meyakinkan.</li>
                            </ul>
                        </li>
                        <li>
                            <h3>Discarded</h3>
                            <p>Istilah ini merujuk pada pasien sembuh. Adapun kriterianya yakni:</p>
                            <ul>
                                <li>Pasien yang hasil pemeriksaan RT-PCR 2 kali negatif selama 2 hari berturut-turut dengan selang waktu 24 jam.</li>
                                <li>Seseorang yang berstatus Kontak Erat dan sudah menyelesaikan masa karantina selama 14 hari.</li>
                            </ul>
                        </li>
                        <li>
                            <h3>Kasus Konfirmasi</h3>
                            <p>Seseorang yang dinyatakan positif terinfeksi virus COVID-19 yang dibuktikan dengan pemeriksaan lab RT-PCR. Kasus konfirmasi dibagi 2 yakni:</p>
                            <ul>
                                <li><strong>Simptomatik</strong> atau konfirmasi dengan gejala</li>
                                <li><strong>Asimptomatik</strong> atau konfirmasi tanpa gejala</li>
                            </ul>
                        </li>
                    </ol>
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

    $load(function(){
        $json('/api/ver1.1/Covid-Kab-Semarang/indexing.json')
            .then(json => {
                $id('last-index').innerText = json['last_index'];
                if(json['index_status'] == 'sussessful'){
                    $id('last-index').innerText = json['next_index'];
                    // reload
                    card.render_card();
                }
            })
    })
    
    // menagbil data
    let card = new Vue({
        el: '#covid-card',
        data: {
            dirawat: 0,
            isolasi: 0,
            sembuh: 0,
            meninggal: 0
        },
        methods: {
            grapInfo: async function(data){
                let posi        = $id('card-positif').getAttribute('data-tooltips');
                let isol        = $id('card-isolasi').getAttribute('data-tooltips');
                let semb        = $id('card-sembuh').getAttribute('data-tooltips');
                let meni        = $id('card-meninggal').getAttribute('data-tooltips');

                await data['data'].forEach(event => {
                    if( event['kasus_posi'] != 0){
                    posi += `, ${event['kecamatan']}(${event['kasus_posi']})`;
                    }
                    if( event['kasus_isol'] != 0){
                        isol += `, ${event['kecamatan']}(${event['kasus_isol']})`;
                    }
                    if( event['kasus_semb'] != 0){
                        semb += `, ${event['kecamatan']}(${event['kasus_semb']})`;
                    }
                    if( event['kasus_meni'] != 0){
                        meni += `, ${event['kecamatan']}(${event['kasus_meni']})`;
                    }
                })
                
                $id('card-positif').setAttribute('data-tooltips', posi );
                $id('card-isolasi').setAttribute('data-tooltips', isol );
                $id('card-sembuh').setAttribute('data-tooltips', semb );
                $id('card-meninggal').setAttribute('data-tooltips', meni );
            },
            render_card: function(){
                $json('/api/ver1.0/Covid-Kab-Semarang/tracker-data.json')
                    .then( json => {
                        this.dirawat    = json['kasus_posi'];
                        this.isolasi    = json['kasus_isol'];
                        this.sembuh     = json['kasus_semb'];
                        this.meninggal  = json['kasus_meni'];
                        
                        this.grapInfo(json);
                        
                        // data vue table 
                        table.rows = json.data[16];
                        // table.rows          = json.data.filter(k => k.kecamatan == 'ungaran-barat')[0];
                    })
            }
        },
        created(){
            this.render_card();
        }
    })

    let table = new Vue({
        el: '#covid-table',
        data: {
            rows: []
        }
    })    

    let chart_covid_postif = new Chart($id("chartjs-0"), {
        type:"line",
        data:{
            labels: <?= $portal['contents']['date_record'] ?>,
            datasets:[
                {
                    label: "Suspek",
                    data: <?= $portal['contents']['suspek'] ?>,
                    fill: true,
                    borderColor: "rgb(255, 69, 0)",
                    backgroundColor: "rgba(255, 69, 0, 0.3)",
                    lineTension: 0.4
                },
                {
                    label:"Konfirmasi Covid",
                    data: <?= $portal['contents']['kasus_posi'] ?>,
                    fill: true,
                    borderColor: "rgb(75, 192, 192)",
                    backgroundColor: "rgba(75, 192, 192, 0.3)",
                    lineTension: 0.4
                }]
            },
        options:{
                scales: {
                    yAxes: [{
                        ticks: {
                                suggestedMin: 15,
                                suggestedMax: 40
                        }
                    }]
                }
        }
    });

    let chart_covid_suspek = new Chart($id("chartjs-1"), {
        type:"line",
        data:{
            labels: <?= $portal['contents']['date_record'] ?>,
            datasets:[{
                    label: "Kasus Meninggal",
                    data: <?= $portal['contents']['kasus_meni'] ?>,
                    fill: true,
                    borderColor: "rgb(255,69,0)",
                    backgroundColor: "rgba(255,69,0, 0.3)",
                    lineTension: 0.4
                },
                {
                    label:"Suspek Meninggal",
                    data: <?= $portal['contents']['suspek_meni'] ?>,
                    fill:true,
                    borderColor:"rgb(255, 127, 0)",
                    backgroundColor:"rgba(255, 127, 0, 0.3)",
                    lineTension:0.4
                }]
            },
        options:{
                scales: {
                    yAxes: [{
                        ticks: {
                                suggestedMin: 40,
                                suggestedMax: 80
                        }
                    }]
                }
        }
    });
</script>
</html>
