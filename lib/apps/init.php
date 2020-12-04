<?php
// Autoload Vendor
require_once __DIR__ . '/../../vendor/autoload.php';

//config
require_once __DIR__ . '/../../lib/apps/config/config.php';

// Autoloading
require_once __DIR__ . '/../../lib/apps/library/autoload.php';
require_once __DIR__ . '/../../lib/apps/controllers/autoload.php';
require_once __DIR__ . '/../../lib/apps/services/autoload.php';
    
// Core
require_once __DIR__ . '/../../lib/apps/core/Middleware.php';
require_once __DIR__ . '/../../lib/apps/core/Router.php';
require_once __DIR__ . '/../../lib/apps/core/Controller.php';
