<?php

namespace Helper\Maker;

use System\Template\Generate;
use System\Template\Method;
use System\Template\MethodPool;

class MakeClassServices
{
  public static function render(string $name): string
  {
    $class = new Generate($name . 'Service');

    $class
      ->tabIndent('  ')
      ->use('Simpus\Apps\Service')
      ->extend('Service')
      ->methods(function(MethodPool $function) {
        $function
          ->name('__construct')
          ->visibility(Method::PUBLIC_)
          ->body([
            'parent::__construct();',
            '// put your code here'
          ])
        ;

        $function
          ->name('Test')
          ->visibility(Method::PUBLIC_)
          ->setReturnType('array')
          ->params(['array $request'])
          ->body(
            [
              'return array(',
              '  \'status\'  => \'ok\',',
              '  \'code\'    => 200,',
              '  \'data\'    => null,',
              '  \'error\'   => false,',
              '  \'headers\' => array(\'HTTP/1.1 200 Oke\')',
              ');',
            ]
          )
        ;

      })
    ;

    // return
    return $class->generate();
  }
}
