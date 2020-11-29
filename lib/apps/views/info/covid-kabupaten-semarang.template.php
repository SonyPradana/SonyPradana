<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <link rel="stylesheet" href="/lib/css/pages/v1.1/covid.css">
    <link rel="stylesheet" href="/lib/css/ui/v1.1/full.style.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
    <script src="/lib/js/vendor/vue/vue.min.js"></script>
    <script src="/lib/js/vendor/chart/Chart.min.js"></script>
    <script src="/lib/js/vendor/enquire/enquire.min.js"></script>
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
        <?php include(BASEURL . '/lib/components/header/header.php'); ?>
    </header>
    
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
                            <img src="<?= $content->article['display_picture_small'] ?>" alt="@<?= $content->article['display_name'] ?>" srcset="">    
                            <div class="author-name"><a href="/Ourteam"><?= $content->article['display_name'] ?></a></div>
                        </div>
                        <div class="time">11 April 2020</div>
                    </div>
                </div>
                <div class="media-article">
                    <div id="covid-card" class="box cards">
                        <div class="card covid-card grad-blue" id="card-positif" data-tooltips="Pasien Positif">
                            <div class="card title">Pasien Positif</div>
                            <div class="card content" v-text="dirawat"></div>
                            <div class="card note">Orang</div>
                        </div>
                        <div class="gap-space"></div>
                        <div class="card covid-card  grad-yellowtored" id="card-isolasi" data-tooltips="Pasien Isolasi">
                            <div class="card title">Pasien Isolasi</div>
                            <div class="card content" v-text="isolasi"></div>
                            <div class="card note">Orang</div>
                        </div>
                        <div class="gap-space"></div>
                        <div class="card covid-card grad-pinktoyellow" id="card-sembuh" data-tooltips="Pasien Sembuh">
                            <div class="card title">Pasien Sembuh</div>
                            <div class="card content" v-text="sembuh"></div>
                            <div class="card note">Orang</div>
                        </div>
                        <div class="gap-space"></div>
                        <div class="card covid-card grad-yellowtored"  id="card-meninggal" data-tooltips="Pasien Meninggal">
                            <div class="card title">Pasien Meninggal</div>
                            <div class="card content" v-text="meninggal"></div>
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
                                    <td v-text="index + 1"></td>
                                    <td v-text="row.desa"></td>
                                    <td v-text="row.pdp.dirawat"></td>
                                    <td v-text="row.pdp.sembuh"></td>
                                    <td v-text="row.pdp.meninggal"></td>
                                    <td v-text="row.positif.dirawat"></td>
                                    <td v-text="row.positif.isolasi"></td>
                                    <td v-text="row.positif.sembuh"></td>
                                    <td v-text="row.positif.meninggal"></td>
                                </tr>
                                <tr>
                                    <td colspan="2">Jumlah</td>
                                    <td v-text="rows.suspek"></td>
                                    <td v-text="rows.suspek_discharded"></td>
                                    <td v-text="rows.suspek_meninggal"></td>
                                    <td v-text="rows.kasus_posi"></td>
                                    <td v-text="rows.kasus_isol"></td>
                                    <td v-text="rows.kasus_semb"></td>
                                    <td v-text="rows.kasus_meni"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                <div class="charts">
                    <h2>Perkembangan Covid Kabupaten Semarang (Kumulatif)</h2>
                    <h3>Positif dan Supek Covid</h3>
                    <div class="chart">
                        <canvas id="chartjs-0" 
                                width="400" height="220" 
                                aria-label="grafik positif dan suspek covid" 
                                role="img">
                            </canvas>
                    </div>
                    <h3>Meninggal</h3>
                    <div class="chart">
                        <canvas id="chartjs-1" 
                                width="400" height="220" 
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
            <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/widget/trivia.html') ?>
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

    $load(function() {
        update_chart();
        
        $json('/api/ver1.1/Covid-Kab-Semarang/info.json')
            .then(json => {
                $id('last-index').innerText = json['last_index'];
            })
    })

    const update_chart = function() {
        $json('/api/ver1.1/Covid-Kab-Semarang/tracker-all.json')
            .then(json => {
                if (json.status == 'ok') {
                    // reset
                    chart_covid_postif.data.datasets[0].data = [];
                    chart_covid_postif.data.datasets[1].data = [];
                    chart_covid_meninggal.data.datasets[0].data = [];
                    chart_covid_meninggal.data.datasets[1].data = [];
    
                    let date_record = Array();
                    json.data.forEach(el => {
                        date_record.push(el.time);
                        chart_covid_postif.data.datasets[0].data.push(el.suspek);
                        chart_covid_postif.data.datasets[1].data.push(el.kasus_posi);
                        chart_covid_meninggal.data.datasets[0].data.push(el.kasus_meni);
                        chart_covid_meninggal.data.datasets[1].data.push(el.suspek_meninggal);
                    });
                    
                    chart_covid_postif.data.labels = date_record;
                    chart_covid_meninggal.data.labels = date_record;
                    chart_covid_postif.update();
                    chart_covid_meninggal.update();
                }
            })
    }
    
    // apps
    const card = new Vue({
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
                        if (json.status == 'ok') {
                            this.dirawat    = json['kasus_posi'];
                            this.isolasi    = json['kasus_isol'];
                            this.sembuh     = json['kasus_semb'];
                            this.meninggal  = json['kasus_meni'];
                            
                            this.grapInfo(json);
                            
                            // data vue table 
                            // table.rows = json.data[16];
                            table.rows          = json.data.filter(k => k.kecamatan == 'ungaran-barat')[0];
                        }
                    })
            }
        },
        mounted(){
            this.render_card();
        }
    });

    const table = new Vue({
        el: '#covid-table',
        data: {
            rows: []
        }
    });

    const chart_covid_postif = new Chart($id("chartjs-0"), {
        type:"line",
        data:{
            labels: null,
            datasets:[
                {
                    label: "Suspek",
                    data: null,
                    fill: true,
                    borderColor: "rgb(255, 69, 0)",
                    backgroundColor: "rgba(255, 69, 0, 0.3)",
                    lineTension: 0.4
                },
                {
                    label:"Konfirmasi Covid",
                    data: null,
                    fill: true,
                    borderColor: "rgb(75, 192, 192)",
                    backgroundColor: "rgba(75, 192, 192, 0.3)",
                    lineTension: 0.4,
                }]
            },
        options:{
            scales: {
                yAxes: [{
                    ticks: {
                        suggestedMin: 15,
                        suggestedMax: 50
                    }
                }]
            }
        }
    });

    const chart_covid_meninggal = new Chart($id("chartjs-1"), {
        type:"line",
        data:{
            labels: null,
            datasets:[{
                    label: "Kasus Meninggal",
                    data: null,
                    fill: true,
                    borderColor: "rgb(255,69,0)",
                    backgroundColor: "rgba(255,69,0, 0.3)",
                    lineTension: 0.4
                },
                {
                    label:"Suspek Meninggal",
                    data: null,
                    fill:true,
                    borderColor:"rgb(254, 203, 0)",
                    backgroundColor:"rgba(254, 203, 0, 0.3)",
                    lineTension:0.4
                }]
            },
        options:{
            scales: {
                yAxes: [{
                    ticks: {
                        suggestedMin: 40,
                        suggestedMax: 90
                    }
                }]
            }
        }
    });

    
    // media query
    enquire.register("screen and (max-width:479px)", {
        match : function() {
            chart_covid_postif.data.datasets[0].pointRadius = 1;
            chart_covid_postif.data.datasets[1].pointRadius = 1;
            chart_covid_meninggal.data.datasets[0].pointRadius = 1;
            chart_covid_meninggal.data.datasets[1].pointRadius = 1;

            chart_covid_postif.data.datasets[0].pointHoverRadius = 3;
            chart_covid_postif.data.datasets[1].pointHoverRadius = 3;
            chart_covid_meninggal.data.datasets[0].pointHoverRadius = 3;
            chart_covid_meninggal.data.datasets[1].pointHoverRadius = 3;
            
            chart_covid_postif.data.datasets[0].borderWidth = 2;
            chart_covid_postif.data.datasets[1].borderWidth = 2;
            chart_covid_meninggal.data.datasets[0].borderWidth = 2;
            chart_covid_meninggal.data.datasets[1].borderWidth = 2;
            
            chart_covid_postif.options.maintainAspectRatio = false;
            chart_covid_meninggal.options.maintainAspectRatio = false;
        },
        unmatch : function() {
            chart_covid_postif.data.datasets[0].pointRadius = 2;
            chart_covid_postif.data.datasets[1].pointRadius = 2;
            chart_covid_meninggal.data.datasets[0].pointRadius = 2;
            chart_covid_meninggal.data.datasets[1].pointRadius = 2;

            chart_covid_postif.data.datasets[0].pointHoverRadius = 4;
            chart_covid_postif.data.datasets[1].pointHoverRadius = 4;
            chart_covid_meninggal.data.datasets[0].pointHoverRadius = 4;
            chart_covid_meninggal.data.datasets[1].pointHoverRadius = 4;
            
            chart_covid_postif.data.datasets[0].borderWidth = 3;   
            chart_covid_postif.data.datasets[1].borderWidth = 3;
            chart_covid_meninggal.data.datasets[0].borderWidth = 3;
            chart_covid_meninggal.data.datasets[1].borderWidth = 3;
            
            chart_covid_postif.options.maintainAspectRatio = true;
            chart_covid_meninggal.options.maintainAspectRatio = true;
        }
    });
</script>
</html>
