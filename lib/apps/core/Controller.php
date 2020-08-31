<?php

namespace Simpus\Apps;

class Controller{
    protected $_template = BASEURL . '/lib/apps/views/StandartTempalte.php';
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

    public function template($tamplate_name){
        $file_location = BASEURL . '/lib/apps/views/' . $tamplate_name . '.php';
        if( !file_exists($file_location) ){
            $file_location = BASEURL . '/lib/apps/views/StandartTempalte.php';
        }
        $this->_template = $file_location;
        return $this;
    }

    public function view($view, $portal = []){
        // short hand to access content
        if( isset( $portal['contents'])){
            $content = (object) $portal['contents'];
        }
        
        // require component
        require_once BASEURL . "/lib/apps/views/" . $view . '.template.php';
        // require js & css

        // requrie templates

        return $this;
    }

    public static function view_exists($view) :bool{        
        return file_exists( BASEURL . "/lib/apps/views/" . $view . '.template.php');
    }
        
    public static function getController($contoller, $method, $args = []){
        $contoller          = ucfirst($contoller) . 'Controller';
        $contoller_location = BASEURL .'/lib/apps/controllers/' . $contoller . '.php';
        if( file_exists($contoller_location) ){
            require_once $contoller_location;
            $controller_name = new $contoller;
            if( method_exists($controller_name, $method) ){
                call_user_func_array([$controller_name, $method], $args);
                return;
            }
        }
    }
}
