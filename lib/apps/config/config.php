<?php 
// global configurasi
define('BASEURL', $_SERVER['DOCUMENT_ROOT']);
date_default_timezone_set('Asia/Jakarta');

// memuat configurasi data-base local atau remote secara otomatis
if( file_exists(BASEURL . '/lib/apps/config/database_local.config.php') ){
    // DB local
    require_once BASEURL . '/lib/apps/config/database_local.config.php';
}else{
    // DB remote
    require_once BASEURL . '/lib/apps/config/database_remote.config.php';
}

// Konfigurasi header menu link 
require_once BASEURL . '/lib/apps/config/headermenu.config.php';
