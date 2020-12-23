<?php
use Simpus\Apps\Config;

// Autoloading
require_once __DIR__ . '/../bootstrap/autoload.php';

// Core
require_once __DIR__ . '/../lib/apps/core/Config.php';
require_once __DIR__ . '/../lib/apps/core/Middleware.php';
require_once __DIR__ . '/../lib/apps/core/Router.php';
require_once __DIR__ . '/../lib/apps/core/Controller.php';

// Declare Config Class
$config = new Config();
