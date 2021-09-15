<?php

namespace Simpus\Tests;

use PHPUnit\Framework\TestCase;

final class BasicTest extends TestCase
{
  public function testFrameworkStrucktur(): void
  {
    $this->assertFileExists('./bootstrap/init.php');
    $this->assertFileExists('./bootstrap/autoload.php');

    $this->assertFileExists('./app/controllers/HomeController.php');
    $this->assertFileExists('./app/controllers/ApiController.php');

    $this->assertFileExists('./app/core/CLI.php');
    $this->assertFileExists('./app/core/Config.php');
    $this->assertFileExists('./app/core/Controller.php');
    $this->assertFileExists('./app/core/Router.php');
    $this->assertFileExists('./app/core/Service.php');

    $this->assertFileExists('./app/core/template/controller');
    $this->assertFileExists('./app/core/template/model');
    $this->assertFileExists('./app/core/template/models');
    $this->assertFileExists('./app/core/template/service');
    $this->assertFileExists('./app/core/template/view');
    $this->assertFileExists('./app/core/template/command');

    $this->assertFileExists('./app/library/System/Database/CrudInterface.php');
    $this->assertFileExists('./app/library/System/Database/MyCRUD.php');
    $this->assertFileExists('./app/library/System/Database/MyModel.php');
    $this->assertFileExists('./app/library/System/Database/MyPDO.php');

    $this->assertFileExists('./app/views/');
    $this->assertFileExists('./app/views/default/error.template.php');
    $this->assertFileExists('./app/views/home/index.template.php');

    $this->assertFileExists('./public/index.php');

    $this->assertFileExists('./.env.example');
    $this->assertFileExists('./CHANGELOG.MD');
    $this->assertFileExists('./composer.json');
    $this->assertFileExists('./composer.lock');
    $this->assertFileExists('./package-lock.json');
    $this->assertFileExists('./package.json');
    $this->assertFileExists('./simpus');
    $this->assertFileExists('./tailwind.config.js');
    $this->assertFileExists('./webpack.mix.js');
  }

  public function testAssetStrucktur(): void
  {
    // scss
    $this->assertFileExists('./resources/sass/cards.scss');
    $this->assertFileExists('./resources/sass/full.style.scss');
    $this->assertFileExists('./resources/sass/style.scss');
    $this->assertFileExists('./resources/sass/widgets.scss');

    // js
    $this->assertFileExists('./resources/js/index.js');
    $this->assertFileExists('./resources/js/bundles/keepalive.js');
    $this->assertFileExists('./resources/js/bundles/message.js');
    // $this->assertFileExists('./public/lib/js/bundles.js');
  }
}
