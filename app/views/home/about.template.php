<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
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
        <?php
            include(BASEURL . '/resources/components/header/header.php')
        ?>
    </header>

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
                        <p>Untuk semua Pegawai puskesmas Lerep dapat ikut perkontribusi langsung melalui Registrasi langsung di <a href="/register">Register User Baru</a></p>
                    </div>
                    <div class="sparator blue">
                        <div class="sparator-title"><h3>Lini Masa</h3></div>
                    </div>
                    <div class="boxs-timeline">
                        <ul class="time-line">
                        <?php foreach($content->time_line as $row ): ?>
                            <li class="story">
                                <div class="time"><?= $row['date'] ?></div>
                                <div class="message">
                                    <p><?= $row['note'] ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </atricle>
            </div>
        </main>
        <aside class="right-side">

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
</script>
</html>
