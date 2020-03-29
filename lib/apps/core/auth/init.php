<?php
/**
 * Aunth             -- Verifikasi token ke server                + sarat: token & security lvl
 * EmailAunt         -- Membuat link dan Code                     + sarat: email
 *                           untuk lupa password 
 * ForgotPassword    -- Menyimpan/mengganti Password baru         + sarat: key --> encode
 * JsonWebToken      -- menyimpan informasi client                + sarat: header & payload -->encode
 *
 */
spl_autoload_register(function( $class ){
    // $class = explode('\\', $class);
    // $class = end($class);
    if (file_exists( $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/auth/controller/' . $class . '.php')){
        require_once  $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/auth/controller/' . $class . '.php';
    }
}, false);
