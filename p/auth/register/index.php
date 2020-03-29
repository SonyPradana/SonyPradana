<?php
#import modul 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/auth/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/library/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/config/DbConfig.php';
use StringValidation as sv;
?>
<?php
#authorization token
session_start();
$token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
$new_auth = new Auth($token, 2);
if( $new_auth->TrushClient() ){
    #redirect ke home page
    header("Location: /");   
    exit();
}
?>
<?php 
#main code

# identifikasi form input
$user_name = isset( $_POST['userName']) ? $_POST['userName'] : '';
$email = isset( $_POST['email'] ) ? $_POST['email'] : '';
$password = isset( $_POST['password'] ) ?$_POST['password'] : '';
$confirm_password = isset( $_POST['password2'] ) ? $_POST['password2'] : '';
$display_name = isset( $_POST['dispName'] ) ? $_POST['dispName'] : '';

if( isset( $_POST['submit'])){
    # verifikasi user input
    $verify_user_name = sv::UserValidation($user_name, 2, 32);
    $verify_email = sv::EmailValidation($email);
    $verify_password = sv::GoodPasswordValidation($password);
    $verify_display_name = sv::NoHtmlTagValidation($display_name);   

    # esekusi jika password sama, dan form input sudah benar
    if( $password === $confirm_password &&
        $verify_user_name && 
        $verify_email &&
        $verify_password &&
        $verify_display_name ){
        
        $data = ['userName' => $user_name,
                'email' => $email,
                'password' => $password,
                'dispName' => $display_name];
        #buat user baru
        $newUser = new Registartion($data);
        $veryNewUser = $newUser->Verify( $user_name, $email );
        #cek dan simpan
        if( $veryNewUser ==  4 ){ # emapt menujukan user dan email bemul digunkan
            if( $newUser->AddToArchive() ) { # file disimpan dipenampungan semntara 
                echo 'data berhasil disimpan, hubun<br>';
                $_POST = [];
                exit();
            }
        }
    }

    # message untuk user jika form input tidak tepat
    $msg_user = $verify_user_name  ? '' : 'User Name tidak diperbolehkan';
    $msg_email = $verify_email  ? '' : 'format Email tidak dizinkan';
    $msg_display_name = $verify_display_name  ? '' : 'Display Name tidak diperbolehkan';
    $msg_password = $verify_password  ? '' : 'password terlalu lemah';
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
    <title>Buat Acunt Baru</title>
    <meta name="description" content="sistem informasi kesehatan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">
       
    <style>
        body{background-color: #d2d6de;}
        form{
            padding: 0 20px;
        }
        input{
            display: block;
            margin-bottom: 10px;
            font-size: 17px;
        }        
        main .container {
            background-color: #fff;    
            padding: 15px;        
            margin: 7% auto;
            width: 60%;
            min-width: 320px;
            max-width: 900px;
            box-shadow: 0 4px 8px 0 #00000022, 0 6px 20px 0 #00000010;
            height: 450px;

            display: grid;
            grid-template-columns: 2fr 1fr;
        }
        .container .body.right p{
            font-size: 24px;
        }
        .container .body.left{
            border-left:  0.1px solid #ece9e9 ;
            padding: 10px;
        }
        .container .body.left .center{
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .container .body.left p{
            text-align: center;
            font-size: 20px
        }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <div class="body right">
                <p>Selamat Datang Di Sitem Informasi Majaemen Puskesmas Lerep</p>
                <form action="" method="post">
                    <label for="userName">User Name</label>
                    <input type="text" name="userName" id="userName-input" value="<?= $user_name  ?>">
                    <?= isset($msg_user) ? '<p>' . $msg_user . '</p>' : '' ?>
                
                    <label for="email">Email</label>
                    <input type="email" name="email" id="emali-input" value="<?= $email ?>">
                    <?= isset($msg_email) ? '<p>' . $msg_email . '</p>' : '' ?>
                    
                    <label for="dispName">Display name</label>
                    <input type="text" name="dispName" id="dispName-input" value="<?= $display_name ?>">
                    <?= isset($msg_display_name) ? '<p>' . $msg_display_name . '</p>' : '' ?>
                    
                    <label for="password">password</label>
                    <input type="password" name="password" id="password-input">
                    <?= isset($msg_password) ? '<p>' . $msg_password . '</p>' : '' ?>
                                
                    <label for="password2">Konfirm Password</label>
                    <input type="password" name="password2" id="password2-input">
                
                    <button type="submit" name="submit">Buat Akun</button>
                <?php  
                if( isset( $veryNewUser )){
                    if( $veryNewUser == 1){
                        echo '<p>user name telah digunakan</p>';
                    }elseif( $veryNewUser == 2){
                        echo '<p>email telah terdaftar</p>';
                    }else{
                        echo '<p>user name atau email telah digunakan</p> ';
                    }
                }                    
                ?>
                 </form>
            </div>
            <div class="body left">
                <div class="logo">
                    <img  class="center" src="/data/img/logo/logo-puskesmas.png" alt="logo" width="100px" height="100px">
                </div>
                
                <p>Bergabunglah untuk mendapatkan akses penuh dalam sinpus</p>
            </div>
        </div>
        
    </main>
</body>
</html>
