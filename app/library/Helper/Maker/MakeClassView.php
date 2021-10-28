<?php

namespace Helper\Maker;

use System\Template\Generate;

class MakeClassView
{
  public static function render(string $name): string
  {
    $class = new Generate($name . 'Service');
    $template = file_get_contents(component_path(true, 'template/view.php'));

    $class->customizeTemplate($template);

    // return
    return $class->generate();
  }
}
