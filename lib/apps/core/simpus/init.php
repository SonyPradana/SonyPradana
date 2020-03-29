<?php
/**
 * kelas / function in berfungsi untuk 
 * memanggil celas celas yang lain yg dibutuhkan untuk 
 * memanggi semua service data sinpus rm
 * 
 * @author dony sonypradana@gmail.com 
 */
spl_autoload_register(function( $class ){
    if (file_exists( $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/simpus/controller/' . $class . '.php')){
        require_once  $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/simpus/controller/' . $class . '.php';
    }
});
