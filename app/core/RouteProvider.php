<?php

namespace Simpus\Apps;

class RouteProvider
{

  private $routes = Array();

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
  public function match($method, string $uri, $callback)
  {
    $route = Array (
      'expression' => Router::mapPatterns($uri),
      'function' => $callback,
      'method' => $method
    );

    array_push($this->routes, $route);
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
