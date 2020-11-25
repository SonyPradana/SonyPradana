<?php

use Simpus\Apps\Controller;
use System\File\Mix;

class MixController extends Controller
{
  public function mix_css()
  {
    // load css ref
    $css_reference = array();
    if (isset($_GET['ref'])) { 
      $css_reference = array (
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

  public function mix_javascript()
  {
    // load js ref/page
    $js_reference = array();
    if (isset($_GET['ref'])) {
      $js_reference = array (
        '/lib/js/page/' . $_GET['ref']
      );
    }

    // load js mix request
    $mix_params = explode(';', $_GET['mix'] ?? 'index.min');
    $js_mix = array_map (
      function($js) {
        return '/lib/js/' . $js . '.js';
      }
    , $mix_params);

    return Mix::javacript(array_merge($js_reference, $js_mix));
  }
}
