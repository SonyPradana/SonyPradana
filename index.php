<?php 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
?>
<?php 
    #Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 2);
    $user = new User($auth->getUserName());
?>
<?php
    # mengambil data rm
    $data_rm = new View_RM();    
    $jumlah_rm = $data_rm->maxData();

    # jadwal pelayana
    $jadwal = [
        1 => ["day" => "Senin", "time" => "08:00 AM-12:00 AM"],
        2 => ["day" => "Selasa", "time" => "08:00 AM-12:00 AM"],
        3 => ["day" => "Rabu", "time" => "08:00 AM-12:00 AM"],
        4 => ["day" => "Kamis", "time" => "08:00 AM-12:00 AM"],
        5 => ["day" => "Jumat", "time" => "08:00 AM-10:30 AM"],
        6 => ["day" => "Sabtu", "time" => "08:00 AM-11:00 AM"],
        7 => ["day" => "Minggu", "time" => "Tutup"],
    ];
    $sort_day = [];
    $n = date('N');
    for($i = $n; $i <= 7; $i++) { 
        $sort_day[$i] = $jadwal[$i];
    }
    for ($i = 1; $i < $n ; $i++) { 
        $sort_day[$i] = $jadwal[$i];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head> 
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPUS Lerep</title>
    <meta name="description" content="Sistem Informasi Manajemen Puskesmas SIMPUS Lerep">
    <meta name="keywords" content="simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang">
    <meta name="author" content="amp">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" sizes="16x16 24x24 32x32 64x64">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="lib/css/main.css">
    <link rel="stylesheet" href="lib/css/ui/v1/widget.css">
    <link rel="stylesheet" href="lib/css/ui/v1/control.css">
    <link rel="stylesheet" href="lib/css/ui/v1/card.css">
    <link rel="stylesheet" href="lib/css/ui/v1/alert.css">
    <script src="lib/js/index.js"></script>
    <script src="lib/js/bundles/message.js"></script>
    <script src="lib/js/bundles/keepalive.js"></script>
    <script src="lib/js/vendor/vue/vue.min.js"></script>
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

        .covid-card .card.action .btn{            
            width: 64px;
            text-align: center;    
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
        <?php $active_menu = 'home' ?>
        <div class="header title">
            <p>Welcome To Simpus Lerep</p>
        </div>
        <div class="header menu">
            <div class="logo">
                <a href="/">Simpus</a>
            </div>
            <div class="nav">                
                <?php if( $auth->TrushClient()): ?>                
                <a href="/p/med-rec/view-rm/" <?= $active_menu == 'lihat data'? 'class="active"' : ''?>>Lihat RM</a>
                <a href="/p/med-rec/search-rm/" <?= $active_menu == 'cari data'? 'class="active"' : ''?>>Cari RM</a>
                <a href="/p/med-rec/new-rm/" <?= $active_menu == 'buat data'? 'class="active"' : ''?>>Buat RM</a>
                <?php endif; ?>
            </div>
            <div class="account">
                <?php if( $auth->TrushClient()): ?>
                <div class="boxs-account"  onclick="open_modal()">
                    <div class="box-account left">
                        <div class="pic-box"></div>
                    </div>
                    <div class="box-account right">
                        <p><?= $user->getDisplayName()?></p>
                    </div>
                </div>                
                <?php else: ?>
                    <a class="btn outline blue small" href="/p/auth/login">login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <div class="modal account">
        <div class="modal-box">
            <span class="close" onclick="close_modal()">&times;</span>
            <div class="boxs-menu">
                <a href="/p/auth/profile/">Edit profile</a>
                <a href="/p/messages/public/">Pesan Masuk</a>
                <a href="/p/auth/reset-password/">Ganti Pasword</a>
                <a href="/p/auth/logout/">Log Out</a>
            </div>
        </div>
    </div>
    <aside class="top">
        <div class="boxs-header">
            <p>Info Covid (kabupaten semarang) <span><a href="https://corona.semarangkab.go.id/covid/">i</a></span></p>
        </div>
        <div class="boxs-info" id="c-covid">
            <div class="info one">
                <div class="item-info left"></div>
                <div class="item-info right">{{ msgPosi }}</div>
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
                <div class="rm-card">
                    <div class="card title">
                        <p><?= $jumlah_rm?></p>
                        <span class="detail"> ~<?= round( ($jumlah_rm / 15000) * 100, 1) ?>%</span>
                    </div>
                    <div class="card content">
                        <p>Data RM Terdata</p>
                    </div>
                </div>
                <div class="covid-card gradient-one medium shadow" data-tooltips="click untuk melihat">
                    <div class="card title">
                        Info Covid Ungaran
                    </div>
                    <div class="card action">
                        <a href="/p/info/covid-kabupaten-semarang/" class="btn fill blue small rounded">Lihat &raquo</a>
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
                        <input type="text" id="input-comment" placeholder="Kritik dan saran">
                    </div>
                </div>
            </div>
            <div class="boxs-timetable">
                <div class="timetables">
                    <div class="timetable title">
                        <h3>Jadwal Pelayanan</h3>
                    </div>
                    <div class="timetable hours">
                    <?php foreach ($sort_day as $key => $value) :?>
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
        <div class="line"></div>
        <p class="big-footer">SIMPUS LEREP</p>
        <div class="boxs footer">
            <div class="box about">
                <a href="/p/about/">About</a>
            </div>
            <div class="box ourteam">
                <a href="/p/contact/ourteam/">Meet Our Team</a>
            </div>
            <div class="box contact">
                <a href="p/contact/contactus/">Contact Us</a>
            </div>
        </div>
        <div class="footnote">
            <p class="note-footer"><a href="https://twitter.com/AnggerMPd">Made with <span class="love">&#9829;</span> by amp</a></p>
        </div>
    </footer>
    <script src="lib/js/index.end.js"></script>
    <script>        
        let togleReviews = new Vue({
            el: '#w-reviews',
            data:{
                seenResult: false,
                seenRespones: true
            }
        });

        let infoCovid = new Vue({
            el: '#c-covid',
            data:{
                msgPosi: 'xxx positif',
                msgSemb: 'xxx sembuh',
                msgMeni: 'xxx meninggal'
            }
        })

        // memuat info Covid, source https://corona.semarangkab.go.id/covid/
        window.addEventListener('load', event => {
            const render = async() => {
                const fetchJSON = await fetch('/lib/ajax/json/public/covid-kab-semarang/info/index.php',{
                    headers:{
                        'Content-Type': 'application/json'
                    }
                })
                return fetchJSON.json();
            }

            render()
                .then( json => {
                    infoCovid.msgPosi = json['kasus_posi'] + ' positif';
                    infoCovid.msgSemb = json['kasus_semb'] + ' sembuh';
                    infoCovid.msgMeni = json['kasus_meni'] + ' meninggal';
                })
        })
        // sticky header
        window.addEventListener('scroll', () => {
            stickyHeader('aside')
        })

        // keep alive
        keepalive(() => {
            location.reload()
        })

        function togle( show ){
            if( show ){
                togleReviews.seenResult = true;
                togleReviews.seenRespones = false;
            }else{
                togleReviews.seenResult = false;
                togleReviews.seenRespones = true;
            }
        }

        function newRating( rating ){
            Rating(rating, 3, 'Rekam Medis').then(json =>{
                if( json['status'] == 'ok'){
                    togle(true)
                }
            })
        }

        const s_msg = document.querySelector('#input-comment');
        s_msg.addEventListener('click', function(){
            window.location = '/p/contact/contactus/';
        });

    </script>
</body>
</html>
