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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot password</title>
    <style>
        input{
            display: block;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <main>
        <form action="" method="post">
            <p>masukan 6 digit kode keamana</p>
            <input type="text" name="validate" id="validate-input">

            <p>new password</p>

            <input type="password" name="password" id="password-input" placeholder="curent password">
            <input type="password" name="password2" id="password2-input" placeholder="new password">               
            <button type="submit" name="reset">reset password</button>
         </form>
    </main>
</body>
</html>
