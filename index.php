<?php 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
?>
<?php 
    #Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 1);
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
    <script src="lib/js/index.js"></script>
    <style>
        /* kerangka aside */
        aside{
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            flex-flow: column;
            height: 100px;
        }
        aside .boxs-info{
            display: grid;
            grid-template-columns: repeat(3, 130px);
            grid-column-gap: 12px;
            align-items: center;
            overflow-x: auto;
        }
        aside .boxs-info .info{
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0px 4px;
        }
        /* sttyle aside */
        aside .boxs-header p{
            margin: 8px 0px;
        }
        aside .boxs-info .info{
            height: 40px;
            background: linear-gradient(25deg,#d64c7f,#ee4758 50%);
            border-radius: 12px;
            cursor: pointer
        }
        aside .boxs-info .info .item-info.left{
            background-color: wheat;
            width: 20px; height: 20px;
            border-radius: 50%;     
            display: flex;
            align-items: center;       
        }
        aside .boxs-info .info .item-info.right{
            color: #fff;
            display: flex;
            align-items: center;
        }
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
                    <a href="/p/auth/login">login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <div class="modal account">
        <div class="modal-box">
            <span class="close" onclick="close_modal()">&times;</span>
            <div class="boxs-menu">
                <a href="#">Edit profile</a>
                <a href="/p/auth/reset-password/">Ganti Pasword</a>
                <a href="/p/auth/logout/">Log Out</a>
            </div>
        </div>
    </div>
    <aside>
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
                        <p><?= $jumlah_rm ?></p>
                    </div>
                    <div class="box-info-right">
                        <p>Data RM  tersimpan</p>
                    </div>
                </div>
                <div class="box-info info-tree">
                    <div class="box-info-left">
                        <p><?=round( $jumlah_rm / 15000 , 3) ?>%</p>
                    </div>
                    <div class="box-info-right">
                        <p>record</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <div class="line"></div>        
        <p class="big-footer">SIMPUS LEREP</p>        
        <p class="note-footer">creat by <a href="https://twitter.com/AnggerMPd">amp</a></p>
        <div class="box"></div>
    </footer>
    <script>
        // memuat info Covid, source https://kawalcorona.com/api/
        window.addEventListener('load', event => {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function(){
                if( this.readyState == 4 && this.status == 200){
                    // berhasil memanggil
                    var json = JSON.parse( this.responseText);
                    var info_one = document.querySelector('.info.one .item-info.right');
                    info_one.innerHTML = json[4]['attributes']['Kasus_Posi'] + ' positif';

                    var info_one = document.querySelector('.info.two .item-info.right');
                    info_one.innerHTML = json[4]['attributes']['Kasus_Semb'] + ' sembuh';

                    var info_one = document.querySelector('.info.three .item-info.right');
                    info_one.innerHTML = json[4]['attributes']['Kasus_Meni'] + ' meninggal';
                }
            }
            xhr.open('GET', 'https://api.kawalcorona.com/indonesia/provinsi/', true);
            xhr.send();
        })
    </script>
    <script src="lib/js/index.end.js"></script>
</body>
</html>
