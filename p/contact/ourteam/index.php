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
    $db = new MyPDO();
    $db->query('SELECT `display_name`, `section` FROM `profiles`');
    $result = $db->resultset();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tim Kita</title>
    <meta name="description" content="TTeam Kita">
    <meta name="keywords" content="simpus lerep, puskesmas lerep, puskesmas, aboutus, about">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">    
    <link rel="stylesheet" href="/lib/css/ui/v1/card.css">
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

        .team-boxs .box.header{
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%
        }
        .team-boxs .box.cards{
            width: auto; height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .team-card{margin: 0 16px}

        @media screen and (max-width: 600px) {
            .container{grid-template-columns: 1fr}
        }
        @media screen and (max-width: 347px) {
            .team-boxs .box.cards{flex-direction: column}
            .team-card{margin: 16px 0}
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
                    <li>Contact</li>
                    <li>Team Kita</li>
                </ul>
            </div>
            <div class="team-boxs">
                <div class="box header"><h1>Our Team</h1></div>
                <div class="box cards">
                    <?php foreach ($result as $value) :?>
                    <div class="team-card">
                        <div class="card image">
                            <img src="/data/img/display-picture/no-image.png" alt="profile ">
                        </div>
                        <div class="card content">
                            <div class="title"><?= $value['display_name'] ?></div>
                            <div class="subtitle"><?= $value['section'] ?></div>
                        </div>
                    </div>
                    <?php endforeach ;?>
                </div>
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
    window.onscroll = function(){
            stickyHeader('.container', '82px', '32px')
    }
</script>
</html>
