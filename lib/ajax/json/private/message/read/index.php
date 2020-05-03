<?php
# import class
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';

# default header
header_remove("Expires");
header_remove("Pragma");
header_remove("X-Powered-By");
header_remove("Connection");
header_remove("Server");
header("Cache-Control:	private");
header("Content-Type: application/json; charset=utf-8");

# Aunt cek
session_start();
$token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
$auth = new Auth($token, 2);
if( !$auth->TrushClient() ){
    // reject jika tidak punya token
    header("HTTP/1.1 401 Unauthorized");
    echo '{"status":"unauthorized"}';
    exit();
}

# ambil parameter dari url
$start = isset( $_GET['s'] ) ? $_GET['s'] : null;
$resiver = "sonypradana@gmail.com";
if( $start == null || !is_numeric($start)){
    // reject jika sintaks tidak valid
    header("HTTP/1.1 400 Bad Request");
    echo '{"status":"bad request"}';
    exit();
}

# ambil data
$read_message = new ReadMessage();
$read_message->filterByPenerima($resiver);
$read_message->limitView(10);
$read_message->viewResiver(false);
$result = $read_message->bacaPesan(true);

# conert ke json
header("HTTP/1.1 200 ok");
echo $result;
