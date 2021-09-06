<?php

// Autoloading
require_once dirname(__DIR__) . '/bootstrap/autoload.php';

// Core
require_once dirname(__DIR__) . '/app/core/Config.php';
require_once dirname(__DIR__) . '/app/core/Cache.php';
require_once dirname(__DIR__) . '/app/core/Middleware.php';
require_once dirname(__DIR__) . '/app/core/RouteNamed.php';
require_once dirname(__DIR__) . '/app/core/Router.php';
require_once dirname(__DIR__) . '/app/core/RouteFactory.php';
require_once dirname(__DIR__) . '/app/core/RouteProvider.php';
require_once dirname(__DIR__) . '/app/core/Controller.php';
require_once dirname(__DIR__) . '/app/core/CLI.php';
require_once dirname(__DIR__) . '/app/core/Service.php';
require_once dirname(__DIR__) . '/app/core/GlobalFuntion.php';
require_once dirname(__DIR__) . '/app/core/TemplateEngine/Portal.php';
require_once dirname(__DIR__) . '/app/core/AbstractMiddleware.php';

// Declare Config Class
return new Simpus\Apps\Config(dirname(__DIR__) . '/app/config/');
