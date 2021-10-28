<?php

namespace Helper\Maker;

use System\Template\Generate;
use System\Template\Method;
use System\Template\Providers\NewFunction;

class MakeClassController
{
  public static function render(string $class_name): string
  {
    $class = new Generate($class_name. 'Controller');

    $class
      ->tabIndent('  ')
      ->uses(
        [
          'System\Router\Controller',
          'Provider\Session\Session'
        ]
      )
      ->extend('Controller')
      ->methods(
        NewFunction::name('index')
          ->visibility(Method::PUBLIC_)
          ->body(
            [
              '$msg = array(\'show\' => false, \'type\' => \'info\', \'content\' => \'oke\');',
              '$error = array();',
              '',
              'return $this->view(\'/' . $class_name . '\', array(',
              '  "auth"          => Session::getSession()[\'auth\'],',
              '  "DNT"           => Session::getSession()[\'DNT\'],',
              '  "redirect_to"   => $_GET[\'redirect_to\'] ?? \'/\',',
              '  "meta"          => array(',
              '    "title"         => "Documnet Title",',
              '    "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",',
              '    "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"',
              '  ),',
              '  "header"        => array(',
              '    "active_menu"   => \'null\',',
              '    "header_menu"   => MENU_MEDREC',
              '  ),',
              '  "contents" => array (',
              '',
              '  ),',
              '  "error"   => $error,',
              '  "message" => array(',
              '    "show"      => $msg[\'show\'],',
              '    "type"      => $msg[\'type\'],',
              '    "content"   => $msg[\'content\']',
              '  )',
              '));',
            ]
          )
      )
    ;

    return $class->generate();
  }
}
