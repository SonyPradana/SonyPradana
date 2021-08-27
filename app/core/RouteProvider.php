<?php

namespace Simpus\Apps;

class RouterProvider
{

  private $routes = Array();

  public $patterns = Array (
    '(:id)'   => '(\d+)',
    '(:num)'  => '([0-9]*)',
    '(:text)' => '([a-zA-Z]*)',
    '(:any)'  => '([0-9a-zA-Z_+-]*)',
    '(:slug)' => '([0-9a-zA-Z_-]*)',
    '(:all)'  => '(.*)',
  );
  /**
   * Get routes has added
   * @return array Routes array
   */
  public function getRoutes()
  {
    return $this->routes;
  }

  /**
   * Function used to add a new route
   * @param array|string $method Methods allow
   * @param string $expression Route string or expression
   * @param callable $function Function to call if route with allowed method is found
   */
  public function match($method,string $uri, $callback)
  {
    $user_pattern   = array_keys($this->patterns);
    $allow_pattern  = array_values($this->patterns);
    $new_uri        = str_replace($user_pattern, $allow_pattern, $uri);
    $route = Array (
      'expression' => $new_uri,
      'function' => $callback,
      'method' => $method
    );

    return new RouteNamed($route, 'prefix-global');
  }

  /**
   * Function used to add a new route [method: get]
   * @param string $expression Route string or expression
   * @param callable $function Function to call if route with allowed method is found
   *
   */
  public function any(string $expression, $function)
  {
    return $this->match(['get','post', 'put', 'patch', 'delete', 'options'], $expression, $function);
  }

  /**
    * Function used to add a new route [method: get]
    * @param string $expression    Route string or expression
    * @param callable $function    Function to call if route with allowed method is found
    *
    */
  public function get(string $expression, $function)
  {
    return $this->match('get', $expression, $function);
  }

  /**
   * Function used to add a new route [method: post]
   * @param string $expression Route string or expression
   * @param callable $function Function to call if route with allowed method is found
   *
   */
  public function post(string $expression, $function)
  {
    return $this->match('post', $expression, $function);
  }

  /**
   * Function used to add a new route [method: put]
   * @param string $expression Route string or expression
   * @param callable $function Function to call if route with allowed method is found
   *
   */
  public function put(string $expression, $function)
  {
    return $this->match('put', $expression, $function);
  }

  /**
   * Function used to add a new route [method: patch]
   * @param string $expression Route string or expression
   * @param callable $function Function to call if route with allowed method is found
   *
   */
  public function patch(string $expression, $function)
  {
    return $this->match('patch', $expression, $function);
  }

  /**
   * Function used to add a new route [method: delete]
   * @param string $expression Route string or expression
   * @param callable $function Function to call if route with allowed method is found
   *
   */
  public function delete(string $expression, $function)
  {
    return $this->match('delete', $expression, $function);
  }

  /**
   * Function used to add a new route [method: options]
   * @param string $expression Route string or expression
   * @param callable $function Function to call if route with allowed method is found
   *
   */
  public function options(string $expression, $function)
  {
    return $this->match('options', $expression, $function);
  }
}
