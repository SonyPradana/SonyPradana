<?php
#import modul 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
?>
<?php
#Aunt cek
session_start();
$token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
$auth = new Auth($token, 3);
if( $auth->TrushClient() ){
    #reddirect jika jwt aktif
    header("Location: /");   
    exit();
}
?>
<?php 
#cek email

# validate user input
# 1 format email benar
$verify_email = (isset($_POST['email'])) ? StringValidation::EmailValidation($_POST['email']) : false;

if( isset( $_POST['submit'] ) && $verify_email ){
    $msg = true; #pesan untuk ditampilkan

    #verifikasi keapsahan email
    $verify =  new EmailAuth($_POST['email']);
    if( $verify->UserVerify() ){
        #header ke lokasi
        $link = $verify->KeyResult();
        header("Location: /p/auth/forgot-password/index.php?id=" . $link);   
        exit();
    }
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
    <title>Verifikasi Email</title>
    <meta name="description" content="sisteminformasi kesehatan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.html') ?>
    <style>
        body {
            background-color: #d2d6de;
        }
        main .container {
            background-color: #fff;    
            padding: 15px;        
            margin: 7% auto;
            width: 360px;
        }
        main .container .logo {
            margin-top: 10px
        }
        main .container .logo .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        main .container p{
            font-size: 24px;
            color: #666;
            text-align: center;
        }
        .form-groub{
            width: 100%;
            height: 30px;
            display: block;
            margin-bottom: 7px;      
            border: 1px solid #ccb8b8;
        } 
        input{
            width: 100%;
            max-width: 320px;
            margin: 6px 12px;
            padding: 0;
            border: 0;
            font-size: 14px
        }
        button{
            width: 100%;
            height: 32px;
        }

    </style>
</head>
<body>
    <main>
    <div class="container">
        <div class="logo">
            <img  class="center" src="/data/img/logo/logo-puskesmas.png" alt="logo" width="60px" height="60px">
        </div>
        <p>Verifikasai Email Pemulih</p>
        <?php if( isset( $msg ) ) :?>
            <p>Emali Verifikasi sudah dirim</p>
        <?php else: ?>
        <form action="" method="post">  
            <div class="body">
                <div class="form-groub">
                <input type="email" name="email" id="email-input" placeholder="masukan email terdaftar" autocomplete="off">
                </div>   
            </div> 
            <div class="footer">            
            <button type="submit" name="submit">kirim</button>
            </div>
        </form>
        <?php endif;?>
    </div>
    </main>
</body>
</html>
