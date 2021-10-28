<?php

namespace Simpus\Apps;

class Config
{
  /**
   * Load config from config folder/file
   * @param string $path Config path location
   */
  public function __construct(string $path)
  {
    // setup dotenv
    $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
    $dotenv->load();

    // load config
    $app_config = include($path . 'app.config.php');
    $dbs_config = include($path . 'database.config.php');
    $pusher_config = include($path . 'pusher.config.php');
    $headerMenu_config = include($path . 'headermenu.config.php');
    $redis_config = include($path . 'redis.config.php');
    $memcache_config = include($path . 'memcache.config.php');
    // excute config
    $this->appConfig($app_config);
    $this->databeseConfig($dbs_config);
    $this->pusherConfig($pusher_config);
    $this->headerMenuConfig($headerMenu_config);
    $this->redisConfig($redis_config);
    $this->memcacheConfig($memcache_config);
  }

  private function appConfig(array $config)
  {
      define('BASEURL', $config['BASEURL']);
      date_default_timezone_set($config['time_zone']);
      define('APP_PATH', [
        'model'       => $config['MODEL_PATH'],
        'view'        => $config['VIEW_PATH'],
        'controllers' => $config['CONTROLLER_PATH'],
        'services'    => $config['SERVICES_PATH'],
        'component'   => $config['COMPONENT_PATH'],
        'commands'    => $config['COMMNAD_PATH'],
        'cache'       => $config['CACHE_PATH'],
        'config'      => $config['CONFIG'],
        'middleware'  => $config['MIDDLEWARE'],
      ]);

      define('APP_FULLPATH', [
        'model'       => $config['BASEURL'] . $config['MODEL_PATH'],
        'view'        => $config['BASEURL'] . $config['VIEW_PATH'],
        'controllers' => $config['BASEURL'] . $config['CONTROLLER_PATH'],
        'services'    => $config['BASEURL'] . $config['SERVICES_PATH'],
        'component'   => $config['BASEURL'] . $config['COMPONENT_PATH'],
        'commands'    => $config['BASEURL'] . $config['COMMNAD_PATH'],
        'cache'       => $config['BASEURL'] . $config['CACHE_PATH'],
        'config'      => $config['BASEURL'] . $config['CONFIG'],
        'middleware'  => $config['BASEURL'] . $config['MIDDLEWARE'],
      ]);
  }

  private function databeseConfig(array $config)
  {
    define('DB_HOST', $config['DB_HOST']);
    define('DB_USER', $config['DB_USER']);
    define('DB_PASS', $config['DB_PASS']);
    define('DB_NAME', $config['DB_NAME']);
  }

  private function pusherConfig(array $config)
  {
    define('PUSHER_APP_ID', $config['PUSHER_APP_ID']);
    define('PUSHER_APP_KEY', $config['PUSHER_APP_KEY']);
    define('PUSHER_APP_SECRET', $config['PUSHER_APP_SECRET']);
    define('PUSHER_APP_CLUSTER', $config['PUSHER_APP_CLUSTER']);
  }

  private function headerMenuConfig(array $config)
  {
    // medical record header menu
    define('MENU_MEDREC', $config['MENU_MEDREC']);

    // kia-anak header meni
    define('MENU_KIA_ANAK', $config['MENU_KIA_ANAK']);

    // poasayndu header menu
    define('MENU_POSYANDU', $config['MENU_POSYANDU']);

  }

  private function redisConfig(array $config)
  {
    define('REDIS_HOST', $config['REDIS_HOST']);
    define('REDIS_PASS', $config['REDIS_PASS']);
    define('REDIS_PORT', $config['REDIS_PORT']);
  }

  private function memcacheConfig(array $config)
  {
    define('MEMCACHED_HOST', $config['MEMCACHED_HOST']);
    define('MEMCACHED_PASS', $config['MEMCACHED_PASS']);
    define('MEMCACHED_PORT', $config['MEMCACHED_PORT']);
  }
}
