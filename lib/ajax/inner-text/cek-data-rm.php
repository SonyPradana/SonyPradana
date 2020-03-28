<?php
    #import modul 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/auth/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/library/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/simpus/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/db/db_crud/DbConfig.php';
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
    #ambil parameter dari url
    # n -> nama kk, a -> alamat, r -> nomor rt, w -> nomor rw
    if( isset($_GET['nm']) && 
        isset($_GET['a']) && 
        isset($_GET['r']) &&
        isset($_GET['w']) ) {
            #ambil data dari url
            $nama = $_GET['nm'];
            $alamat = $_GET['a'];
            $no_rt  = $_GET['r'];
            $no_rw = $_GET['w'];

            #ambil data dr data base
            $sData = new View_RM();
            #cari berdasarkan parameter
            $sData->filterByNama($nama);
            $sData->filterByAlamat($alamat);
            $sData->filterByRt($no_rt);
            $sData->filterByRw($no_rw);
            #costume filter
            $sData->forceLimitView(1);
            #result
            if( isset( $sData->result()[0] ) ){
                echo "1";
                exit();
            }
            echo "0";               
            }

        }
