<?php
#import modul 
use Simpus\Auth\Auth;
use Simpus\Auth\User;
use Simpus\Auth\ResetPassword;
use Simpus\Helper\StringValidation;
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
?>
<?php
#Aunt cek
$token = $_SESSION['token'] ?? '';
$auth = new Auth($token, 2);
if( !$auth->TrushClient() ){
    header("Location: /login");   
    exit();
}
?>
<?php 
# property
$user_name = $auth->getUserName();
$user = new User($user_name);
$display_name = $user->getDisplayName();


$msg = '';
if( isset( $_POST['reset']) ){
    $p1 = $_POST['password'];
    $p2 = $_POST['password2'];
    $p3 = $_POST['password3'];

    # validasi user input
    $verify_pass = StringValidation::GoodPasswordValidation($p2);


    if( $p2 === $p3 && $verify_pass) {
        $new_pass = new ResetPassword($user_name, $p1);

        if( $new_pass->passwordVerify() ){ 
            $new_pass->newPassword($p2);    

            header("Location: /logout?url=/login");    
            exit() ;
        }else{
            # password salah
            $msg = 'masukan kembali password Anda';
        }
    }else{     
        # konfirmasi password salah  
        $msg = $verify_pass ? 'konirmasi password salah' : 'password terlalu lemah';
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
    <title>reset password</title>
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
            <p>Reset password</p>
            <p><?= $display_name ?></p>
            <form action="" method="post">                
                <div class="body">
                    <div class="form-groub">
                        <input type="password" name="password" id="password-input" placeholder="curent password">
                    </div>

                    <div class="form-groub">
                        <input type="password" name="password2" id="password2-input" placeholder="new password">
                    </div>

                    <div class="form-groub">
                        <input type="password" name="password3" id="password3-input" placeholder="confirm password">
                    </div>
                </div>
                <div class="footer">
                    <button type="submit" name="reset">reset password</button>
                <?php if( isset($msg) ) : ?>
                    <p style="color:red"><?= $msg ?></p>    
                <?php endif ;?>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
