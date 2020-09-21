<?php

    spl_autoload_register(function( $class ){
        $className = str_replace("\\", DIRECTORY_SEPARATOR, $class);
        if (file_exists( $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/controllers/' . $className . '.php')){
            require_once  $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/controllers/' . $className . '.php';
        }
    });
