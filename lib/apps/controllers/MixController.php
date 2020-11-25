<?php

use Simpus\Apps\Controller;
use System\File\Mix;

class MixController extends Controller
{
  public function mix_css()
  {
    // load css rev
    $css_reference = array();
    if (isset($_GET['ref'])) { 
      $css_reference = array(
        '/lib/css/pages/v1.1/' . $_GET['ref'] . '.css'
      );
    }
    
    // Load css mix request
    $mix_params = explode(';', $_GET['mix'] ?? 'style');
    $css_mix = array_map (
      function($css) {
        return '/lib/css/ui/v1.1/' .$css. '.css';
      }
    , $mix_params);

    return Mix::css(array_merge($css_reference, $css_mix));
  }
}
