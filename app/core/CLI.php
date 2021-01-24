<?php

namespace Simpus\Apps;

use System\Database\MyPDO;

// TODO:
// 1. rapikan strukrur kode
//  a. pecah code jadi function-function kecil
//  b. buat function khusu untuk mangambil flag
class CLI
{
  public const CLI_VERSION = '0.2.1';
  private $BASE_DIR;
  private const MAKE_CONTROLLER = array (
    'template_location' => '/app/core/template/controller',
    'save_location' => '/app/controllers/',
    'pattern' => '__controller__',
    'surfix' => 'Controller.php'
  );
  private const MAKE_VIEW = array (
    'template_location' => '/app/core/template/view',
    'save_location' => '/app/views/',
    'pattern' => '__view__',
    'surfix' => '.template.php'
  );
  private const MAKE_SERVICE = array (
    'template_location' => '/app/core/template/service',
    'save_location' => '/app/services/',
    'pattern' => '__service__',
    'surfix' => 'Service.php'
  );
  private const MAKE_MODEL = array (
    'template_location' => '/app/core/template/model',
    'save_location' => '/app/library/Model/',
    'pattern' => '__model__',
    'surfix' => '.php'
  );
  private const MAKE_MODELS = array (
    'template_location' => '/app/core/template/models',
    'save_location' => '/app/library/Model/',
    'pattern' => '__models__',
    'surfix' => 's.php'
  );

  public function __construct(array $arguments, string $base_directory = __DIR__)
  {
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    $this->BASE_DIR = $base_directory;
    // throw to cli routing
    if (isset($arguments[1])) {
      $this->Routing($arguments[1], array($arguments[2] ?? '', $arguments[3] ?? ''));
    } else {
      $this->printHelp();
    }

  }

  private function Routing(string $main_argument, array $option)
  {
    if (substr( $main_argument, 0, 2) === "--") {
      // command for get flag
      $param = explode('--', $main_argument);

      switch ($param[1]) {
        case 'help':
          $this->printHelp();
          break;

        case 'version':
          echo 'cli vervion ' . self::CLI_VERSION;
          break;

        default:
          echo 'command not register';
          break;
      }
    } elseif (substr( $main_argument, 0, 5) === "make:") {
      // commad to make somethink
      $param = explode(':', $main_argument);
      if ($option[0] == '') {
        echo "argumnet name must set before\n";
        echo "--> php simpus make:option argument\n";
        exit;
      }

      switch ($param[1]) {
        case 'controller':
          echo "making controlller...\n";
          $makeController = $this->makeTemplate($option[0], self::MAKE_CONTROLLER);

          echo "making view...\n";
          $makeView = $this->makeTemplate($option[0], self::MAKE_VIEW);

          if ($makeController && $makeView) {
            echo "\nfinish created controller and view " . round(microtime(true) - APP_START, 4) . ' second';
          } else {
            echo "\n";
          }
          break;

        case 'view':
          echo "making view...\n";
          $makeView = $this->makeTemplate($option[0], self::MAKE_VIEW);

          if ($makeView) {
            echo "\nfinish created view ". round(microtime(true) - APP_START, 4) . ' second';
          } else {
            echo "\nfailed to creat view";
          }
          break;

        case 'service':
          echo "making service...\n";
          $makeService = $this->makeTemplate($option[0], self::MAKE_SERVICE);

          if ($makeService) {
            echo "\nfinish created service ". round(microtime(true) - APP_START, 4) . ' second';
          } else {
            echo "\nfailed to creat service";
          }
          break;

        case 'model':
          echo "making model...\n";
          $makeModel = $this->makeTemplate($option[0], self::MAKE_MODEL, $option[0] . '/');
          if ($makeModel) {
            echo "\nfinish created model ". round(microtime(true) - APP_START, 4) . ' second';
          } else {
            echo "\nfailed to creat model";
          }
          if (substr($option[1], 0, 12) == '--table-name') {
            $table_name = explode('=', $option[1])[1];
            $this->FillModelDatabase($this->BASE_DIR . '/app/library/Model/' . $option[0] . '/' . $option[0] . '.php', $table_name);
          }
          break;

        case 'models':
          echo "making models...\n";
          $makeModels = $this->makeTemplate($option[0], self::MAKE_MODELS, $option[0] . '/');
          if ($makeModels) {
            echo "\nfinish created model ". round(microtime(true) - APP_START, 4) . ' second';
          } else {
            echo "\nfailed to creat model";
          }
          if (substr($option[1], 0, 12) == '--table-name') {
            $table_name = explode('=', $option[1])[1];
            $this->FillModelsDatabase($this->BASE_DIR . '/app/library/Model/' . $option[0] . '/' . $option[0] . self::MAKE_MODELS['surfix'], $table_name);
          }
          break;

        default:
          echo "Argumet not allow\n";
          break;

      }
    } else {
      $this->printHelp();
    }
  }

