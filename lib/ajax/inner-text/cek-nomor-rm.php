<?php
    #import modul 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/auth/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/library/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/simpus/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/config/DbConfig.php';
?>
<?php
    #Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 2);
    if( !$auth->TrushClient() ){  
        exit();
    }
?>
<?php
    # ambil parameter dari url
    if( isset($_GET['nr']) ){
        # ambil data dari url
        $nomor_rm = $_GET['nr'];

        # ambil data
        $cari_rm = new View_RM();
        $cari_rm->filterByNomorRm($nomor_rm);
        $cari_rm->forceLimitView(2);
        echo $cari_rm->maxData();
        exit;
    }
echo -1;
