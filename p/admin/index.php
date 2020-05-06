<?php
#import modul 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/auth/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/library/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/simpus/init.php';
?>
<?php
#Aunt cek
session_start();
$token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
$auth = new Auth($token, 2);
$auth->authing();
if( !$auth->privilege('admin') ){
    echo 'You don’t have permission to access this page!';
    header('HTTP/1.1 403 Forbidden');
    exit();
}
?>
hellp world!!!
