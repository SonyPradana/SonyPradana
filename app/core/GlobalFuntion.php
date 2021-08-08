<?php

if (! function_exists("dd")) {
  /**
   * Print prity value and close apilication
   * @param mixed $value
   *  Value to be print
   * @param mixed $values
   *  Values to be print
   * @return void
   *  Print and Die
   */
  function dd($value, ...$values): void {
    die(var_dump($value, ...$values));
  }
}

// path aplication

if (! function_exists("app_path")) {

  /**
   * Get full aplication path, base on config file
   *
   * @param string $folder_name
   *  Application path name
   * @param bool $include_basePath
   *  True to add base path to result
   * @return string
   *  Application path folder
   */
  function app_path(string $folder_name, bool $include_basePath = false): string {
    return $include_basePath
      ? (APP_FULLPATH[$folder_name] ?? '')
      : (APP_PATH[$folder_name] ?? '');
  }
}

if (! function_exists("model_path")) {

  /**
   * Get aplication model path, base on config file
   *
   * @param bool $include_basePath
   *  True to add base path to result
   * @return string
   *  Application path folder
   */
  function model_path(bool $include_basePath = false, string $surfix_path = ''): string {
    return $include_basePath
      ? (APP_FULLPATH['model'] ?? '') . $surfix_path
      : (APP_PATH['model'] ?? '') . $surfix_path;
  }
}

if (! function_exists("view_path")) {

  /**
   * Get aplication view path, base on config file
   *
   * @param bool $include_basePath
   *  True to add base path to result
   * @return string
   *  Application path folder
   */
  function view_path(bool $include_basePath = false, string $surfix_path = ''): string {
    return $include_basePath
      ? (APP_FULLPATH['view'] ?? '') . $surfix_path
      : (APP_PATH['view'] ?? '') . $surfix_path;
  }
}

if (! function_exists("controllers_path")) {

  /**
   * Get aplication controllers path, base on config file
   *
   * @param bool $include_basePath
   *  True to add base path to result
   * @return string
   *  Application path folder
   */
  function controllers_path(bool $include_basePath = false, string $surfix_path = ''): string {
    return $include_basePath
      ? (APP_FULLPATH['controllers'] ?? '') . $surfix_path
      : (APP_PATH['controllers'] ?? '') . $surfix_path;
  }
}

if (! function_exists("services_path")) {

  /**
   * Get aplication services path, base on config file
   *
   * @param bool $include_basePath
   *  True to add base path to result
   * @return string
   *  Application path folder
   */
  function services_path(bool $include_basePath = false, string $surfix_path = ''): string {
    return $include_basePath
      ? (APP_FULLPATH['services'] ?? '') . $surfix_path
      : (APP_PATH['services'] ?? '') . $surfix_path;
  }
}

if (! function_exists("component_path")) {

  /**
   * Get aplication component path, base on config file
   *
   * @param bool $include_basePath
   *  True to add base path to result
   * @return string
   *  Application path folder
   */
  function component_path(bool $include_basePath = false, string $surfix_path = ''): string {
    return $include_basePath
      ? (APP_FULLPATH['component'] ?? '') . $surfix_path
      : (APP_PATH['component'] ?? '') . $surfix_path;
  }
}

if (! function_exists("commands_path")) {

  /**
   * Get aplication commands path, base on config file
   *
   * @param bool $include_basePath
   *  True to add base path to result
   * @return string
   *  Application path folder
   */
  function commands_path(bool $include_basePath = false, string $surfix_path = ''): string {
    return $include_basePath
      ? (APP_FULLPATH['commands'] ?? '') . $surfix_path
      : (APP_PATH['commands'] ?? '') . $surfix_path;
  }
}

if (! function_exists("cache_path")) {

  /**
   * Get aplication cache path, base on config file
   *
   * @param bool $include_basePath
   *  True to add base path to result
   * @return string
   *  Application path folder
   */
  function cache_path(bool $include_basePath = false, string $surfix_path = ''): string {
    return $include_basePath
      ? (APP_FULLPATH['cache'] ?? '') . $surfix_path
      : (APP_PATH['cache'] ?? '') . $surfix_path;
  }
}

