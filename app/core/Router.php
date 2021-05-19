<?php

namespace Simpus\Apps;

class Router
{
  private static $routes = Array();
  private static $pathNotFound = null;
  private static $methodNotAllowed = null;

  public static $patterns = Array (
    '(:id)'   => '(\d+)',
    '(:num)'  => '([0-9]*)',
    '(:text)' => '([a-zA-Z]*)',
    '(:any)'  => '([0-9a-zA-Z_+-]*)',
    '(:slug)' => '([0-9a-zA-Z_-]*)',
    '(:all)'  => '(.*)',
  );

  /**
   * Adding new router using array of router
   * @param array $route Router array format (expression, function, method)
   */
  public static function addRoutes(array $route)
  {
    if (isset($route['expression'])
    && isset($route['function'])
    && isset($route['method'])) {
      array_push(self::$routes, $route);
    }
  }

  /**
   * Get routes has added
   * @return array Routes array
   */
  public static function getRoutes()
  {
    return self::$routes;
  }

  /**
   * Reset all propery to be null
   */
  public static function Reset()
  {
    self::$routes = Array();
    self::$pathNotFound = null;
    self::$methodNotAllowed = null;
  }

  /**
   * Grouping routes using same prafix
   * @param sting $prefix Prefix of router exprestion
   * @return RouterFactory Function ti collact routes
   */
  public static function prefix(string $prefix)
  {
    return new RouterFactory($prefix);
  }

  /**
   * Function used to add a new route
   * @param array|string $method Methods allow
   * @param string $expression Route string or expression
   * @param callable $function Function to call if route with allowed method is found
   */
  public static function match($method, string $uri, $callback)
  {
    $user_pattern   = array_keys(self::$patterns);
    $allow_pattern  = array_values(self::$patterns);
    $new_uri        = str_replace($user_pattern, $allow_pattern, $uri);
    $route = Array (
      'expression' => $new_uri,
      'function' => $callback,
      'method' => $method
    );

    array_push(self::$routes, $route);
  }

  /**
   * Function used to add a new route [any method]
   * @param string $expression Route string or expression
   * @param callable $function Function to call if route with allowed method is found
   *
   */
  public static function any(string $expression, $function)
  {
    self::match(['get','post', 'put', 'patch', 'delete', 'options'], $expression, $function);
  }

  /**
   * Function used to add a new route [method: get]
   * @param string $expression Route string or expression
   * @param callable $function Function to call if route with allowed method is found
   *
   */
  public static function get(string $expression, $function)
  {
    self::match('get', $expression, $function);
  }

  /**
   * Function used to add a new route [method: post]
   * @param string $expression Route string or expression
   * @param callable $function Function to call if route with allowed method is found
   *
   */
  public static function post(string $expression, $function)
  {
    self::match('post', $expression, $function);
  }

  /**
   * Result when route expression not register/found
   * @param callable Function to be Call
   */
  public static function pathNotFound($function)
  {
    self::$pathNotFound = $function;
  }

  /**
   * Result when route method not match/allowed
   * @param callable Function to be Call
   */
  public static function methodNotAllowed($function)
  {
    self::$methodNotAllowed = $function;
  }

  /**
   * Run/execute routes
   * @param string $basepath Base Path
   * @param boolean $case_matters Cese sensitive metters
   * @param boolean $trailing_slash_matters Trailing slash matters
   * @param boolean $multimatch Return Multy route
   */
  public static function run($basepath = '', $case_matters = false, $trailing_slash_matters = false, $multimatch = false)
  {
    // The basepath never needs a trailing slash
    // Because the trailing slash will be added using the route expressions
    $basepath = rtrim($basepath, '/');

    // Parse current URL
    $parsed_url = parse_url($_SERVER['REQUEST_URI']);

    $path = '/';

    // If there is a path available
    if (isset($parsed_url['path'])) {
      // If the trailing slash matters
  	  if ($trailing_slash_matters) {
  		  $path = $parsed_url['path'];
  	  } else {
        // If the path is not equal to the base path (including a trailing slash)
        if ($basepath.'/' != $parsed_url['path']) {
          // Cut the trailing slash away because it does not matters
          $path = rtrim($parsed_url['path'], '/');
        } else {
          $path = $parsed_url['path'];
        }
  	  }
    }

    // Get current request method
    $method = $_SERVER['REQUEST_METHOD'];

    $path_match_found = false;

    $route_match_found = false;

    foreach (self::$routes as $route) {

      // If the method matches check the path

      // Add basepath to matching string
      if ($basepath != '' && $basepath != '/') {
        $route['expression'] = '('.$basepath.')'.$route['expression'];
      }

      // Add 'find string start' automatically
      $route['expression'] = '^'.$route['expression'];

      // Add 'find string end' automatically
      $route['expression'] = $route['expression'].'$';

      // Check path match
      if (preg_match('#'.$route['expression'].'#'.($case_matters ? '' : 'i').'u', $path, $matches)) {
        $path_match_found = true;

        // Cast allowed method to array if it's not one already, then run through all methods
        foreach ((array)$route['method'] as $allowedMethod) {
            // Check method match
          if (strtolower($method) == strtolower($allowedMethod)) {
            array_shift($matches); // Always remove first element. This contains the whole string

            if ($basepath != '' && $basepath != '/') {
              array_shift($matches); // Remove basepath
            }

            call_user_func_array($route['function'], $matches);

            $route_match_found = true;

            // Do not check other routes
            break;
          }
        }
      }

      // Break the loop if the first found route is a match
      if ($route_match_found && !$multimatch) {
        break;
      }

    }

    // No matching route was found
    if (!$route_match_found) {
      // But a matching path exists
      if ($path_match_found) {
        if (self::$methodNotAllowed) {
          call_user_func_array(self::$methodNotAllowed, Array($path,$method));
        }
      } else {
        if (self::$pathNotFound) {
          call_user_func_array(self::$pathNotFound, Array($path));
        }
      }
    }
  }

}
