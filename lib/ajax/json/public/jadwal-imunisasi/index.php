<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
    require_once BASEURL . '/lib/ajax/json/public/jadwal-imunisasi/controller/jadwalKIA.php';

    header_remove("Expires");
    header_remove("Pragma");
    header_remove("X-Powered-By");
    header_remove("Connection");
    header_remove("Server");
    header("Cache-Control:	private");
    header("Content-Type: application/json;charset=utf-8");

    $month = $_GET['month'] ?? date('m');
    $year  = $_GET['year'] ?? date('Y');

    $res = new jadwalKIA($month, $year);
    
    echo json_encode($res->getdata());
