<?php

use System\Router\AbstractMiddleware;

class SessionStartMiddleware extends AbstractMiddleware
{
  public function handle()
  {
    session_name('simpus');
    session_set_cookie_params([
      'secure' => true,
      'httponly' => true,
    ]);
    // seesion start
    session_start();
  }
}
