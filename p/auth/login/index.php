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

# form disiis
if( isset( $_POST['login'] ) 
        && isset( $_POST['userName'] )
        && isset( $_POST['password'] ) ) {
            
    $user_name = $_POST['userName'];
    $password = $_POST['password'];    

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
    input {
        display: block;
        margin: 5px 0;
    }

    </style>
</head>
<body>
    <main>
        <div>Welcome:</div>
        <?php if( !$session_bane_fase ) :?>
        <?= '<p> Kesempatan login Anda : ' . $stat_bane . '</p>'?>
        <form action="" method="post">          
            <input type="text" name="userName" id="userName-input" placeholder="User Name" autofocus>
            <input type="password" name="password" id="password-input" placeholder="Password">

            <button type="submit" name="login">Login</button>
            <?php if( isset($validate_user_name)) : ?>
                <?= ( $validate_user_name == false ) ? '<p style="color: red;margin: 5px 0;">kombinasi username atau password tidak tepat</p>' : ''?>
            <?php endif;?>  
        </form>
        <?php else: ?>
        <?= '<p> Anda sedang berda di bane fase : ' . ($exp_bane - time()) . '</p>'?>
        <?php endif; ?>
        <a href="/p/auth/forgot-password/email-verification">lupa kata sandi?</a>
    </main>
</body>
</html>
