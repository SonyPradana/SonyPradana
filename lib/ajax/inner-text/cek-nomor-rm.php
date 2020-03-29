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
        header("Location: /p/auth/login");   
        exit();
    }
?>
<?php
    # ambil parameter dari url
    if( isset($_GET['nr']) ){
        # ambil data dari url
        $nomor_rm = $_GET['nr'];

        # ambil data
        $sData = new View_RM();
        # cari berdasarkan parameter
        $sData->filterByNomorRm($nomor_rm);
        # costume filter
        $sData->forceLimitView(1);
        #result
        if( isset( $sData->result()[0] ) ){
            echo "1";
            exit();
        }
        echo "0";
    }
