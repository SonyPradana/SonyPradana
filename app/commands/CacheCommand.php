<?php

use Simpus\Apps\Cache;
use Simpus\Apps\Command;

class CacheCommand extends Command
{

  public static array $command = array(
    [
      "cmd"       => "cache",
      "mode"      => "full",
      "class"     => self::class,
      "fn"        => "println",
    ],
    [
      "cmd"       => "cache",
      "mode"      => "start",
      "class"     => self::class,
      "fn"        => "switcher",
    ],
  );

  public function println()
  {
    echo $this->textBlue("cache controll cli");
  }

  public function switcher()
  {
    // get category command
    $makeAction = explode(':', $this->CMD);

    // stopwatch
    $watch_start = microtime(true);

    // find router
    switch ($makeAction[1] ?? '') {

      case 'info':
        echo
          $this->textDim("cache driver"),
          "\t",
          $this->textBlue($_ENV["CACHE_DRIVER" ?? "not set"]);
        break;

      case 'clear':
        $prefix = $this->OPTION[0] ?? '';
        echo $this->textDim("clearing cache...\n");
        if ($prefix != '') {
          echo $this->textDim("with prefix - "),
            $this->textYellow($prefix), "\n";
        }

        Cache::static()->clear($prefix);
        break;

      default:
        echo $this->textRed("\nArgumnet not register");
        break;
    }

    // end stopwatch
    $watch_end = round(microtime(true) - $watch_start, 3) * 1000;
    echo "\nDone in " , $this->textYellow($watch_end ."ms\n");
  }

  public function printHelp()
  {
    return array(
      'option' => array(
        "\n\t" . $this->textGreen("cache") . ":info" . $this->tabs(5) . "Get cache information",
        "\n\t" . $this->textGreen("cache") . ":clear" . $this->tabs(5) . "Clear all cache",
        "\n\t" . $this->textGreen("cache") . ":clear [cache_prefix]" . $this->tabs(3) . "Clear cache with prefix items name",
      ),
      'argument' => array()
    );

  }

}
