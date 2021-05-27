<?php

namespace Simpus\Apps;
use Simpus\Apps\Middleware;

abstract class Controller extends Middleware
{

  public function view($view, $portal = [])
  {
    // short hand to access content
    if (isset($portal['contents'])) {
      $content = (object) $portal['contents'];
    }

    // require component
    require_once APP_FULLPATH['view'] . $view . '.template.php';

    // require js & css

    // requrie templates

    return $this;
  }

  public static function view_exists($view) :bool
  {
    return file_exists( APP_FULLPATH['view'] . $view . '.template.php');
  }

  /**
   * Shorthand to create new controller
   * @param string $controller Name of contorller
   * @param array $args Paramter to pass controller contractor
   * @return static Controller
   */
  public static function getController($contoller, $method, $args = [])
  {
    $contoller_location = APP_FULLPATH['controllers'] . $contoller . '.php';
    if (file_exists($contoller_location)) {
      require_once $contoller_location;
      $controller_name = new $contoller;
      if (method_exists($controller_name, $method)) {
        call_user_func_array([$controller_name, $method], $args);
        return;
      }
    }
  }

  /**
   * @var static This classs
   */
  private self $_static;

  /**
   * Instance of controller.
   * Shorthadn to crete new class
   */
  public static function static()
  {
    return self::$_static ?? new static;
  }
}
