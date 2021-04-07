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

    // send method type
    $params['x-method'] = $method;

    // send version request
    $params['x-version'] = $version;

    $response = $this->getService( $unit . 'Service', $action, [$params]);

    // get header and them remove header from response
    $headers = $response['headers'] ?? [];
    unset($response['headers']);

    // insert defult header
    array_push($headers, 'Content-Type: application/json');

    // respone as json
    Respone::print($response, 0, array(
      'headers' => array_merge(Respone::headers(), $headers)
    ));
  }

  private function getService($service_nama, $method_nama, $args = []) :array
  {
    $service_nama   = str_replace('-', '', $service_nama);
    $method_nama    = str_replace('-', '_', $method_nama);

    if (file_exists(APP_FULLPATH['services'] . $service_nama . '.php')) {
      $service = new $service_nama;
      if (method_exists($service, $method_nama)) {
        // call target services
        return call_user_func_array([$service, $method_nama], $args) ?? [];
      }

      // method not found
      return array(
        'status'  => 'Bad Request',
        'code'    => 400,
        'error'   => array(
          'server'  => 'Bad Request',
          'leyer'   => 1,
        ),
        'headers' => ['HTTP/1.1 400 Bad Request']
      );
    }

    // page not found
    return array(
      'status'  => 'Service Not Found',
      'code'    => 404,
      'error'   => array(
        'server'  => 'Service Not Found',
        'leyer'   => 1,
      ),
      'headers' => ['HTTP/1.1 404 Service Not Found']
    );
  }
}
