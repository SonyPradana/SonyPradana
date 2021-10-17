<?php

namespace System\Template;

class ConstPools
{
  private $pools = [];

  public function name(string $name)
  {
    return $this->pools[] = new Constant($name);
  }

  public function getPools(): array
  {
    return $this->pools;
  }

}
