<?php
/**
 * kelas / function in berfungsi untuk 
 * memanggil kelas kelas yang lain yg dibutuhkan untuk 
 * memanggi semua service data library
 * 
 * @author sonypradana@gmail.com 
 */
spl_autoload_register(function( $class ){
    if (file_exists( $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/message/controller/' . $class . '.php')){
        require_once  $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/message/controller/' . $class . '.php';
    }
});
