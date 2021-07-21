<?php

namespace Simpus\Apps;

use DefaultService;
use Simpus\Apps\Middleware;
use Simpus\Auth\Auth;

abstract class Service extends Middleware
{
  const CODE_NO_CONTENT           = 204;
  const CODE_BAD_REQUEST          = 400;
  const CODE_UNAUTHORIZED         = 401;
  const CODE_FORBIDDEN            = 403;
  const CODE_NOT_FOUND            = 404;
  const CODE_METHOD_NOT_ALLOWED   = 405;

  /** @var DefaultService */
  protected DefaultService $error;

  public function __construct()
  {
    $this->error = new DefaultService();
  }

  /**
   * return error handel
   * prettier from error property
   *
   * @param int $error_code Error code (400, 403 or Services::CODE_NOT_FOUND)
   * @return array Array error provider
   */
  protected function error(int $error_code = 404): array
  {
    // to prevent parent::_construct() not declare
    $this->error = null ?? new DefaultService();

    // No Content
    if ($error_code === 204) {
      return $this->error->code_204();
    }

    // bad request
    if ($error_code === 400) {
      return $this->error->code_400();
    }

    // unauthorized
    if ($error_code === 401) {
      return $this->error->code_401();
    }

    // forbiden
    if ($error_code === 403) {
      return $this->error->code_403();
    }

    // method not allowed
    if ($error_code === 405) {
      return $this->error->code_405();
    }

    // default error
    return $this->error->code_404();
  }

  /**
   * handle error as a function on return array,
   * so can return error instanl
   *
   * @param int $error_code http error code (400, 401, 403, 404, 405)
   * @return void error handle
   */
  protected function errorHandler(int $error_code = 404): void
  {
    $repone     = $this->error($error_code);
    $headers[]  = array_values($repone['headers']);
    $headers[]  = 'Content-Type: application/json';

    respone($repone, $error_code, $headers)
      ->json()
      ->close();
  }

  /**
   * Authorize cek,
   * jika user belum login akses ditolak
   */
  protected function useAuth(): void
  {
    // cek access
    if ($this->getMiddleware()['auth']['login'] == false) {
      respone(
        // content
        array(
          'status'  => 'Unauthorized',
          'code'    => 401,
          'error'   => array(
            'server' => 'Unauthorized'
          ),
        ),

        // status code
        401,

        // headers
        array(
          'HTTP/1.0 401 Unauthorized',
          'Content-Type: application/json'
        )
      )
        ->json()
        ->close();
    }
  }

  protected function useUserRole(string $role): void
  {
    // cek access
    $token = Middleware::getMiddleware()['auth']['token'];
    $auth = new Auth($token, Auth::USER_NAME_AND_USER_AGENT_IP);

    if (! $auth->privilege($role)) {
      respone(
        // content
        array(
          'status'  => 'Unauthorized',
          'code'    => 401,
          'error'   => array(
            'server' => 'Unauthorized'
          ),
        ),

        // status code
        401,

        // headers
        array(
          'HTTP/1.0 401 Unauthorized',
          'Content-Type: application/json'
        )
      )
        ->json()
        ->close();
    }
  }
}
