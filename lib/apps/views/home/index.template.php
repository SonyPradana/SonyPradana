<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <link rel="stylesheet" href="/lib/css/ui/v1.1/widgets.css">
    <link rel="stylesheet" href="/lib/css/ui/v1.1/cards.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/message.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <script src="/lib/js/vendor/vue/vue.min.js"></script>
    <style>
        /* costume main container */
        .container.width-view{
            margin-top: 12px !important;
            display: grid;
            grid-template-columns: 1fr minmax(250px, 280px);
            grid-column-gap: 24px; grid-row-gap: 24px;
        }
        main.news{            
            overflow-x: hidden;
        }        
        aside.side{
            background-color: #fff
        }
        
        .boxs-card{
            overflow-x: visible;
            display: flex;
            min-height: 115px;
        }

        .respone-card .card.action .btn{            
            width: 64px;
            text-align: center;    
        }

        #jadwal-imunisasi{
            margin-right: 12px;
        }
        /* tablet vie view */
        @media screen  and (max-width: 767px) {
            /* costume main container */
            .container.width-view{
                display: grid;
                grid-template-columns: 1fr;
            }
            .boxs-card{min-height: 80px}
        }
        @media screen and (max-width: 1000px) {
            .boxs-card{
                overflow: auto;
            }
        }
    </style>
</head>
<body>
    <header>
        <?php include(BASEURL . '/lib/components/header/header.php'); ?>
    </header>
    <div id="modal">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    </div>

    <aside class="top">
        <div class="boxs-header">
            <p>Info Covid (kabupaten semarang) <span><a rel="nofollow href="https://corona.semarangkab.go.id/covid/">i</a></span></p>
        </div>
        <div class="boxs-info" id="c-covid">
            <div class="info one">
                <div class="item-info left"></div>
                <div class="item-info right">{{ msgPosi }}</div>
            </div>
            <div class="info three">
                <div class="item-info left"></div>
                <div class="item-info right">{{ msgIsol }}</div>
            </div>
            <div class="info two">
                <div class="item-info left"></div>
                <div class="item-info right">{{ msgSemb }}</div>
            </div>
            <div class="info three">
                <div class="item-info left"></div>
                <div class="item-info right">{{ msgMeni }}</div>
            </div>
        </div>
    </aside>
    <div class="container width-view">
        <main class="news">
            <div class="boxs-card">
                <div class="card rm-card grad-yellowtored shadow-bottom-left-medium">
                    <div class="card title">
                        <p><?= $content->jumlah_rm ?></p>
                        <span class="detail">&nbsp;~<?= round( ($content->jumlah_rm / 15000) * 100, 1) ?>%</span>
                    </div>
                    <div class="card content">
                        <p>Data RM Terdata</p>
                    </div>
                </div>
                <div class="card respone-card grad-pinktoyellow shadow-bottom-left-medium" data-tooltips="click untuk melihat" id="jadwal-imunisasi">
                    <div class="card title">
                        Jadwal Imunisai
                    </div>
                    <div class="card action">
                        <a href="/info/jadwal-pelayanan/" class="btn fill blue small rounded">Lihat &raquo</a>
                    </div>
                </div>
                <div class="card respone-card grad-blue shadow-bottom-left-medium" data-tooltips="click untuk melihat">
                    <div class="card title">
                        Info Covid Ungaran
                    </div>
                    <div class="card action">
                        <a href="/info/covid-kabupaten-semarang" class="btn fill blue small rounded">Lihat &raquo</a>
                    </div>
                </div>
            </div>
            <div class="sparator blue">
                <div class="sparator-title">Berita terbaru</div>
                </div>
            <div class="boxs-news">

            </div>
        </main>
        <aside class="side">
            <div class="boxs-review">
                <div class="reviews" id="w-reviews">
                    <div class="review title">
                        <h3>Ulasan untuk Kami</h3>
                    </div>
                    <div class="review results" v-if="seenResult">
                        <div class="result done" v-on:click="togle(false)">
                            <p>Terimakasih</p>
                        </div>
                    </div>
                    <div class="review respones" v-if="seenRespones">
                        <div class="respone low" v-on:click="newRating(1)">
                        </div>
                        <div class="respone med" v-on:click="newRating(2)"></div>
                        <div class="respone hig" v-on:click="newRating(3)"></div>
                    </div>
                    <div class="review comment">
                        <input type="text" id="input-comment" v-on:click="gotoContact" placeholder="Kritik dan saran">
                    </div>
                </div>
            </div>
            <div class="boxs-timetable">
                <div class="timetables">
                    <div class="timetable title">
                        <h3>Jadwal Pelayanan</h3>
                    </div>
                    <div class="timetable hours">
                    <?php foreach ($portal['contents']['jadwal_sort'] as $key => $value) :?>
                        <div class="box-day <?= date('N') == $key ? 'active' : ''?>">
                            <div class="day"><p><?= $value['day'] ?></p></div>
                            <div class="hour"><p><?= $value['time'] ?></p></div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                    <div class="timtable note">
                        <p>Note: Tanggal merah dan libur tutup</p>
                        <p style="color: blue">Selama wabah covid berlaku pembatasan, untuk keamaan bersama</p>
                    </div>
                </div>
            </div>
        </aside>
    </div>
    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>
    <script src="/lib/js/index.end.js"></script>
    <script>        
        let reviews = new Vue({
            el: '#w-reviews',
            data:{
                seenResult: false,
                seenRespones: true
            },
            methods: {
                togle: function(show){
                    if( show ){
                        this.seenResult     = true;
                        this.seenRespones   = false;
                    }else{
                        this.seenResult     = false;
                        this.seenRespones   = true;
                    }
                },
                newRating: function( rating ){
                    Rating(rating, 3, 'Rekam Medis')
                        .then( json => {
                            if( json['status'] == 'ok' ){
                                this.togle(true);
                            }
                        })
                },
                gotoContact: function(){
                    window.location = '/Contactus';
                }
            }
        });

        let infoCovid = new Vue({
            el: '#c-covid',
            data:{
                msgPosi: 'xxx positif',
                msgIsol: 'xxx isolasi',
                msgSemb: 'xxx sembuh',
                msgMeni: 'xxx meninggal'
            },
            created(){
                $json('/lib/ajax/json/public/covid-kab-semarang/info/index.php')
                .then( json => {
                    this.msgPosi = json['kasus_posi'] + ' positif';
                    this.msgSemb = json['kasus_semb'] + ' sembuh';
                    this.msgMeni = json['kasus_meni'] + ' meninggal';
                    this.msgIsol = json['kasus_isol'] + ' isolasi'
                })
            }
        })

        // sticky header
        window.addEventListener('scroll', () => {
            stickyHeader('aside')
        })

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
    </script>
</body>
</html>
