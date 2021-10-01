<?php

namespace Simpus\Apps;

use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\AbstractAdapter;
use Symfony\Component\Cache\Adapter\AbstractTagAwareAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\PdoAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;

class Cache
{
  /**
   * Abstract adabter
   * @return AbstractTagAwareAdapter|AbstractAdapter
   *  Cache adabter symfony cache
   */
  public $cache_driver;
  private static Cache $self;

  /**
   * @param string $cache_driver
   *  By default cache driver type using env file
   */
  public function __construct(string $cache_driver = 'file')
  {
    $driver = $_ENV['CACHE_DRIVER'] ?? $cache_driver;
    $driver = strtolower($driver);

    // redis adapter
    if ($driver == 'redis') {
      // dsn config
      $port = REDIS_PORT != '' ? ':' . REDIS_PORT : '';
      $pwd  = REDIS_PASS == '' ? REDIS_PASS . '@' : '';
      $host = REDIS_HOST;

      $clinet = RedisAdapter::createConnection('redis://' . $pwd . $host . $port);
      $this->cache_driver = new RedisTagAwareAdapter($clinet);
      return;
    }

    if ($driver == 'memcached') {
      // dsn config
      $port = MEMCACHED_PORT != '' ? ':' . MEMCACHED_PORT : '';
      $pwd  = MEMCACHED_PASS == '' ? MEMCACHED_PASS . '@' : '';
      $host = MEMCACHED_HOST;

      $client = MemcachedAdapter::createConnection('memcached://' . $pwd . $host . $port);
      $this->cache_driver = new MemcachedAdapter($client);
      return;
    }

    if ($driver == 'pdo') {
      $this->cache_driver = new PdoAdapter(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME,
        '',
        0,
        array(
          'db_table' => 'zCache',
          'db_username' => DB_USER,
          'db_db_password' => DB_PASS,
        )
      );
      return;
    }

    // file system adapter [defalut adapter]
    $this->cache_driver = new FilesystemAdapter(
      '',
      0,
      APP_FULLPATH['cache'],
      null
    );

  }

  /**
   * Short hand to call cache class
   * @return AbstractTagAwareAdapter|AbstractAdapter
   *  Cache adabter symfony cache
   */
  public static function static()
  {
    $static = self::$self ?? new Cache();
    return $static->cache_driver;
  }

  /**
   * Short hand for cache->get()
   *
   * @param string $key
   *  The key for which to return the corresponding Cache Item.
   * @param int $second
   *  Set the expiration time in second.
   * @param mixed $data
   *  Set item tobe cache
   * @return mixed
   *  Cache item base kegived
   */
  public static function remember(string $key, int $second, $data)
  {
    return self::static()->get(
      $key,
      function(CacheItemInterface $item) use ($second, $data) {
        $item->expiresAfter($second);

        if (is_callable($data)) {
          return call_user_func_array($data, []);
        }
        return $data;
      }
    );
  }
}
