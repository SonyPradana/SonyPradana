<?php
#import modul 
use Simpus\Auth\Auth;
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
?>
<?php
#Aunt cek
$token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
$auth = new Auth($token, 2);
$auth->authing();
if( !$auth->privilege('admin') ){
    echo 'You don’t have permission to access this page!';
    header('HTTP/1.1 403 Forbidden');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Halaman Admin!!!</h1>
</body>
</html>
