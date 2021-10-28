<?php

namespace Helper\Maker;

use System\Template\Generate;

class MakeClassModels
{
  public static function render(string $name, ?string $table_name): string
  {
    $table_name ??= '--';
    $class = new Generate($name . 's');

    $class
      ->tabIndent('  ')
      ->namespace('Model\\' . ucfirst($name))
      ->uses(
        [
          'System\Database\MyModel',
          'System\Database\MyPDO'
        ]
      )
      ->extend('MyModel')
      ->addMethod('__construct')
        ->addParamComment('MyPDO', '$PDO', 'DataBase class Dependency Injection')
        ->params(['MyPDO $PDO = null'])
        ->body(
          [
            '$this->_TABELS[]  = "' . $table_name . '";',
            '$this->PDO = $PDO ?? MyPDO::getInstance();'
          ]
        )
    ;

    // return
    return $class->generate();
  }
}
