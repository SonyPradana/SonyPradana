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
        echo $this->textDim("clearing cache...\n");
        Cache::static()->clear();
        break;

      default:
        echo $this->textRed("\nArgumnet not register");
        break;
    }

    // end stopwatch
    $watch_end = round(microtime(true) - $watch_start, 3) * 1000;
    echo "\nDone in " , $this->textYellow($watch_end ."ms\n");
  }



}
