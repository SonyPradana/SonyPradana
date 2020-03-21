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
#cek email

# validate user input
# 1 format email benar
$verify_email = (isset($_POST['email'])) ? StringValidation::EmailValidation($_POST['email']) : false;

if( isset( $_POST['submit'] ) && $verify_email ){
    $msg = 'code email sudah dikirim'; #pesan untuk ditampilkan

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
</head>
<body>
    <main>
    <?php if( isset( $msg ) ): ?>
        <div>
            <p>Emali Verifikasi sudah dirim</p>
        </div>
    <?php else: ?>
        <div>
            <p>Verifikasai Email</p>
        </div>
        <form action="" method="post">
            <input type="email" name="email" id="email-input" placeholder="masukan email terdaftar">
            <button type="submit" name="submit">kirim</button>
        </form>
    <?php endif; ?>
    </main>
</body>
</html>
