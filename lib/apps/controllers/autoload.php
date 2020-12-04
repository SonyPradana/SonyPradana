<?php

    spl_autoload_register(function( $class ){
        $className = str_replace("\\", DIRECTORY_SEPARATOR, $class);
        if (file_exists( BASEURL . '/lib/apps/controllers/' . $className . '.php')){
            require_once  BASEURL . '/lib/apps/controllers/' . $className . '.php';
        }
    });
