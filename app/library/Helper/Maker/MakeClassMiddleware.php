<?php

namespace Helper\Maker;

use System\Template\Generate;
use System\Template\Method;

class MakeClassMiddleware
{
  public static function render(string $name): string
  {
    $class = new Generate($name . 'Middleware');

    $class
      ->tabIndent('  ')
      ->use('System\Router\AbstractMiddleware')
      ->extend('AbstractMiddleware')
      ->addMethod('handle')
        ->visibility(Method::PUBLIC_)
        ->body('// do your stuff')
      ;
      // return
    return $class->generate();
  }
}
