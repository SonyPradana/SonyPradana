<?php
# script ini digunakan hanya untuk proxy (perantar)
# link aslinya: https://api.kawalcorona.com/indonesia/provinsi/

# default header
header_remove("Expires");
header_remove("Pragma");
header_remove("X-Powered-By");
header_remove("Connection");
header_remove("Server");
header("Cache-Control:	private");
header("Content-Type: application/json; charset=utf-8");

# allow other website
header("Access-Control-Allow-Origin: *");

#get content
$url = "https://api.kawalcorona.com/indonesia/provinsi/";
$json = file_get_contents($url);

if( $json != ''){
    header("HTTP/1.1 200 Ok");
    echo $json;
    exit();
}

header("HTTP/1.1 500 Server error");
echo "[]";
