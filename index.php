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
    <link rel="stylesheet" href="lib/css/ui/v1/control.costume.css">
    <link rel="stylesheet" href="lib/css/ui/v1/control.css">
    <script src="lib/js/index.js"></script>
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
        /* tablet vie view */
        @media screen  and (max-width: 767px) {
            /* costume main container */
            .container.width-view{
                display: grid;
                grid-template-columns: 1fr;
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
                <a href="#">Edit profile</a>
                <a href="#">Pesan Masuk</a>
                <a href="/p/auth/reset-password/">Ganti Pasword</a>
                <a href="/p/auth/logout/">Log Out</a>
            </div>
        </div>
    </div>
    <aside class="top">
        <div class="boxs-header">
            <p>Info Covid (jawa tengah) <span><a href="https://kawalcorona.com/">i</a></span></p>
        </div>
        <div class="boxs-info">
            <div class="info one">
                <div class="item-info left"></div>
                <div class="item-info right">xxx positif</div>
            </div>
            <div class="info two">
                <div class="item-info left"></div>
                <div class="item-info right">xxx sembuh</div></div>
            <div class="info three">
                <div class="item-info left"></div>
            <div class="item-info right">xxx meninggal</div></div>
        </div>
    </aside>
    <div class="container width-view">
        <main class="news">
            <div class="boxs-card">
                <div class="cards">
                    <div class="card title">
                        <p>15000</p>
                    </div>
                    <div class="card content">
                        <p>Data RM</p>
                    </div>
                </div>
                <div class="cards">
                    <div class="card title">
                        <p><?= $jumlah_rm?></p>
                    </div>
                    <div class="card content">
                        <p>Data RM Terdata</p>
                    </div>
                </div>
                <div class="cards">
                    <div class="card title">
                        <p><?= round( ($jumlah_rm / 15000) * 100, 1) ?>% </p>
                    </div>
                    <div class="card content">
                        <p>Prosentase </p>
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
                <div class="reviews">
                    <div class="review title">
                        <p>Ulasan untuk Kami</p>
                    </div>
                    <div class="review respones">
                        <div class="respone low">
                        </div>
                        <div class="respone med"></div>
                        <div class="respone hig"></div>
                    </div>
                    <div class="review comment">
                        <input type="text" id="input-comment" placeholder="Kritik dan saran">
                    </div>
                </div>
            </div>
            <div class="boxs-timetable">
                <div class="timetables">
                    <div class="timetable title">
                        <p>Jadwal Pelayanan</p>
                    </div>
                    <div class="timetable hours">
                        <div class="box-day <?= date('D') == 'Mon' ? 'active' : ''?>">
                            <div class="day"><p>Senin</p></div>
                            <div class="hour"><p>08:00 AM-12:00 AM</p></div>
                        </div>
                        <div class="box-day <?= date('D') == 'Tue' ? 'active' : ''?>">
                            <div class="day"><p>Selasa</p></div>
                            <div class="hour"><p>08:00 AM-12:00 AM</p></div>
                        </div>
                        <div class="box-day <?= date('D') == 'Wed' ? 'active' : ''?>">
                            <div class="day"><p>Rabu</p></div>
                            <div class="hour"><p>08:00 AM-12:00 AM</p></div>
                        </div>
                        <div class="box-day <?= date('D') == 'Thu' ? 'active' : ''?>">
                            <div class="day"><p>Kamis</p></div>
                            <div class="hour"><p>08:00 AM-12:00 AM</p></div>
                        </div>
                        <div class="box-day <?= date('D') == 'Fri' ? 'active' : ''?>">
                            <div class="day"><p>Jumat</p></div>
                            <div class="hour"><p>08:00 AM-10:30 AM</p></div>
                        </div>
                        <div class="box-day <?= date('D') == 'Sat' ? 'active' : ''?>">
                            <div class="day"><p>Sabtu</p></div>
                            <div class="hour"><p>08:00 AM-11:00 AM</p></div>
                        </div>
                        <div class="box-day <?= date('D') == 'Sun' ? 'active' : ''?>">
                            <div class="day"><p>Minggu</p></div>
                            <div class="hour"><p>Tutup</p></div>
                        </div>
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
                <a href="#">About</a>
            </div>
            <div class="box ourteam">
                <a href="#">Meet Our Team</a>
            </div>
            <div class="box contact">
                <a href="p/contact/contactus/">Contact Us</a>
            </div>
        </div>
        <div class="footnote">
            <p class="note-footer">creat by <a href="https://twitter.com/AnggerMPd">amp</a></p>
        </div>
    </footer>
    <script>
        // memuat info Covid, source https://kawalcorona.com/api/
        window.addEventListener('load', event => {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function(){
                if( this.readyState == 4 && this.status == 200){
                    // berhasil memanggil
                    var json = JSON.parse( this.responseText);
                    var jateng = json.find(json => json['attributes']['Provinsi'] == 'Jawa Tengah');

                    var info_one = document.querySelector('.info.one .item-info.right');
                    info_one.innerHTML = jateng['attributes']['Kasus_Posi'] + ' positif';

                    var info_one = document.querySelector('.info.two .item-info.right');
                    info_one.innerHTML = jateng['attributes']['Kasus_Semb'] + ' sembuh';

                    var info_one = document.querySelector('.info.three .item-info.right');
                    info_one.innerHTML = jateng['attributes']['Kasus_Meni'] + ' meninggal';
                }
            }
            xhr.open('GET', 'https://api.kawalcorona.com/indonesia/provinsi/', true);
            xhr.send();
        })
        // sticky header
        window.onscroll = function(){stickyHeader()};
        var mycontent = document.querySelector('aside');
    </script>
    <script src="lib/js/index.end.js"></script>
</body>
</html>
