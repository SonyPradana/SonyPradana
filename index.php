<?php
session_start();

use Simpus\Apps\Route;
use Simpus\Apps\Controller;
use Simpus\Apps\Middleware;
use Simpus\Auth\Auth;
use Simpus\Auth\User;

require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';

    $app   = new Route();
  $token = $_SESSION['token'] ?? '';
    $auth  = new Auth($token, 2);
    $user  = new User( $auth->getUserName() );
    Middleware::setMiddleware([
        "auth" => [
            "token"                 => $token,
            "login"                 => $auth->TrushClient(),
            "user_name"             => $auth->TrushClient() ? $auth->getUserName() : null,
            "display_name"          => $auth->TrushClient() ? $user->getDisplayName() : null,
            "display_picture_small" => $auth->TrushClient() ? $user->getSmallDisplayPicture() : null
        ],
        "DNT"       => isset( $_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] == 1 ? true : false,
        "before"    => function() use ($auth) {
            if( !$auth->TrushClient() ){  
                header("Location: /login?url=" . $_SERVER['REQUEST_URI']);  
                exit();
            }
        }
    ]);

    // home
    $app->get('/', function(){
        require_once BASEURL . '/lib/apps/controllers/HomeController.php';
        (new HomeController())->index();
    });

    // auth    
    $app->match(['get', 'post'], '/login', function(){
        require_once BASEURL . '/lib/apps/controllers/AuthController.php';
        (new AuthController())->login();
    });  
    $app->match(['get', 'post'], '/logout', function(){
        require_once BASEURL . '/lib/apps/controllers/AuthController.php';
        (new AuthController())->logout();
    });  
    $app->match(['get', 'post'], '/profile', function(){
        require_once BASEURL . '/lib/apps/controllers/AuthController.php';
        (new AuthController())->profile();
    });
    $app->match(['get', 'post'], '/register', function(){
        require_once BASEURL . '/lib/apps/controllers/AuthController.php';
        (new AuthController())->register();
    });
    $app->match(['get', 'post'], '/reset-password', function(){
        require_once BASEURL . '/lib/apps/controllers/AuthController.php';
        (new AuthController())->reset();
    });
    $app->match(['get', 'post'], '/forgot/(:text)', function(string $action){
        require_once BASEURL . '/lib/apps/controllers/AuthController.php';
        if( $action == 'reset' ){
            (new AuthController())->hardReset();
        }elseif( $action = 'send'){
            (new AuthController())->send();
        }else{            
            require_once BASEURL . '/lib/apps/controllers/DefaultController.php';
            (new DefaultController())->status(404, []);
        }
    });

    $app->get('/admin', function(){
        require_once BASEURL . '/lib/apps/controllers/AdminController.php';
        (new AdminController())->index  ();
    });


    // message
    $app->get('/messages/public', function(){
        require_once BASEURL . '/lib/apps/controllers/MessageController.php';
        (new MessageController())->public();
    });

    // halaman standar
    $app->get('/About', function(){
        require_once BASEURL . '/lib/apps/controllers/HomeController.php';
        (new HomeController())->about();
    });    
    $app->get('/Ourteam', function(){
        require_once BASEURL . '/lib/apps/controllers/ContactController.php';
        (new ContactController())->ourTeam();
    });
    $app->match(['get', 'post'], '/Contactus', function(){
        require_once BASEURL . '/lib/apps/controllers/ContactController.php';
        (new ContactController())->contactUs();
    });
    
    // info    
    $app->get('/info/(:any)', function(string $page) {
        if( Controller::view_exists('info/' . $page)){
            require_once BASEURL . '/lib/apps/controllers/InfoController.php';
            (new InfoController())->render('info/' . $page);
        }else{
            require_once BASEURL . '/lib/apps/controllers/DefaultController.php';
            (new DefaultController())->status(404, []);
        }
    });

    // unit kerja
    // rekam-medis
    $app->get('/rekam-medis', function(){
        require_once BASEURL . '/lib/apps/controllers/RekamMedisController.php';
        (new RekamMedisController())->index();
    });
    $app->match(['get', 'post'], '/rekam-medis/(:text)', function(string $page){
        if( Controller::view_exists('rekam-medis/' . $page) ){
            require_once BASEURL . '/lib/apps/controllers/RekamMedisController.php';
            (new RekamMedisController())->show( strtolower($page) );
        }else{
            require_once BASEURL . '/lib/apps/controllers/RekamMedisController.php';
            (new RekamMedisController())->index();
        }
    });
    // kia-anak biodata/posyandu
    $app->match(['get', 'post'], '/kia-anak/(:text)/(:text)', function(string $action, string $unit){
        if( Controller::view_exists('kia-anak/'. $unit . '/'. $action)){
            require_once BASEURL . '/lib/apps/controllers/KiaAnakController.php';
            (new KiaAnakController)->show($action, $unit);            
        }else{            
            require_once BASEURL . '/lib/apps/controllers/DefaultController.php';
            (new DefaultController())->status(404, []);
        }
    });

    // API
    $app->match(['get', 'put'], '/API/([0-9a-zA-Z.]*)/(:any)/(:any).json', function($access, $unit, $action){
        require_once BASEURL . '/lib/apps/controllers/ApiController.php';
        (new ApiController())->index($unit, $action);
    });

    // default path 404, 405
    $app->pathNotFound(function($path) {
        require_once BASEURL . '/lib/apps/controllers/DefaultController.php';
        (new DefaultController())->status(404, []);
    });
    $app->methodNotAllowed(function($path, $method) {
        require_once BASEURL . '/lib/apps/controllers/DefaultController.php';
        (new DefaultController())->status(405, ['path' => $path, 'method' => $method]);
    });

    $app->run('/');
