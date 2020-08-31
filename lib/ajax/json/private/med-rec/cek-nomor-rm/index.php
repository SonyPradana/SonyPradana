<?php
    // import modul    
    use Simpus\Auth\Auth;
    use Simpus\Simpus\MedicalRecords;
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';

    // Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 2);

    // header 
    header_remove("Expires");
    header_remove("Pragma");
    header_remove("X-Powered-By");
    header_remove("Connection");
    header_remove("Server");
    header("Cache-Control:	private");
    header("Content-Type: application/json; charset=utf-8");

    // cek Aunt
    if( !$auth->TrushClient() ){
        // reject jika tidak punya token
        header("HTTP/1.1 401 Unauthorized");
        echo '{"status":"unauthorized"}';
        exit();
    }

    // kode utama:

    // ambil parameter dari url (GET)
    $nomor_rm = isset( $_GET['nr'] ) ? $_GET['nr'] : null;
   
    // cek request
    if( $nomor_rm == null){
        // reject sintaks yang tidak valid
        header("HTTP/1.1 400 Bad Request");
        echo '{"status":"bad request"}';
        exit();
    }

    // cari data ke data base
    $sData= new MedicalRecords();
    $sData->filterByNomorRm($nomor_rm);
    // configure filter
    $sData->forceLimitView(2);
    // result
    $res = $sData->maxData();
    header("HTTP/1.1 200 Ok");
    echo '{"status":"ok", "found":"' . $res .'"}';
