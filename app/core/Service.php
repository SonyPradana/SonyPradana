<?php

namespace Simpus\Apps;

use DefaultService;
use Simpus\Apps\Middleware;
use Simpus\Helper\HttpHeader;

abstract class Service extends Middleware
{
  const CODE_BAD_REQUEST            = 400;
  const CODE_BAD_UNAUTHORIZED       = 401;
  const CODE_BAD_FORBIDDEN          = 403;
  const CODE_NOT_FOUND              = 404;
  const CODE_NOT_METHOD_NOT_ALLOWED = 405;

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
   * Authorize cek,
   * jika user belum login akses ditolak
   */
  protected function useAuth(): void
  {
    // cek access
    if ($this->getMiddleware()['auth']['login'] == false) {
      HttpHeader::printJson(
        // costume respone
        array(
          'status'  => 'Unauthorized',
          'code'    => 401,
          'error'   => array(
            'server' => 'Unauthorized'
          ),
        ),

        // respone code
        500,

        // costume header
        array(
          "headers" => array (
            'HTTP/1.0 401 Unauthorized',
            'Content-Type: application/json'
          )
      ));
    }
  }
}
