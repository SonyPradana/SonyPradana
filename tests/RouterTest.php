<?php

namespace Simpus\Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Simpus\Apps\Router;

class RouterTest extends TestCase
{
  public function testFoundRouter(): void
  {
    $this->expectOutputString('hay');

    // logic
    $_SERVER['REQUEST_URI'] = '/found';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    Router::Reset();
    Router::get('/found', function() {
      echo 'hay';
    });

    Router::run('/');
  }

  public function testNotAllowRouter(): void
  {
    $this->expectOutputString('not allowed');

    // logic
    $_SERVER['REQUEST_URI'] = '/not-allowed';
    $_SERVER['REQUEST_METHOD'] = 'POST';

    Router::Reset();
    Router::get('/not-allowed', function($any) {
      echo 'hay';
    });

    Router::methodNotAllowed(function() {
      echo 'not allowed';
    });

    Router::run('/');
  }

  public function testNotFoundRouter(): void
  {
    $this->expectOutputString('/not-found');

    // logic
    $_SERVER['REQUEST_URI'] = '/not-found';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    Router::Reset();
    Router::get('/has-found', function($any) {
      echo 'hay';
    });

    Router::pathNotFound(function($path) {
      echo $path;
    });

    Router::run('/');
  }

  public function testSpeedRouter(): void
  {
    $watch_start = microtime(true);
    $keepRun = true;
    $routerCount = 0;
    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    while ($keepRun == true) {
      Router::Reset();
      Router::get('/', function() {
        return "router test speed";
      });
      Router::run('/');

      $routerCount++;

      if (microtime(true) - $watch_start > 1) {
        $keepRun = false;
      }
    }

    $this->assertGreaterThan(26_000, $routerCount);
  }

  public function testSpeedRouterWithRest(): void
  {
    $start = microtime(true);

    $client = new Client();
    $requests = function ($total) {
      $uri = 'localhost';
      for ($i = 0; $i < $total; $i++) {
        yield new Request('GET', $uri);
      }
    };

    $pool = new Pool($client, $requests(100), [
      'concurrency' => 5,
      'fulfilled' => function (Response $response, $index) {
        // this is delivered each successful response
      },
      'rejected' => function (RequestException $reason, $index) {
        // this is delivered each failed request
      },
    ]);
    // Initiate the transfers and create a promise
    $pool->promise()->wait();

    $end = microtime(true);

    $this->assertLessThan(0.99, $end - $start);
  }
}
