<?php
//config
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/config/config.php';

// Autoloading
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/library/autoload.php';
    
// router
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/Middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/Router.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/Controller.php';
