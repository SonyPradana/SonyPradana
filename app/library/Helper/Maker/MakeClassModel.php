<?php

namespace Helper\Maker;

use System\Database\MyPDO;
use System\Database\MyQuery;
use System\Template\Generate;
use System\Template\Method;
use System\Template\MethodPool;

class MakeClassModel
{
  public static function render(string $name, ?string $table_name): string
  {
    $table_name ??= '--';
    $class = new Generate($name);

    // read data from databse
    $table_column = MyQuery::conn("COLUMNS", MyPDO::conn("INFORMATION_SCHEMA"))
      ->select()
      ->equal("TABLE_SCHEMA", DB_NAME)
      ->equal("TABLE_NAME", $table_name)
      ->all() ?? [];

    $class
      ->tabIndent('  ')
      ->namespace('Model\\' . ucfirst($name))
      ->uses(
        [
          'System\Database\MyCRUD',
          'System\Database\MyPDO'
        ]
      )
      ->extend('MyCRUD')
      ->methods(function(MethodPool $function) use ($table_column, $table_name) {
        // getter
        $function
          ->name('getID')
          ->visibility(Method::PUBLIC_)
          ->body('return $this->ID[\'id\'] ?? null;')
        ;

        // setter
        $function
          ->name('setID')
          ->visibility(Method::PUBLIC_)
          ->addParams('$val')
          ->body(
            [
              '$this->ID = array(',
              '  \'id\' => $val',
              ');',
              'return $this;'
            ]
          )
        ;

        $columns = [];
        foreach ($table_column as $column) {
          $column_name = $column['COLUMN_NAME'];
          if ($column_name == 'id') {
            continue;
          }

          // filed model map
          $columns[] = "  '$column_name'\t=> null,";

          // getter from table
          $function
            ->name($column_name)
            ->visibility(Method::PUBLIC_)
            ->body("return " . "$" . "this->COLUMNS['$column_name'];")
          ;

          // setter from table
          $function
            ->name('set' . ucfirst($column_name))
            ->visibility(Method::PUBLIC_)
            ->params(['$val'])
            ->body(
              [
                "$" . "this->COLUMNS['$column_name'] = " . "$" . "val;",
                'return $this;'
              ]
            )
          ;
        }

        // constructor
        $function
          ->name('__construct')
          ->visibility(Method::PUBLIC_)
          ->body(
            [
              '$this->PDO = MyPDO::getInstance();',
              '$this->TABLE_NAME = \'' . $table_name . '\';',
              '$this->COLUMNS = array(',
                ...$columns,
              ');'
            ]
          )
        ;
      })
    ;

    // return
    return $class->generate();
  }
}
