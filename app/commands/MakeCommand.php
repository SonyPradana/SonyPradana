<?php

use Helper\Maker\MakeClassCommand;
use Helper\Maker\MakeClassController;
use Helper\Maker\MakeClassMiddleware;
use Helper\Maker\MakeClassModel;
use Helper\Maker\MakeClassModels;
use Helper\Maker\MakeClassServices;
use Helper\Maker\MakeClassView;
use System\Console\Command;

class MakeCommand extends Command
{

  public static array $command = array(
    [
      "cmd"       => "make",
      "mode"      => "start",
      "class"     => MakeCommand::class,
      "fn"        => "switcher",
    ],
  );

  public function printHelp()
  {
    return array(
      'option' => array(
        "\n\t" . $this->textGreen("make") . ":controller [controller_name]\t\tgenerate new controller and view",
        "\n\t" . $this->textGreen("make") . ":view [view_name]\t\t\t\tgenerate new view",
        "\n\t" . $this->textGreen("make") . ":service [services_name]\t\t\tgenerate new service",
        "\n\t" . $this->textGreen("make") . ":model [model_name] " . $this->textDim("[argument]") . "\t\tgenerate new model",
        "\n\t" . $this->textGreen("make") . ":models [models_name] " . $this->textDim("[argument]") . "\t\tgenerate new models",
        "\n\t" . $this->textGreen("make") . ":command [command_name] " . "\t\t\tgenerate new command",
        "\n\t" . $this->textGreen("make") . ":middleware [middleware_name] " . "\t\tgenerate new middleware",
      ),
      'argument' => array(
        "\n\t" . $this->textDim("--table-name=[table_name]") . "\tget table column when creating model/models",
      )
    );
  }

  public function switcher()
  {
    // get category command
    $makeAction = explode(':', $this->CMD);

    // get naming class
    if ($this->OPTION[0] == '') {
      echo "\tArgument name cant be null";
      echo "\n\t>>\t" . $this->textGreen("php") . " simpus " . $this->textGreen("make:") . $makeAction[1] . $this->textRed(" not_null");
      exit;
    }

    // stopwatch
    $watch_start = microtime(true);

    // find router
    switch ($makeAction[1]) {
      case 'controller':
        $this->make_controller();
        $this->make_view();
        break;

      case 'view':
        $this->make_view();
        break;

      case 'service':
        $this->make_servises();
        break;

      case 'model':
        $this->make_model();
        break;

      case 'models':
        $this->make_models();
        break;

      case 'command':
        $this->make_commad();
        break;

      case 'middleware':
        $this->make_middleware();
        break;

      default:
        echo $this->textRed("\nArgumnet not register");
        break;
    }

    // end stopwatch
    $watch_end = round(microtime(true) - $watch_start, 3) * 1000;
    echo "\nDone in " . $this->textYellow($watch_end ."ms\n");
  }

  public function make_controller()
  {
    echo $this->textYellow("Making controller file...");
    echo $this->textDim("\n...\n");

    // main code
    $template_location = controllers_path(true, $this->OPTION[0] . 'Controller.php');
    $success = !file_exists($template_location);

    if ($success) {
      $success = file_put_contents($template_location, MakeClassController::render($this->OPTION[0]));
    }

    // result
    if ($success) {
      echo $this->textGreen("\nFinish created controller\n");
    } else {
      echo $this->textRed("\nFailed Create controller\n");
    }
  }

  public function make_view()
  {
    echo $this->textYellow("Making view file...");
    echo $this->textDim("\n...\n");

    // main code
    $template_location = view_path(true, $this->OPTION[0] . '.template.php');
    $success = !file_exists($template_location);

    if ($success) {
      $success = file_put_contents($template_location, MakeClassView::render($this->OPTION[0]));
    }

    // result
    if ($success) {
      echo $this->textGreen("\nFinish created view file\n");
    } else {
      echo $this->textRed("\nFailed Create view file\n");
    }
  }

  public function make_servises()
  {
    echo $this->textYellow("Making service file...");
    echo $this->textDim("\n...\n");

    // main code
    $template_location = services_path(true, $this->OPTION[0] . 'Service.php');
    $success = !file_exists($template_location);

    if ($success) {
      $success = file_put_contents($template_location, MakeClassServices::render($this->OPTION[0]));
    }

    // result
    if ($success) {
      echo $this->textGreen("\nFinish created services file");
    }else {
      echo $this->textRed("\nFailed Create services file");
    }
  }

  public function make_model()
  {
    echo $this->textYellow("Making model file...");
    echo $this->textDim("\n...\n");

    // main code
    $template_folder = model_path(true, $this->OPTION[0]);
    $template_location = model_path(true, $this->OPTION[0] . '/' . $this->OPTION[0] . '.php');

    $success = !file_exists($template_folder)
      ? mkdir($template_folder)
      : !file_exists($template_location);

    if ($success) {
      // fill table name
      $table_name = substr($this->OPTION[1], 0, 12) == '--table-name'
        ? explode('=', $this->OPTION[1])[1]
        : null
      ;
      $success = file_put_contents($template_location, MakeClassModel::render($this->OPTION[0], $table_name));
    }

    // result
    if ($success) {
      echo $this->textGreen("\nFinish created model file");
    } else {
      echo $this->textRed("\nFailed Create model file");
    }
  }

  public function make_models()
  {
    echo $this->textYellow("Making models file...");
    echo $this->textDim("\n...\n");

    // main code
    $template_folder = model_path(true, $this->OPTION[0]);
    $template_location = model_path(true, $this->OPTION[0] . '/' . $this->OPTION[0] . 's.php');

    $success = !file_exists($template_folder)
      ? mkdir($template_folder)
      : !file_exists($template_location);

    if ($success) {
      // fill table name
      $table_name = substr($this->OPTION[1], 0, 12) == '--table-name'
        ? explode('=', $this->OPTION[1])[1]
        : null
      ;
      $success = file_put_contents($template_location, MakeClassModels::render($this->OPTION[0], $table_name));
    }

    // result
    if ($success) {
      echo $this->textGreen("\nFinish created models file");
    } else {
      echo $this->textRed("\nFailed Create models file");
    }
  }

  public function make_commad()
  {
    echo $this->textYellow("Making command file...");
    echo $this->textDim("\n...\n");

    // main code
    $config_location = config_path(true, 'command.config.php');
    $template_location = commands_path(true, $this->OPTION[0] . 'Command.php');
    $success = !file_exists($template_location);

    if ($success) {
      $success = file_put_contents($template_location, MakeClassCommand::render($this->OPTION[0]));
    }

    if ($success) {
      // add command config for calling new command
      $config = file_get_contents($config_location);
      $new_config = str_replace(
        "// more command here",

        "// " . $this->OPTION[0] . "\n\t" .
        $this->OPTION[0] . "Command::$"."command\n" .
        "\t// more command here",

        $config);

      $success = file_put_contents($config_location, $new_config);
    }

    // result
    if ($success) {
      echo $this->textGreen("\nFinish created command file");
    } else {
      echo $this->textRed("\nFailed Create command file");
    }
  }

  public function make_middleware()
  {
    echo $this->textYellow("Making middleware file...");
    echo $this->textDim("\n...\n");

    // main code
    $template_location = middleware_path(true, $this->OPTION[0] . 'Middleware.php');
    $success = !file_exists($template_location);

    if ($success) {
      $success = file_put_contents($template_location, MakeClassMiddleware::render($this->OPTION[0]));
    }

    // result
    if ($success) {
      echo $this->textGreen("\nFinish created middleware file");
    } else {
      echo $this->textRed("\nFailed Create middleware file");
    }
  }

}
