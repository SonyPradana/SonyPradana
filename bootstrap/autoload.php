<?php

  // autoload controller
  spl_autoload_register(function( $class ){
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $class);
    if (file_exists( BASEURL . '/lib/apps/controllers/' . $className . '.php')) {
      require_once  BASEURL . '/lib/apps/controllers/' . $className . '.php';
    }
  });

  // autoload library
  spl_autoload_register(function( $class ){
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $class);
    if (file_exists( BASEURL . '/lib/apps/library/' . $className . '.php')) {
        require_once  BASEURL . '/lib/apps/library/' . $className . '.php';
    }
  });

  // autoload services
  spl_autoload_register(function( $class ){
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $class);
    if (file_exists( BASEURL . '/lib/apps/services/' . $className . '.php')) {
        require_once  BASEURL . '/lib/apps/services/' . $className . '.php';
    }
  });
