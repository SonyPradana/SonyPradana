<?php
#import modul 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/auth/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/library/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/db/db_crud/DbConfig.php';
?>
<?php
#Aunt cek
session_start();
$token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
#$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1SWQiOjE0LCJ1TmFtZSI6ImFuZ2dlciIsImV4cHQiOjE1ODI3NTA0ODgsImlwIjoiOjoxIiwidUFnZW50IjoiTW96aWxsYVwvNS4wIChXaW5kb3dzIE5UIDEwLjA7IFdpbjY0OyB4NjQ7IHJ2OjczLjApIEdlY2tvXC8yMDEwMDEwMSBGaXJlZm94XC83My4wIn0.nrx0rBqneP1-ErORCIj6kBWzXXyIWKMVrSJRu1D5Sdo';
$test_auth = new Auth($token, 1);
if( $test_auth->TrushClient() == true){
    header("Location: /");   
    exit();
}
?>
<?php
# cek session bane 
$session_bane_fase = true;
# melihat sisa bane di session max: 5. 0 atrinya sedang dibane
$stat_bane = isset( $_SESSION['na'] ) ? $_SESSION['na'] : 5;
# melihat sisa waktu bane di session max: 2 menit
$exp_bane = isset( $_SESSION['to'] ) ? $_SESSION['to'] : time() - 1;
# reset timer & status bane, jika sudah melawati bane fase
if( $exp_bane < time() AND $stat_bane == 0){
    $stat_bane = 5;
    $exp_bane = time();
}

# cek sedang di in bane fase tidak
if( $stat_bane == 0 OR $exp_bane > time()){
    # bane fase
    $session_bane_fase = true;
}else{
    # no in bane fase
    $session_bane_fase = false;    
}
# login
$Verify_jwt = false; # default status login

# form di isi    
$user_name = isset( $_POST['userName'] ) ? $_POST['userName'] : '';
$password = isset( $_POST['password'] ) ? $_POST['password'] : '';   

if( isset( $_POST['login'] ) 
        && $user_name != ''
        && $password != '' ) {

    # verrifikasi user input
    # 1. format username benar 
    $validate_user_name = StringValidation::UserValidation($user_name, 2, 32);

    #cek dalam session bane tidak
    if( !$session_bane_fase && $validate_user_name ){
        Login::RefreshBaneFase($user_name);
        if( !Login::BaneFase($user_name) ){
            #login sucess
            $newLogin = new Login($user_name, $password);
            #simpan jWT jika login benar
            $_SESSION['token'] = $newLogin->JWTResult();
            $Verify_jwt = $newLogin->VerifyLogin();
        }
    }
    
    # session bane logic
    if( $Verify_jwt){
        #redirect ke url yg dituju jika ada
        $url = isset( $_GET['url'] ) ? $_GET['url'] : '/';
        header("Location: " .  $url );
        #reset before closing
        $stat_bane = 5;
        $exp_bane = time();        
        #simpan session bane
        $_SESSION['na'] = $stat_bane;
        $_SESSION['to'] = $exp_bane;
        exit();
    }else{
        #refresh session bane
        #kurangin kesempaan login saat user salah password
        $stat_bane = ($stat_bane < 1 ) ? 0 : $stat_bane - 1;
        if( $stat_bane < 1 AND $exp_bane < time() ){
            #kesempatan salah hanya ada 5x
            $exp_bane = time() + 180;
        }
    }
}
#simpan session bane
$_SESSION['na'] = $stat_bane;
$_SESSION['to'] = $exp_bane;
#Done
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <meta name="description" content="sistem informasi kesehatan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">
       
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
        .white-space{
            width: 100%;
            height: 30px;
            display: block;
            margin-bottom: 7px;      
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
            <p>Login SIMPUS</p>
            <?php if( !$session_bane_fase ) :?>
            <?='<script>console.log("'. $stat_bane . '")</script>'?>
            <form action="" method="post">  
                <div class="body">
                    <div class="form-groub">
                        <input type="text" name="userName" id="userName-input" placeholder="username" value="<?= $user_name ?>" maxlength="32" tabindex="1" autocomplete="off">
                    </div>   
                    <div class="form-groub">                
                        <input type="password" name="password" id="password-input" placeholder="Password" tabindex="2">
                    </div>    
                </div> 
                <div class="footer">
                    <button type="submit" name="login" tabindex="3">Login</button>
                    <?php if( isset($validate_user_name)) : ?>
                        <?= ( $validate_user_name == false ) ? '<p style="color: red;margin: 5px 0;">kombinasi username atau password tidak tepat</p>' : ''?>
                    <?php endif;?>  
                </div>
            </form>
            <?php else: ?>
            <?= '<p style="color:red;font-size=11px"> Anda sedang berda di bane fase : ' . ($exp_bane - time()) . '</p>'?>
            <?php endif; ?>
            <a href="/p/auth/forgot-password/email-verification">lupa kata sandi?</a>       

        </div>
    </main>
</body>
</html>
