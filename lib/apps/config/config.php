<?php 
// global configurasi
define('BASEURL', __DIR__ . '/../../..');
date_default_timezone_set('Asia/Jakarta');

// load setting from .env
$dotenv = Dotenv\Dotenv::createImmutable(BASEURL);
$dotenv->load();

// konfigurasi pusher
$dotenv->required(['DB_HOST', 'DB_USER', 'DB_PASS']);
require_once BASEURL . '/lib/apps/config/pusher.config.php';

// konfigurasi database
$dotenv->required(['PUSHER_APP_ID', 'PUSHER_APP_KEY', 'PUSHER_APP_SECRET', 'PUSHER_APP_CLUSTER']);
require_once BASEURL . '/lib/apps/config/database.config.php';

// konfigurasi header menu link 
require_once BASEURL . '/lib/apps/config/headermenu.config.php';
