<?php

namespace Simpus\Apps;

class Middleware
{
    private static $_middleware = [
        "auth" => [
            "login" => false,
            "user_name" => null,
            "display_name" => null,
            "display_picture_small" => null
        ]
    ];

    private static $hasSet = false;
    public static function setMiddleware(array $middleware){
        if (! self::$hasSet) {
            self::$_middleware = (array) $middleware;
            self::$hasSet = true;
        }
    }

    protected static function getMiddleware() :array{
        return (array) self::$_middleware;
    }
}
