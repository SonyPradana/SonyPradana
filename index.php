<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/library/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/simpus/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/database/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/config/DbConfig.php';
?>
<?php 
    #Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 1);
    $user = new User($auth->getUserName());
?>
<?php
    $db = new MyPDO();
    $db->query("SELECT * FROM data_rm");
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
    <title>SIMPUS Lerep</title>
    <meta name="description" content="sisteminformasi kesehtan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">

    <link rel="stylesheet" href="lib/css/style-main.css">
    <style>
        .boxs-main.top{   
            border-radius: 8px;         
            padding: 30px;
            display: grid;
            grid-template-columns: repeat(3, minmax(232px, 250px));
            grid-column-gap: 60px; 
            grid-row-gap: 60px;
        } 
        .boxs-main.top .box-info {
            display: grid;
            grid-template-columns: 2fr 1fr;
            grid-column-gap: 6px;
            
            justify-content: center;	


            background: #fa2500;
            background: -moz-linear-gradient(-45deg, #fa2500 0%, #e6de00 85%, #e6de00 100%);
            background: -webkit-gradient(left top, right bottom, color-stop(0%, #fa2500), color-stop(85%, #e6de00), color-stop(100%, #e6de00));
            background: -webkit-linear-gradient(-45deg, #fa2500 0%, #e6de00 85%, #e6de00 100%);
            background: -o-linear-gradient(-45deg, #fa2500 0%, #e6de00 85%, #e6de00 100%);
            background: -ms-linear-gradient(-45deg, #fa2500 0%, #e6de00 85%, #e6de00 100%);
            background: linear-gradient(135deg, #fa2500 0%, #e6de00 85%, #e6de00 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fa2500', endColorstr='#e6de00', GradientType=1);

            height: 150px;
            border-radius: 15px;
            box-shadow: -1px 0px 46px 0px rgba(0, 0, 0, 0.28);
        }
        .box-info-left p{
            font-size:47px;
            text-align: right;  
            vertical-align: middle;
            line-height: 150px;
            margin: 0;
        }
        .box-info-right p{
            color: #333;
            font-size: 17px;
            text-align: left;   
            vertical-align: middle;
            margin: 66.5px 0;
        }

        @media screen and (max-width: 1020px) {
            .boxs-main.top{       
                grid-template-columns: repeat(2, minmax(232px, 250px));
            }

        }
        @media screen and (max-width: 747px) {
            .boxs-main.top{
                grid-template-columns: minmax(150px, 250px);
            }
        }
        /* mobile potret*/
        @media screen and (max-width: 400px) {
            .boxs-main.top .box-info{
                grid-template-columns: 1fr;
                grid-template-rows: 2fr 1fr;
            }
            .box-info-left p{
                text-align: center;
                line-height: 100px;
            }
            .box-info-right p{
                margin: 0;
                text-align: center;
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
        <div class="header nav">
            <nav class="banner">
                <div class="logo">
                    <a href="/" onclick="myFunction()">Simpus</a>
                </div>
                <div class="menu">
                <?php if( $auth->TrushClient()): ?>
                    <a href="/p/med-rec/view-rm/" <?= $active_menu == 'lihat data'? 'class="active"' : ''?>>lihat data rm</a>
                    <a href="/p/med-rec/search-rm/" <?= $active_menu == 'cari data'? 'class="active"' : ''?>>cari data rm</a>
                    <a href="/p/med-rec/new-rm/" <?= $active_menu == 'buat data'? 'class="active"' : ''?>>buat data rm</a>
                <?php endif; ?>
                </div>
            </nav>
            <div class="account">
                <?php if( $auth->TrushClient()): ?>
                    <div class="boxs-account">
                        <div class="box-account left">
                            <div class="pic-box"></div>
                        </div>
                        <div class="box-account right">
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
            <div class="boxs-main top">
                <div class="box-info info-one">
                    <div class="box-info-left">
                        <p>15000</p>
                    </div>
                    <div class="box-info-right">
                        <p>Data RM</p>
                    </div>
                </div>
                <div class="box-info info-two">
                    <div class="box-info-left">
                        <p>83</p>
                    </div>
                    <div class="box-info-right">
                        <p>Data RM  tersimpan</p>
                    </div>
                </div>
                <div class="box-info info-tree">
                    <div class="box-info-left">
                        <p>0,005%</p>
                    </div>
                    <div class="box-info-right">
                        <p>record</p>
                    </div>
                </div>
            </div>
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
