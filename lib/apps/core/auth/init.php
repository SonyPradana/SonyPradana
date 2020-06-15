<?php
spl_autoload_register(function( $class ){
    if (file_exists( $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/auth/controller/' . $class . '.php')){
        require_once  $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/auth/controller/' . $class . '.php';
    }
}, false);
