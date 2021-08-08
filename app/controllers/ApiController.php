<?php

use Simpus\Apps\Controller;

class ApiController extends Controller
{
  public function index($unit, $action, $version): void
  {
    $response = $this->getService(
      $unit . 'Service',
      $action,
      request()
        ->with(['x-version' => $version])
        ->allIn()
    );

    response()
      ->setContent($response)
      ->setResponeCode($response['code'] ?? 200)
      ->setHeaders($response['headers'] ?? [])
      ->removeHeader([
        'Expires',
        'Pragma',
        'X-Powered-By',
        'Connection',
        'Server',
      ])
      ->json();
  }

  private function getService($service_nama, $method_nama, $args = []): array
  {
    $service_nama   = str_replace('-', '', $service_nama);
    $method_nama    = str_replace('-', '_', $method_nama);

    if (file_exists(services_path(true,  $service_nama . '.php'))) {
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
