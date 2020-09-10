<?php

use Simpus\Apps\Controller;
use Simpus\Helper\HttpHeader;

class ApiController extends Controller
{

    public function index($unit, $action)
    {
        $result = $this->getService( $unit . 'Service', $action, [$_GET]);

        // get header and them remove header from result
        $headers = $result['headers'] ?? [];
        unset($result['headers']);
        
        // isnsert defult header
        array_push($headers, 'Content-Type: application/json');

        HttpHeader::printJson($result, 0, [
            "headers" => $headers
        ]);
    }

    private function getService($service_nama, $method_nama, $args = []) :array
    {
        if( file_exists( BASEURL . "/lib/apps/services/" . $service_nama . '.php') ){
            require_once BASEURL . '/lib/apps/services/' . $service_nama . '.php';
            $service = new $service_nama;
            if( method_exists($service, $method_nama) ){
                return call_user_func_array([$service, $method_nama], $args);
            }
        }
        return [];
    }
}
