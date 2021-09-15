<?php

namespace Simpus\Apps;

abstract class Controller
{
  public function __invoke($invoke)
  {
    call_user_func([$this, $invoke]);
  }

  public function view(string $view, array $portal = [])
  {
    static::renderView($view, $portal);

    return $this;
  }

  public static function renderView(string $view, array $portal = [])
  {
    $auth = new \TemplateEngine\Portal($portal['auth'] ?? []);
    $meta = new \TemplateEngine\Portal($portal['meta'] ?? []);
    $content = new \TemplateEngine\Portal($portal['contents'] ?? []);
    $content_type = $portal['header']['content_type'] ?? 'Content-Type: text/html';

    // get render content
    ob_start();
    require_once view_path(true, $view . '.template.php');
    $html = ob_get_clean();

    // send render content to client
    response()
      ->setContent($html)
      ->setResponeCode(\System\Http\Response::HTTP_OK)
      ->setHeaders([$content_type])
      ->removeHeader([
        'Expires',
        'Pragma',
        'X-Powered-By',
        'Connection',
        'Server',
      ])
      ->send();
  }

  public static function view_exists($view) :bool
  {
    return file_exists(view_path(true) . $view . '.template.php');
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
