<?php

namespace Simpus\Tests;

use PHPUnit\Framework\TestCase;
use Simpus\Apps\Route;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Simpus\Apps\Controller;

class RouterTest extends TestCase
{
  public function testFoundRouter(): void
  {
    $this->expectOutputString('hay');

    // logic
    $_SERVER['REQUEST_URI'] = '/found';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    Route::get('/found', function() {
      echo 'hay';
    });

    Route::run('/');
  }

  public function testNotAllowRouter(): void
  {
    $this->expectOutputString('not allowed');

    // logic
    $_SERVER['REQUEST_URI'] = '/not-allowed';
    $_SERVER['REQUEST_METHOD'] = 'POST';

    Route::get('/not-allowed', function($any) {
      echo 'hay';
    });

    Route::methodNotAllowed(function() {
      echo 'not allowed';
    });

    Route::run('/');
  }

  public function testNotFoundRouter(): void
  {
    $this->expectOutputString('/not-found');

    // logic
    $_SERVER['REQUEST_URI'] = '/not-found';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    Route::get('/has-found', function($any) {
      echo 'hay';
    });

    Route::pathNotFound(function($path) {
      echo $path;
    });

    Route::run('/');
  }

  public function testSpeedRouter(): void
  {
    $watch_start = microtime(true);
    $keepRun = true;
    $routerCount = 0;
    $_SERVER['REQUEST_URI'] = '/';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    while ($keepRun == true) {
      $router = new Route();
      $router->get('/', function() {
        return "router test speed";
      });
      $router->run('/');

      $routerCount++;

      if (microtime(true) - $watch_start > 1) {
        $keepRun = false;
      }

      $router = null;
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

    $this->assertLessThan(1.15, $end - $start);
  }
}
