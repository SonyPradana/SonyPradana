<?php

namespace Simpus\Helper;

class HttpHeader{

    public static function printJson($printed_data, $status_code = 200, array $headers = ['headers' => ['HTTP/1.1 404 Page Not Found']]){
        if( $status_code == 200 ){
            header("HTTP/1.1 200 Oke");
            $printed_data['status'] = 'ok';
        }elseif( $status_code == 401){
            header("HTTP/1.1 401 Unauthorized");
            $printed_data['status'] = 'unauthorized';
        }elseif( $status_code == 403){
            header("HTTP/1.1 403 Access Denied");
            $printed_data['status'] = 'access dinied';
        }else {
            // costume header
            foreach( $headers['headers'] as $header){
                header($header);
            }
        }

        echo json_encode($printed_data, JSON_NUMERIC_CHECK);
        exit();
    }

    public static function standartJsonHeader($debug = false){
        header_remove("Expires");
        header_remove("Pragma");
        header_remove("X-Powered-By");
        header_remove("Connection");
        header_remove("Server");
        header("Cache-Control:	private");
        if( !$debug ){
            header("Content-Type: application/json;charset=utf-8");
        }
    }
}
