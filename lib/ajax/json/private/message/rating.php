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

# init componet ->url
$sender = $_SERVER['REMOTE_ADDR'];
$rating = isset( $_GET['rating'] ) ? $_GET['rating'] : null;
$max_rating = isset( $_GET['mrating'] ) ? $_GET['mrating'] : null;
$unit = isset( $_GET['unit'] ) ? $_GET['unit'] : null;

#header exit, jika url kosong
if( $sender == null || $rating == null || $max_rating == null || $unit == null){
    header("HTTP/1.1 400 Bad Request");
    echo '{"status":"bad gatway"}';
    exit();
}

$new_review = new Rating($sender, $rating, $max_rating, $unit);
#cek spam
if( $new_review->spamDetector() ){
    header("HTTP/1.1 403 Forbidden");
    echo '{"status":"forbidden"}';
    exit();
}

#jika tidak spam simpan dan kasih respone code 200
$new_review->kirimPesan();
header("HTTP/1.1 200 Ok");
echo '{"status":"ok"}';
exit();
