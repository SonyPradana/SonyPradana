<?php
    use Simpus\Route;
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';

    $app = new Route();

    // home
    $app->get('/', function() {
        require_once BASEURL . '/p/home/index.php';
    });

    // auth    
    $app->match(['get', 'post'], '/login', function(){
        require_once BASEURL . '/p/auth/login/index.php';
    });  
    $app->match(['get', 'post'], '/logout', function(){
        require_once BASEURL . '/p/auth/logout/index.php';
    });  
    $app->match(['get', 'post'], '/profile', function(){
        require_once BASEURL . '/p/auth/profile/index.php';
    });
    $app->match(['get', 'post'], '/register', function(){
        require_once BASEURL . '/p/auth/register/index.php';
    });
    $app->match(['get', 'post'], '/reset-password', function(){
        require_once BASEURL . '/p/auth/reset-password/index.php';
    });

    // message
    $app->get('/messages/public', function(){
        require_once BASEURL . '/p/messages/public/index.php';
    });

    // halaman standar
    $app->get('/About', function(){
        require_once BASEURL . '/p/about/index.php';
    });    
    $app->get('/Ourteam', function(){
        require_once BASEURL . '/p/contact/ourteam/index.php';
    });
    $app->get('/Contactus', function(){
        require_once BASEURL . '/p/contact/contactus/index.php';
    });

    // info    
    $app->get('/info/([a-zA-Z0-9_-]*)', function($var1) {
        if( file_exists(BASEURL . '/p/info/' . $var1 .  '/index.php') ){            
            require_once BASEURL . '/p/info/' . $var1 . '/index.php';
        }else{
            header('HTTP/1.0 404 Not Found');
    
           require_once BASEURL . '/404.shtml';
        }
    });

    // default path 404, 405
    $app->pathNotFound(function($path) {
        header('HTTP/1.0 404 Not Found');

        require_once BASEURL . '/404.shtml';
    });
    $app->methodNotAllowed(function($path, $method) {
        header('HTTP/1.0 405 Method Not Allowed');

        echo '<h1>Error 405</h1>';
        echo '<p>The requested path "'.$path.'" exists. But the request method "'.$method.'" is not allowed on this path!</p>';
    });

    $app->run('/');
