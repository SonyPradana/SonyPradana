<?php

namespace Simpus\Database;

interface crudInterface
{
  public function cread(): bool;
  public function read(): bool;
  public function update(): bool;
  public function delete(): bool;

  public function isExist(): bool;
}
