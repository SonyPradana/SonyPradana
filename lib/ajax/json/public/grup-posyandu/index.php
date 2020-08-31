<?php
    use Simpus\Helper\HttpHeader;
    use Simpus\Simpus\GroupsPosyandu;
    
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';

    HttpHeader::standartJsonHeader();

    $desa = $_GET['desa'] ?? null;

    if( $desa == null){
        $grup_posyandu = GroupsPosyandu::getPosyanduAll();
    }else{
        $grup_posyandu = GroupsPosyandu::getPosyandu($desa);
    }

    HttpHeader::printJson(["data" => $grup_posyandu], 200);

