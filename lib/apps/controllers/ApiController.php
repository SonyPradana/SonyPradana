<?php

use Simpus\Apps\Controller;
use Simpus\Helper\HttpHeader;

class ApiController extends Controller
{

    public function index($unit, $action)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $params = $_GET;
        if( $method == 'PUT' ){
            $body   = file_get_contents('php://input');
            $params = json_decode($body, true);
        }
        $result = $this->getService( $unit . 'Service', $action, [$params]);

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
        $service_nama   = str_replace('-', '', $service_nama);
        $method_nama    = str_replace('-', '_', $method_nama);

        if( file_exists( BASEURL . "/lib/apps/services/" . $service_nama . '.php') ){
            $service = new $service_nama;
            if( method_exists($service, $method_nama) ){
                return call_user_func_array([$service, $method_nama], $args);
            }
        }
        return [
            'status'  => 'Bad Request',
            'headers' => ['HTTP/1.1 400 Bad Request']
        ];
    }
}
