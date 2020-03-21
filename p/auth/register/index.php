<?php
#import modul 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/auth/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/library/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/db/db_crud/DbConfig.php';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Acunt Baru</title>
    <style>
        input{
            display: block;
        }
    </style>
</head>
<body>
    <main>
        <p>Selamat Datang Di Sitem Informasi Majaemen Puskesmas Lerep</p>
        <p>Bergabunglah untuk mendapatkan akses penuh dalam sinpus</p>
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
    </main>
</body>
</html>
