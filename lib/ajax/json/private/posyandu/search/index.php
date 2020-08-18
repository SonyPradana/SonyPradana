<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';

    session_start();
    $token = $_SESSION['token'] ?? '';
    $auth = new Auth($token, 2);

    HttpHeader::standartJsonHeader(true);
    
    $code_hash   = $_GET['idhash'] ?? null;
    $id_posyandu = $_GET['idposyandu'] ?? 0;
    
    if( !$auth->TrushClient() )HttpHeader::printJson([], 401);          // auth cek    
    if( $code_hash == null) HttpHeader::printJson([], 403);             // param cek
    
    $db =  new MyPDO();
    $data_posyandu = new PosyanduRecords($db);
    $data_posyandu
        ->filtterById( CCode::ConvertToCode($code_hash) )
        ->setStrictSearch( true );
        
    HttpHeader::printJson([
        "data" => $data_posyandu->result()
    ], 200);
