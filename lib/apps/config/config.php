<?php 
// global configurasi
define('BASEURL', $_SERVER['DOCUMENT_ROOT']);
date_default_timezone_set('Asia/Jakarta');

// load setting from .env
$dotenv = Dotenv\Dotenv::createImmutable(BASEURL);
$dotenv->load();

// lonfigurasi pusher
require_once BASEURL . '/lib/apps/config/pusher.config.php';

// konfigurasi database
require_once BASEURL . '/lib/apps/config/database.config.php';

// Konfigurasi header menu link 
require_once BASEURL . '/lib/apps/config/headermenu.config.php';

// default define this app
require_once BASEURL . '/lib/apps/config/define.config.php';
