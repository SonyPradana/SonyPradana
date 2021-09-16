<?php

// Autoloading
require_once dirname(__DIR__) . '/bootstrap/autoload.php';

// Core
require_once dirname(__DIR__) . '/app/core/Config.php';
require_once dirname(__DIR__) . '/app/core/Cache.php';
require_once dirname(__DIR__) . '/app/core/CLI.php';
require_once dirname(__DIR__) . '/app/core/Service.php';
require_once dirname(__DIR__) . '/app/core/GlobalFuntion.php';
require_once dirname(__DIR__) . '/app/core/TemplateEngine/Portal.php';

// Declare Config Class
return new Simpus\Apps\Config(dirname(__DIR__) . '/app/config/');
