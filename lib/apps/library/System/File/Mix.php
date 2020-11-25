<?php

namespace System\File;

class Mix
{
  public static function css(array $params)
  {
    header('Content-Type: text/css; charset="utf-8"', true, 200);
    $css_content = '/* css mixed by system */' ;
    foreach ($params as $css) {
      $css_content .= file_get_contents(BASEURL . $css);
    }

    echo $css_content;
  }
}
