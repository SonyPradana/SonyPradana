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

    public static function setMiddleware(array $middleware){
        self::$_middleware = (array) $middleware;
    }
    protected static function getMiddleware() :array{
        return (array) self::$_middleware;
    }
}
