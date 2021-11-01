<?php

namespace System\Collection;

class CollectionImmutable
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

  public function clear()
  {
    $this->collection = [];
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

  private function set(string $name, $value)
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

  public function dumb()
  {
    var_dump($this->collection);

    return $this;
  }
}

