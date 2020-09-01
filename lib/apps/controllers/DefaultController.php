<?php

use Simpus\Apps\Controller;

class DefaultController extends Controller{
    public function status($status_code, $args){        
        if( $status_code == 404){            
            header('HTTP/1.0 404 Page Not found');
            
            // result
            return $this->view('default/404', [
                "auth"    => $this->getMiddleware()['auth'],
                "meta"     => [
                    "title"         => "Page Not Found",
                    "discription"   => "Page Not Found",
                    "keywords"      => "405, error"
                ],
                "header"   => [
                    "active_menu"   => 'home',
                    "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
                ],
                "contents" => []
            ]);

        }elseif( $status_code == 405){
            header('HTTP/1.0 405 Method Not Allowed');

            // result
            return $this->view('default/405', [
                "auth"    => $this->getMiddleware()['auth'],
                "meta"     => [
                    "title"         => "Method Not Allow 405",
                    "discription"   => "Method Not Allow 405",
                    "keywords"      => "405, error"
                ],
                "header"   => [
                    "active_menu"   => 'home',
                    "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
                ],
                "contents" => [
                    "message"     =>  'The requested path "'. $args['path'] .'" exists. But the request method "' . $args['method'] . '" is not allowed on this path!'
                ]
            ]);
        }
    }
}
