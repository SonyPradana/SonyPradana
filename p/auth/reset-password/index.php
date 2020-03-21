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
$new_auth = new Auth($token, 2);
if( !$new_auth->TrushClient() ){
    header("Location: /p/auth/login");   
    exit();
}
?>
<?php 
# property
$user_name = $new_auth->getUserName();
$newUser = new User($user_name);
$display_name = $newUser->getDisplayName();


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

            header("Location: /p/auth/logout/index.php?url=/p/auth/login");    
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reset password</title>
    <style>
        input{
            display: block;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <main>
        <div>
            <p>Reset password</p>
            <p><?= $display_name ?></p>
            <?php if( isset($msg) ) : ?>
                <p style="color:red"><?= $msg ?></p>    
            <?php endif ;?>
            <form action="" method="post">
                <input type="password" name="password" id="password-input" placeholder="curent password">

                <input type="password" name="password2" id="password2-input" placeholder="new password">

                <input type="password" name="password3" id="password3-input" placeholder="confirm password">

                <button type="submit" name="reset">reset password</button>
            </form>
        </div>
    </main>
</body>
</html>
