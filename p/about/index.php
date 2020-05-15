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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami</title>
    <meta name="description" content="Tentang kami">
    <meta name="keywords" content="simpus lerep, puskesmas lerep, puskesmas, aboutus, about">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">    
    <link rel="stylesheet" href="/lib/css/ui/v1/timeline.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <style>
        .container{
            display: grid;
            grid-template-columns: 1fr minmax(250px, 280px);
            grid-column-gap: 24px; grid-row-gap: 24px;
        }
        main{ margin: 24px}
        article .about p{
            line-height: 1.5;
            font-size: 1.3rem;
            margin: 4px 0;
        }
        @media screen and (max-width: 600px) {
            .container{grid-template-columns: 1fr}
        }
    </style>
</head>
<body>    
    <header>
        <?php $active_menu = 'home' ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.html') ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/alert.html') ?>
    
    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Tentang Kami</li>
                </ul>
            </div>
            <div class="about-boxs">
                <div class="about-image">
                    <img src="/data/img/logo/logo-puskesmas.png" alt="logo simpus lerep" width="200">
                </div>
                <article class="about-contain">
                    <div class="about">
                        <h2>Simpus Lerep</h2>
                        <p>SIMPUS, merupakan program aplikasi yang memberikan informasi baik untuk administrasi dan pengelolaan sebuah puskesmas demi meningkatkan kinerja dan menangani keseluruhan proses manajemen di Puskesmas</p>
                    
                        <h2>Hubungi Kami</h2>
                        <p>Kami sangat gembira menerima chat Anda. Sangat bagus apablia ingin memberikan masukan dengan email: <a href="mailto:support@simpuslerep.com">support@simpuslerep.com</a></p>
                        <h2>Kontribusi Bersama</h2>
                        <p>Untuk semua Pegawai puskesmas Lerep dapat ikut perkontribusi langsung melalui Registrasi langsung di <a href="/p/auth/register/">Register User Baru</a></p>
                    </div>
                    <div class="sparator blue">
                        <div class="sparator-title"><h3>Lini Masa</h3></div>
                    </div>
                    <div class="boxs-timeline">
                        <ul class="time-line">
                            <li class="story">
                                <div class="time">21 Maret 2020</div>
                                <div class="message">
                                    <p>I was born, Website pertama kali di publish</p>
                                </div>
                            </li>
                            <li class="story">
                                <div class="time">28 Maret 2020</div>
                                <div class="message">
                                    <p>Data Rekam Medis Pertama dibuat</p>
                                </div>
                            </li>
                            <li class="story">
                                <div class="time">30 Maret 2020</div>
                                <div class="message">
                                    <p>Logo dan Merek dibuat</p>
                                </div>
                            </li>
                            <li class="story">
                                <div class="time">1 April 2020</div>
                                <div class="message">
                                    <p>Penambahan fitur filter di Lihat Data Rekam Medis</p>
                                </div>
                            </li>
                            <li class="story">
                                <div class="time">8 April 2020</div>
                                <div class="message">
                                    <p>Perbaikan Search Result (Hasil Pencaria) Data Rekam Medis lebih tepat / relevant</p>
                                </div>
                            </li>
                            <li class="story">
                                <div class="time">22 April 2020</div>
                                <div class="message">
                                    <p>Pemberithuan(Alert) bila seeson logintelah berahir</p>
                                </div>
                            </li>
                            <li class="story">
                                <div class="time">1 May 2020</div>
                                <div class="message">
                                    <p>Penambahan fitur Ulasan atau Review untuk pelayanan Kami, berseta laporan kritik dan saran.</p>
                                </div>
                            </li>
                            <li class="story">
                                <div class="time">7 May 2020</div>
                                <div class="message">
                                    <p>Peningkatan Keamanan authorization dan authentication dari injection.</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </atricle>
            </div>
        </main>
        <aside class="right-side">

        </aside>
    </div>

    <div class="gotop" onclick="gTop()"></div>
    <?php if( isset( $msg ) ) :?>
        <div class="snackbar">
            <?= $msg ?>
        </div>
    <?php endif; ?>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>
</body>
<script src="/lib/js/index.end.js"></script>
<script>    
    // sticky header
    window.onscroll = function(){stickyHeader('82px', '32px')};
    var mycontent = document.querySelector('.container');
</script>
</html>