  private function makeTemplate(string $argument, array $make_option, string $folder = ''): bool
  {
    if (file_exists($this->BASE_DIR . $make_option['save_location'] . $folder . $argument . $make_option['surfix'])) {
      echo "file alredy exis\n";
      return false;
    } elseif (! file_exists($this->BASE_DIR . $make_option['save_location'] . $folder)) {
        mkdir($this->BASE_DIR . $make_option['save_location'] . $folder);
    }

    $get_template = file_get_contents($this->BASE_DIR . $make_option['template_location']);
    $get_template = str_replace($make_option['pattern'], $argument,  $get_template);
    $isCopied = file_put_contents($this->BASE_DIR . $make_option['save_location'] . $folder . $argument . $make_option['surfix'], $get_template);

    return $isCopied === false ? false : true;
  }

  private function FillModelDatabase(string $model_location, string $table_name)
  {
    $pdo = new MyPDO('simpusle_simpus_lerep');
    $pdo->query(
      "SELECT
        *
      FROM
        INFORMATION_SCHEMA.COLUMNS
      WHERE
        TABLE_SCHEMA = :dbs AND TABLE_NAME = :table
    ");
    $pdo->bind(':dbs', 'simpusle_simpus_lerep');
    $pdo->bind(':table', $table_name);
    $pdo->execute();

    $table_column = $pdo->resultset() ?? exit;
    $to_string = '';
    foreach ($table_column as $column) {
      $to_string .= "'" . $column['COLUMN_NAME'] . "' => null,\n\t\t\t";
    }

    $getContent = file_get_contents($model_location);
    $getContent = str_replace('__table__', $table_name, $getContent);
    $getContent = str_replace('__column__', $to_string, $getContent);
    $isCopied   = file_put_contents($model_location, $getContent);

    return $isCopied === false ? false : true;
  }
  private function FillModelsDatabase(string $model_location, string $table_name)
  {
    $getContent = file_get_contents($model_location);
    $getContent = str_replace('__table__', $table_name, $getContent);
    $isCopied   = file_put_contents($model_location, $getContent);

    return $isCopied === false ? false : true;
  }

  private function printHelp()
  {
    // print all avlibale cmd
    echo "Welocome to simpus CLI\n";
    echo "Usage: \t php simpus [flag]\n";
    echo "       \t php simpus [option] [argumnet]\n";
    echo "avilable flag [flag]\n";
    echo "\t--help\t\tgeting help all command\n";
    echo "\t--version\tget version simpus cli\n";
    echo "avilable option [option] [classname]\n";
    echo "\tmake:controller [controller_name]\teasy way make controller and view\n";
    echo "\tmake:view [view_name]\t\t\teasy way make view\n";
    echo "\tmake:service [service_name]\t\teasy way make service\n";
    echo "\tmake:model [model_name] [flag]\teasy way make model\n";
    echo "\tmake:models [models_name] [flag]\teasy way make models\n";
    echo "\t\t\noptional: [flag]\n";
    echo "\t--table-name=[table_name]\tget table column when creating model\n";
  }
}
