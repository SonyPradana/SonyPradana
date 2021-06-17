<?php

namespace Helper\String;

class Str
{
  public static function startWith(string $find, string $in): bool
  {
    return substr($in, 0, strlen($find)) == $find;
  }

  public static function contains(string $needle, string $haystack): bool
  {
    return '' === $needle || false !== strpos($haystack, $needle);
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
