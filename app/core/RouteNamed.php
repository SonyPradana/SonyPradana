<?php

namespace Simpus\Apps;

class RouteNamed
{
  private $route;

  public function __construct(array $route)
  {
    $route['name'] = 'global';
    $this->route = $route;
  }

  public function name(string $name)
  {
    $this->route['name'] =$name;
  }

  public function __destruct()
  {
    Router::addRoutes($this->route);
  }
}
