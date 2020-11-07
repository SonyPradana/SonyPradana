<?php
//config
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/config/config.php';

// Autoloading
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/library/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/controllers/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/services/autoload.php';
    
// Core
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/Middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/Router.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/Controller.php';

// Autoload Vendor
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
