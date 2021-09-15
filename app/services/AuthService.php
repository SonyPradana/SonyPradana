<?php

use Simpus\Apps\Service;
use Provider\Session\Session;

class AuthService extends Service
{
  public function __construct()
  {
    $this->error = new DefaultService();
  }

  public function login_status(array $request)
  {
    $status = Session::getSession()['auth'];

    if ($status['login']) {
      return array(
        'status'    => 'ok',
        'headers'   => ['HTTP/1.1 200 Oke']
      );

    } elseif (! $status['login']) {

      if ($status['token'] == '') {
        return array(
          'status'    => 'not login',
          'headers'   => ['HTTP/1.1 200 Oke']
        );
      }

      return array(
        'status'    => 'Session end',
        'headers'   => ['HTTP/1.1 200 Oke']
      );
    }
  }
}
