<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/auth/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/library/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/simpus/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/db/db_crud/DbConfig.php';
?>
<?php 
    #Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 1);
    $user = new User($auth->getUserName());
?>
<?php 
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
    <meta name="description" content="sisteminformasi kesehtan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">

    <link rel="stylesheet" href="lib/css/style-main.css">
    <style>
    </style>
</head>
<body>
    <header>
        <div class="header title">
           <p>Welcome To Simpus Lerep</p>
        </div>
        <div class="header nav">
            <nav class="banner">
                <div class="logo">
                    <a href="/" onclick="myFunction()">Home</a>
                </div>
                <div class="menu">
                <?php if( $auth->TrushClient()): ?>
                    <a href="/p/med-rec/view-rm/">lihat data rm</a>
                    <a href="/p/med-rec/search-rm/">cari data rm</a>
                    <a href="/p/med-rec/new-rm/">buat data rm</a>
                <?php endif; ?>
                </div>
            </nav>
            <div class="account">
                <?php if( $auth->TrushClient()): ?>
                    <div class="boxs">
                        <div class="box left">
                            <div class="pic-box"></div>
                        </div>
                        <div class="box right">
                            <p><?= $user->getDisplayName()?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/p/auth/login">login</a>
                <?php endif; ?>
            </div>
        </div>  
    </header>  
    <main>
        <div class="container">
            
        </div>
    </main> 
    <footer>
        <div class="line"></div>        
        <p class="big-footer">SIMPUS LEREP</p>        
        <p class="note-footer">creat by <a href="https://twitter.com/AnggerMPd">amp</a></p>
        <div class="box"></div>
    </footer>
</body>
<script>
    function openNav(){
        document.getElementById("myNav").style.width = "250px";
    }
</script>
</html>
