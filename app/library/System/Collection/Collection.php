<?php

namespace System\Collection;

class Collection
{
  private array $collection = [];

  public function __construct(array $collection)
  {
    foreach ($collection as $key => $item) {
      $this->set($key, $item);
    }
  }

  public function __get($name)
  {
    return $this->get($name);
  }

  public function __set($name, $value)
  {
    return $this->set($name, $value);
  }

  public function clear()
  {
    $this->collection = [];
    return $this;
  }

  public function add(array $params)
  {
    $this->collection = array_merge($this->collection, $params);
    return $this;
  }

  public function all(): array
  {
    return $this->collection;
  }

  public function get(string $name, $default = null)
  {
    return $this->collection[$name] ?? $default;
  }

  public function remove(string $name)
  {
    if ($this->has($name)) {
      unset($this->collection[$name]);
    }

    return $this;
  }

  public function set(string $name, $value)
  {
    $this->collection[$name] = $value;
    return $this;
  }

  public function has(string $name)
  {
    return array_key_exists($name, $this->collection);
  }

  public function contain($item)
  {
    return in_array($item, $this->collection);
  }

  public function replace(array $new_collection)
  {
    $this->collection = $new_collection;
    return $this;
  }

  public function keys(): array
  {
    return array_keys($this->collection);
  }

  public function count(): int
  {
    return count($this->collection);
  }

  public function each(callable $callable)
  {
    foreach ($this->collection as $key => $item) {
      call_user_func($callable, $item, $key);
    }

    return $this;
  }

  /** its like array_map */
  public function every(callable $callable)
  {
    $new_collection = [];
    foreach ($this->collection as $key => $item) {
      $new_collection[$key] = call_user_func($callable, $item, $key);
    }

    $this->collection = $new_collection;

    return $this;
  }

  /** is like array_filter */
  public function with(callable $condition_true)
  {
    $new_collection = [];
    foreach ($this->collection as $key => $item) {
      $call = call_user_func($condition_true, $item, $key);
      $condition = is_bool($call) ? $call : false;

      if ($condition === true) {
        $new_collection[$key] = $item;
      }
    }

    $this->collection = $new_collection;

    return $this;
  }

  public function dumb()
  {
    var_dump($this->collection);

    return $this;
  }
}

