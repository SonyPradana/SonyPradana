<?php
session_start();

use Model\Trivia\Trivia;
use Simpus\Apps\{Route, Controller, Middleware};
use Simpus\Auth\{Auth, User};

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
            DefaultController::page_401(array (
                'links' => array (
                    array('Home Page', '/'),
                    array('Login',  '/login?url=' . $_SERVER['REQUEST_URI'])
                )
            ));
        }
    }
]);

// home
$app->get('/', function() {
    (new HomeController())->index();
});

// auth    
$app->match(['get', 'post'], '/login', function() {
    (new AuthController())->login();
});  
$app->match(['get', 'post'], '/logout', function() {
    (new AuthController())->logout();
});  
$app->match(['get', 'post'], '/profile', function() {
    (new AuthController())->profile();
});
$app->match(['get', 'post'], '/register', function() {
    (new AuthController())->register();
});
$app->match(['get', 'post'], '/reset-password', function() {
    (new AuthController())->reset();
});
$app->match(['get', 'post'], '/forgot/(:text)', function(string $action) {
    if ($action == 'reset') {
        (new AuthController())->hardReset();
    } elseif ( $action == 'send') {
        (new AuthController())->send();
    } else {
        DefaultController::page_404(array (
            'path' => '/forgot/'.$action
        ));
    }
});

$app->get('/admin', function() {
    (new AdminController())->index  ();
});


// message
$app->get('/messages/public', function() {
    (new MessageController())->public();
});

// halaman standar   
$app->get('/About', function() {
    (new HomeController())->about();
});    
$app->get('/Ourteam', function() {
    (new ContactController())->ourTeam();
});
$app->match(['get', 'post'], '/Contactus', function() {
    (new ContactController())->contactUs();
});

// info    
$app->get('/info/(:any)', function(string $page) {
    if (Controller::view_exists('info/' . $page)) {
        (new InfoController())->show( $page );
    } else {
        DefaultController::page_404(array (
            'path' => '/info/'.$page
        ));
    }
});

// aricle
$app->get('/read/(:any)', function(string $title) {
    // TODO: article exist cheacker before call index 
    (new ArticleController())->index($title);
});

// unit kerja
// rekam-medis
$app->get('/rekam-medis', function() {
    (new RekamMedisController())->index();
});
$app->match(['get', 'post'], '/rekam-medis/(:text)', function(string $page) {
    if (Controller::view_exists('rekam-medis/' . $page)) {
        (new RekamMedisController())->show( strtolower($page) );
    } else {
        (new RekamMedisController())->index();
    }
});
// kia-anak biodata/posyandu
$app->match(['get', 'post'], '/kia-anak/(:text)/(:text)', function(string $action, string $unit) {
    if (Controller::view_exists('kia-anak/'. $unit . '/'. $action)) {
        (new KiaAnakController)->show($action, $unit);            
    } else {            
        DefaultController::page_404(array (
            'path' => '/kia-anak/'.$action.'/'.$unit
        ));
    }
});

// API
$app->match(['get', 'put'], '/API/([0-9a-zA-Z.]*)/(:any)/(:any).json', function($version, $unit, $action) {
    (new ApiController())->index($unit, $action, $version);
});
// API - Mix
$app->get('/css/style.css', function(){
    (new MixController())->mix_css();
});

// Trivia
$app->match(array('get', 'post'), '/trivia/submit', function() {
    (new TriviaController())->submit();
});

// default path 404, 405
$app->pathNotFound(function($path) {
    DefaultController::page_404(array(
        'path' => $path
    ));
});
$app->methodNotAllowed(function($path, $method) {
    DefaultController::page_405(array (
        'path' => $path,
        'method' => $method
    ));
});

$app->run('/');
