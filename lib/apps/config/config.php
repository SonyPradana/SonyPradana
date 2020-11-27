<?php 
// global configurasi
define('BASEURL', $_SERVER['DOCUMENT_ROOT']);
date_default_timezone_set('Asia/Jakarta');

// load setting from .env
$dotenv = Dotenv\Dotenv::createImmutable(BASEURL);
$dotenv->load();

// lonfigurasi pusher
$dotenv->required(['DB_HOST', 'DB_USER', 'DB_PASS']);
require_once BASEURL . '/lib/apps/config/pusher.config.php';

// konfigurasi database
$dotenv->required(['PUSHER_APP_ID', 'PUSHER_APP_KEY', 'PUSHER_APP_SECRET', 'PUSHER_APP_CLUSTER']);
require_once BASEURL . '/lib/apps/config/database.config.php';

// Konfigurasi header menu link 
require_once BASEURL . '/lib/apps/config/headermenu.config.php';
