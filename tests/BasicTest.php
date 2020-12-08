<?php

namespace Simpus\Tests;

use PHPUnit\Framework\TestCase;

final class BasicTest extends TestCase
{
  public function testFrameworkStrucktur(): void
  {
    $this->assertFileExists('./lib/apps/init.php');

    $this->assertFileExists('./lib/apps/config/config.php');

    $this->assertFileExists('./lib/apps/controllers/autoload.php');
    $this->assertFileExists('./lib/apps/controllers/HomeController.php');
    $this->assertFileExists('./lib/apps/controllers/ApiController.php');

    $this->assertFileExists('./lib/apps/core/CLI.php');
    $this->assertFileExists('./lib/apps/core/Controller.php');
    $this->assertFileExists('./lib/apps/core/Middleware.php');
    $this->assertFileExists('./lib/apps/core/Router.php');

    $this->assertFileExists('./lib/apps/core/template/controller');
    $this->assertFileExists('./lib/apps/core/template/model');
    $this->assertFileExists('./lib/apps/core/template/service');
    $this->assertFileExists('./lib/apps/core/template/view');

    $this->assertFileExists('./lib/apps/library/autoload.php');
    $this->assertFileExists('./lib/apps/library/System/Database/CrudInterface.php');
    $this->assertFileExists('./lib/apps/library/System/Database/MyCRUD.php');
    $this->assertFileExists('./lib/apps/library/System/Database/MyModel.php');
    $this->assertFileExists('./lib/apps/library/System/Database/MyPDO.php');
    
    $this->assertFileExists('./lib/apps/services/autoload.php');    

    $this->assertFileExists('./lib/apps/views/');    
    $this->assertFileExists('./lib/apps/views/default/error.template.php');    
    $this->assertFileExists('./lib/apps/views/home/index.template.php');
    
    $this->assertFileExists('./public/index.php');

    $this->assertFileExists('./.env.example');
    $this->assertFileExists('./composer.json');
    $this->assertFileExists('./composer.lock');
    $this->assertFileExists('./package-lock.json');
    $this->assertFileExists('./package.json');
    $this->assertFileExists('./simpus');
    $this->assertFileExists('./webpack.mix.js');
  }

  public function testAssetStrucktur(): void
  {
    // scss
    $this->assertFileExists('./lib/scss/cards.scss');
    $this->assertFileExists('./lib/scss/full.style.scss');
    $this->assertFileExists('./lib/scss/style.scss');
    $this->assertFileExists('./lib/scss/widgets.scss');

    // js
    $this->assertFileExists('./lib/js/index.js');
    $this->assertFileExists('./lib/js/bundles/keepalive.js');
    $this->assertFileExists('./lib/js/bundles/message.js');
    // $this->assertFileExists('./public/lib/js/bundles.js');    
  }
}
