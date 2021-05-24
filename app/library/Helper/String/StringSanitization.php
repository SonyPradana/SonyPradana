<?php

namespace Helper\String;

/**
 * htmlspecialchars -> khusus untuk password,karena tidak boleh men interupt password dr user
 * selain itu wajib hapus tanda baca, lower case, dan remove html tag
 */
class StringSanitization
{
  public static function removePunctuation(string $val): string
  {
    // $newStr = strtolower($val);
    $newStr = stripcslashes($val); # remove tanda baca
    // $newStr = htmlspecialchars($newStr);

    return $newStr;
  }

  public static function removeHtmlTags(string $val): string
  {
    $str = preg_replace("#<(.*)/(.*)>#iUs", "", $val);
    $str = strtolower($str);
    return $str;
  }

  public static function removeNumber(string $val): string
  {
    $str = preg_replace("/^[0-9]/", "", $val);
    $str = strtolower($val);
    return $str;
  }

  public static function removeWord(string $val): string
  {
    # remove no number character
    $str = preg_replace('/\D/', '', $val);
    return $str;
  }

  public static function forceDateFormate($val)
  {
    return $val;
  }
}
