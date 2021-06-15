<?php

namespace Helper\String;

class Str
{
  public static function startWith(string $find, string $in): bool
  {
    return substr($in, 0, strlen($find)) == $find;
  }

  public static function contains(string $find, string $in)
  {
    if (!strpos($find, $in)) {
      return true;
  }
  return false;
  }

  public static function fillText(string $text, int $lenght, string $fillWith)
  {
    $fill_lenght = $lenght - strlen($text);
    for ($i=0; $i < $fill_lenght; $i++) {
      $text = $fillWith . $text;
    }
    return $text;
  }
}
