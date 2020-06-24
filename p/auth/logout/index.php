<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
    
#header
$url = $_GET['url'] ?? '/';
header("Location: " . $url );

session_start();
$token = $_SESSION['token'] ?? '';
$test_auth = new Auth($token, 2);
if( $test_auth->TrushClient() == true){
    echo '<h1>logout diterima, user berhasil logout</h1>';
    #logout data base
    $newLogout = new Logout($token);   
}
#logout seesion
unset($_SESSION["token"]);
