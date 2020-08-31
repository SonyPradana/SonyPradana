<?php 
    #import modul 
    use Simpus\Auth\Auth;
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
?>
<?php
    # status 
    $status = 'Session end';
    $token = '';

    # sesoin cek    
    session_start();

    # token cek -> status user
    if( isset($_SESSION['token']) ){
        $token = $_SESSION['token'];
    }else{
        $status = 'not login';
    }

    # auth
    $auth =  new Auth($token, 2);
    if( $auth->TrushClient() ){
        $status = 'ok';
    }

$arr = ["status" => $status, "expt" => null];
header_remove("Expires");
header_remove("Pragma");
header_remove("X-Powered-By");
header_remove("Connection");
header_remove("Server");
header("Cache-Control:	private");
header("Content-Type: application/json; charset=utf-8");
echo json_encode($arr);

