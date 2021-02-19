<?php

namespace Simpus\Tests;

use PHPUnit\Framework\TestCase;
use Simpus\Apps\Route;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

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
    $start = microtime(true);
    $_SERVER['REQUEST_URI'] = '/router';
    $_SERVER['REQUEST_METHOD'] = 'GET';

    for ($i=0; $i < 10_000; $i++) {
      $app = new Route();
      $app->get('/router', function() {
        // estimate runing whole request 75ms
        sleep(0.075);
      });

      $app->run('/');
      $app = null;
    }
    $end = microtime(true);

    $this->assertLessThan(0.7, $end - $start);
  }

  public function testSpeedRouterWithRest(): void
  {
    $start = microtime(true);

    $client = new Client();
    $requests = function ($total) {
      $uri = '/';
      for ($i = 0; $i < $total; $i++) {
        yield new Request('GET', $uri);
      }
    };

    $pool = new Pool($client, $requests(1_000), [
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

    $this->assertLessThan(0.75, $end - $start);
  }
}
