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
    $new_auth = new Auth($token, 3);
    if( $new_auth->TrushClient() ){
        #reddirect jika jwt aktif
        header("Location: /");   
        exit();
    }
?>
<?php 
    #page ini bekerja dengan membaca url -> $_GET
    #ambil token/key/link
    $key = ( isset( $_GET['id'] ) ) ? $_GET['id'] : '';
    $code = ( isset( $_POST['validate'] ) ) ? $_POST['validate'] : '';
    if( isset( $_GET['id'] ) ){
        #cek dari form
        if( isset($_POST['reset']) 
            && isset($_POST['password']) 
            && isset($_POST['password2'])  ){
            
            $p1 = $_POST['password'];
            $p2 = $_POST['password2'];

            # verifikasi code dan password
            $verify_code = StringValidation::NumberValidation( $code, 6, 6 );
            # verifikasi password berkulitas
            $verify_psw = StringValidation::GoodPasswordValidation( $p1 );

            if( $p1 === $p2 && $verify_code && $verify_psw){
                $newPassword = new ForgotPassword($key, $code);
                $newPassword->NewPassword( $p1 );

                # self distruction link dan code
                $newPassword->deleteSection();
    
                #header ke login
                header("Location: /p/auth/login");   
                exit();                        
            }           
        }
        #ganti password 

    }else{
        # hanya yg punya id yg bisa masuk
        // header("Location: /");           
        header('HTTP/1.1 400 Bad Request');
        exit();
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
    <title>Forgot password</title>
    <meta name="description" content="sisteminformasi kesehatan puskesmas Lerep">
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
            <p>Buat Ulang Password</p>
            <form action="" method="post">
                <div class="body">
                    <div class="form-groub">
                        <input type="text" name="validate" id="validate-input" placeholder="masukan 6 digit kode keamana" maxlength="6">
                    </div>
                    <div class="white-space">
                    </div>
                    
                    <div class="form-groub">
                        <input type="password" name="password" id="password-input" placeholder="new password">
                    </div>
                    <div class="form-groub">
                        <input type="password" name="password2" id="password2-input" placeholder="confirm password">               
                    </div>
                </div>
                <div class="footer">
                    <button type="submit" name="reset">reset password</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
