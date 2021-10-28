<?php

namespace Helper\Maker;

use System\Template\Generate;
use System\Template\Method;
use System\Template\MethodPool;
use System\Template\Property;
use System\Template\Providers\NewProperty;

class MakeClassCommand
{
  public static function render(string $class_name): string
  {
    $class = new Generate($class_name. 'Command');

    $class
      ->tabIndent("  ")
      ->use('System\Console\Command')
      ->extend('Command')

      // property
      ->propertys(
        NewProperty::name('command')
          ->visibility(Property::PUBLIC_)
          ->setStatic()
          ->addVaribaleComment('array')
          ->expecting(
            [
              '= array(',
              '  [',
              '    "cmd"       => "'. $class_name .'",',
              '    "mode"      => "full",',
              '    "class"     => ' . $class_name . 'Command::class,',
              '    "fn"        => "println",',
              '  ],',
              ')'
            ]
          )
      )

      // group funtion
      ->methods(function(MethodPool $function) use ($class_name) {
        $function
          ->name('printHelp')
          ->visibility(Method::PUBLIC_)
          ->body([
            'return array(',
            '  \'option\'   => array(),',
            '  \'argument\' => array()',
            ');'
          ]);

        $function
          ->name('println')
          ->visibility(Method::PUBLIC_)
          ->body([
            'echo $this->textGreen("' . $class_name . '");'
          ]);

      })
      ;

    return $class->generate();
  }


}
