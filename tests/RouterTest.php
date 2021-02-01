<?php

namespace Simpus\Tests;

use NewsFeederService;
use PHPUnit\Framework\TestCase;
use Simpus\Apps\Route;
use System\Database\MyPDO;

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
    for ($i=0; $i < 1_000; $i++) {
      $_SERVER['REQUEST_URI'] = '/router';
      $_SERVER['REQUEST_METHOD'] = 'GET';

      $app = new Route();
      $app->get('/router', function() {
        $pdo = new MyPDO('test_simpus_lerep');
        (new NewsFeederService($pdo))->ResendNews([]);
      });

      $app->run('/');
      $app = null;
    }
    $end = microtime(true);

    $this->assertLessThan(0.7, $end - $start);
  }
}
