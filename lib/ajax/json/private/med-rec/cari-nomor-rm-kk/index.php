<?php
    // import modul
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
    // n --> kk           a --> alamat       
    // r --> no rt        w --> no rw
    $nama_kk = isset( $_GET['n'] ) ? $_GET['n'] : null;
    $alamat = isset( $_GET['a'] ) ? $_GET['a'] : null;
    $rt = isset( $_GET['r'] ) ? $_GET['r'] : null;
    $rw = isset( $_GET['w'] ) ? $_GET['w'] : null;

    // cek request
    if( $nama_kk == null || $alamat == null || $rt == null || $rw == null ){        
        // reject sintaks yang tidak valid
        header("HTTP/1.1 400 Bad Request");
        echo '{"status":"bad request"}';
        exit();
    }

    // cari data ke data base
    $sData= new MedicalRecords();
    $sData->filterByNamaKK($nama_kk);
    $sData->filterByAlamat($alamat);
    $sData->filterByRt($rt);
    $sData->filterByRw($rw);
    // configure filter
    $sData->forceLimitView(1);
    // result
    if( isset($sData->result()[0]) ){
        $res = $sData->result()[0]['nomor_rm_kk'];
        header("HTTP/1.1 200 Ok");
        echo '{"status":"ok", "nomor_rm_kk":"' . $res .'"}';
        exit();
    }

    // no result (perintah berhasil tp tidak konten ditampilkan)
    // header("HTTP/1.1 204 No Content");
    echo '{"status":"no content", "nomor_rm_kk":""}';
