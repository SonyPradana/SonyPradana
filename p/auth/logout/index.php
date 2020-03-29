<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/auth/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/config/DbConfig.php';

session_start();
$token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
$test_auth = new Auth($token, 2);
if( $test_auth->TrushClient() == true){
    echo 'logout diterima, user berhasil logout';
    #logout data base
    $newLogout = new Logout($token);   

    #logout seesion
    $_SESSION['token'] = '';

    #header
    $url = isset( $_GET['url'] ) ? $_GET['url'] : '/';
    header("Location: " .  $url );
    exit();
}else{
    echo 'kenapa harus logout, kamu siapa?';
}
