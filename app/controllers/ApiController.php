<?php

use Helper\Http\Respone;
use Simpus\Apps\Controller;

class ApiController extends Controller
{

  public function index($unit, $action, $version): void
  {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $params = $_GET;
    if ($method == 'PUT') {
      $body   = file_get_contents('php://input');
      $params = json_decode($body, true);
    } elseif ($method == 'POST') {
      $params = $_POST;
    }

    if (! empty($_FILES)) {
      $params['files'] = $_FILES;
    }
    // send version request
    $params['x-version'] = $version;

    $result = $this->getService( $unit . 'Service', $action, [$params]);

    // get header and them remove header from result
    $headers = $result['headers'] ?? [];
    unset($result['headers']);

    // insert defult header
    array_push($headers, 'Content-Type: application/json');

    // respone as json
    Respone::print($result, 0, array (
      'headers' => array_merge(Respone::headers(), $headers)
    ));
  }

  private function getService($service_nama, $method_nama, $args = []) :array
  {
    $service_nama   = str_replace('-', '', $service_nama);
    $method_nama    = str_replace('-', '_', $method_nama);

    if (file_exists( BASEURL . "/app/services/" . $service_nama . '.php')) {
      $service = new $service_nama;
      if (method_exists($service, $method_nama)) {
        return call_user_func_array([$service, $method_nama], $args);
      }
    }
    return array (
      'status'  => 'Bad Request',
      'headers' => ['HTTP/1.1 400 Bad Request']
    );
  }
}