if (! function_exists("config_path")) {

  /**
   * Get aplication config path, base on config file
   *
   * @param bool $include_basePath
   *  True to add base path to result
   * @return string
   *  Application path folder
   */
  function config_path(bool $include_basePath = false, string $surfix_path = ''): string {
    return $include_basePath
      ? (APP_FULLPATH['config'] ?? '') . $surfix_path
      : (APP_PATH['config'] ?? '') . $surfix_path;
  }
}

if (! function_exists("base_path")) {

  /**
   * Get base path
   *
   * @param string $insert_path
   *  Insert string in end of path
   * @return string
   * Base path folder
   */
  function base_path(string $insert_path = ''): string {
    return BASEURL . $insert_path;
  }
}

// string

if (! function_exists("slugify")) {
  /**
   * Convert whole text to slug
   *
   * @param string $text
   *  Bad text to convert
   * @return string
   *  Clean slug text
   */
  function slugify(string $text): string {
    return \Helper\String\Manipulation::slugify($text);
  }
}

if (! function_exists("startsWith")) {
  /**
   * Cek Text start with with
   *
   * @param string $find
   *  Text to find
   * @param string $in
   *  Resource to find
   * @return bool
   *  True if text find in resouce
   */
  function startsWith(string $find, string $in): bool {
    return \Helper\String\Str::startWith($find, $in);
  }
}

if (! function_exists("stringContains")) {
  /**
   * Cek text exis on text
   *
   * @param string $find
   *  Text to find
   * @param string $in
   *  Resource to find
   * @return bool
   *  True if find text in text
   */
  function stringContains(string $find, string $in): bool
  {
    return \Helper\String\Str::contains($find, $in);
  }
}

// contoller

if (! function_exists("view")) {
  /**
   * Return view file and fill with data
   *
   * @param string $view_name
   *  View file locaation
   * @param array $portal
   *  Data to serve in view file
   * @return void
   *  Raw html
   */
  function view(string $view_name, $portal = [])
  {
    // short hand to access content
    if (isset($portal['contents'])) {
      $content = (object) $portal['contents'];
    }

    // require component
    require_once app_path('view', true) . $view_name . '.template.php';
  }
}

if (! function_exists("api_abort")) {
  /**
   * Abort Api
   *
   * @param int $status_code
   *  Abort with costume status code
   * @return void
   *  Abort as Api respone
   */
  function api_abort(int $status_code) {
    return ApiController::static()->index('Default', "code_$status_code", "1.1");
  }
}

if (! function_exists("abort")) {

  /**
   * Abort as respone
   *
   * @param int $status_code
   *  Abort with costume status code
   * @param array $option
   *  Abort with costume header
   * @return void
   *  Abort as html respone
   */
  function abort(int $status_code, array $option = array()) {
    switch ($status_code) {
      case 400:
        return DefaultController::page_400();
        break;

      case 401:
        return DefaultController::page_401($option);
        break;

      case 403:
        return DefaultController::page_403();
        break;

      case 404:
        return DefaultController::page_404($option);
        break;

      case 405:
        return DefaultController::page_405($option);
        break;

      default:
        # code...
        break;
    }
    return ApiController::static()->index('Default', "code_$status_code", "1.1");
  }
}

// timing

if (! function_exists('now')) {
  function now($time = "") {
    return \Provider\Time\Now::now($time);
  }
}

// broadcast

if (! function_exists('broadcast')) {
  /**
   * Send broadcast to send to clinets
   *
   * @param string $channels Channel name to sand to clinet
   * @param string $event Event name to sand to clinet
   * @param array $data Data array to sand nto clinet
   * @return array A data
   */
  function broadcast($channels, $event, $data) {
    return (new \System\Broadcast\Broadcast($channels, $event))->trigerPusher($data);
  }
}

// http

if (! function_exists('request')) {
  function request(): System\Http\Request
  {
    return (new System\Http\RequestFactory())->getFromGloball();
  }
}

if (! function_exists('response')) {
  function response($conten = '', int $respone_code = System\Http\Response::HTTP_OK, array $headers = []): System\Http\Response
  {
    return (new System\Http\Response($conten, $respone_code, $headers));
  }
}

// template

if (! function_exists('view')) {
  function view(string $view, array $portal = [])
  {
    \Simpus\Apps\Controller::renderView($view, $portal);
  }
}
