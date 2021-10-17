<?php

use System\Console\Command;

class DebugCommand extends Command
{

  public static array $command = array(
    [
      "cmd"       => "debug",
      "mode"      => "full",
      "class"     => DebugCommand::class,
      "fn"        => "println",
    ],
  );

  public function printHelp()
  {
    return array(
      'option' => array(
        "\n\t", $this->textGreen("debug") , $this->tabs(6), "run debug code"
      ),
      'argument' => array()
    );
  }

  public function println()
  {}
}
