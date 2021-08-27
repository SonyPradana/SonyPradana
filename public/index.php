<?php
session_name('simpus');
session_set_cookie_params(['secure' => true, 'httponly' => true,]);
session_start();

use Simpus\Apps\{Router, Controller, Middleware, RouteProvider};
use Simpus\Auth\{Auth, User};

require_once dirname(__DIR__) . '/vendor/autoload.php';

$token = $_SESSION['token'] ?? '';
$auth  = new Auth($token, Auth::USER_NAME_AND_USER_AGENT_IP);
$user  = new User($auth->getUserName());

Middleware::setMiddleware( array(
  "auth" => array(
    "token"                 => $token,
    "login"                 => $auth->TrushClient(),
    "user_name"             => $auth->getUserName(),
    "display_name"          => $user->getDisplayName(),
    "display_picture_small" => $user->getSmallDisplayPicture()
  ),
  "DNT" => isset($_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] == 1 ? true : false
));

// home
Router::get('/', function() {
  return (new HomeController())->index();
});

// auth
Router::match(['get', 'post'], '/login', function() {
  return (new AuthController())->login();
});
Router::match(['get', 'post'], '/logout', function() {
  return (new AuthController())->logout();
});
Router::match(['get', 'post'], '/profile', function() {
  return (new AuthController())->profile();
});
Router::match(['get', 'post'], '/register', function() {
  return (new AuthController())->register();
});
Router::match(['get', 'post'], '/reset-password', function() {
  return (new AuthController())->reset();
});
Router::match(['get', 'post'], '/forgot/(:text)', function(string $action) {
  if ($action == 'reset') {
    return (new AuthController())->hardReset();
  }
  if ($action == 'send') {
    return (new AuthController())->send();
  }
  return DefaultController::page_404(array(
    'path' => '/forgot/'.$action
  ));
});

Router::prefix('/admin')->routes(function(RouteProvider $routes) {
  $routes->get('', function() {
    return (new AdminController())->index();
  });

  $routes->get('/(:any)', function($any) {
    return (new AdminController())->index();
  });

  $routes->get('/(:any)/(:any)', function($any, $second_any) {
    return (new AdminController())->index();
  });
});

// message
Router::get('/messages/public', function() {
  return (new MessageController())->public();
});

// halaman standar
Router::get('/About', function() {
  return (new HomeController())->about();
});
Router::get('/Ourteam', function() {
  return (new ContactController())->ourTeam();
});
Router::match(['get', 'post'], '/Contactus', function() {
  return (new ContactController())->contactUs();
});

// info
Router::get('/info/(:any)', function(string $page) {
  if (Controller::view_exists('info/' . $page)) {
    return (new InfoController())->show( $page );
  }

  return DefaultController::page_404(array (
    'path' => '/info/'.$page
  ));
});

Router::get('/vaksinasi', function() {
  return (new VaksinasiController)->index();
});

// article
Router::get('/read/(:any)', function(string $title) {
  // TODO: article exist cheacker before call index
  return (new ArticleController())->index($title);
});

// unit kerja
// pendaftaran
Router::get('/pendaftaran', function() {
  return (new RegistrationMRController)->index();
});
// rekam-medis
Router::prefix('/rekam-medis')->routes(function(RouteProvider $routes) {
  $routes->get('', function() {
    return (new RekamMedisController())->index();
  });

  $routes->match(['get', 'post'], '/(:text)', function(string $page) {
    if (Controller::view_exists('rekam-medis/' . $page)) {
      return (new RekamMedisController())->show( strtolower($page) );
    }

    return DefaultController::page_404(array (
      'path' => '/rekam-medis/' . $page
    ));
  });
});
// kia-anak biodata/posyandu
Router::match(['get', 'post'], '/kia-anak/(:text)/(:text)', function(string $action, string $unit) {
  if (Controller::view_exists('kia-anak/'. $unit . '/'. $action)) {
    return (new KiaAnakController)->show($action, $unit);
  }

  return DefaultController::page_404(array (
    'path' => '/kia-anak/'.$action.'/'.$unit
  ));
});

// API
Router::any('/API/([0-9a-zA-Z.]*)/(:any)/(:any).json', function($version, $unit, $action) {
  return (new ApiController())->index($unit, $action, $version);
});

// Trivia
Router::match(array('get', 'post'), '/trivia/submit', function() {
  return (new TriviaController())->submit();
});

// Stories
Router::prefix('/stories')->routes(function(RouteProvider $route) {

  $route->get('', function() {
    return (new StoriesController)->index();
  });

  $route->get('/view/(:any)', function($storyID) {
    return (new StoriesController)->preview($storyID);
  });

  $route->get('/roll/(:any)', function($group_name) {
    return (new StoriesController)->roll($group_name);
  });
});

Router::get('/QnA', function() {
  return (new QuestionAnswerController)->index();
});
Router::prefix('/question')->routes(function(RouteProvider $route) {
  $route->get('/(:id)/(:slug)', function($thread_ID, $slug) {
    return (new QuestionAnswerController)->thread($thread_ID);
  });

  $route->get('/ask', function() {
    return (new QuestionAnswerController)->ask();
  });

  $route->get('/answer/(:id)', function($id) {
    return (new QuestionAnswerController)->answer($id);
  });
});

// sitemap generator
Router::get('/sitemap.(:text)', function($ext) {
  $ext = strtolower($ext);
  $respone = new SiteMapController();

  switch ($ext) {
    case "html":
    case "txt":
      return $respone->html();
      break;

    case "xml":
      return $respone->index();
      break;

    default:
      return DefaultController::page_404([]);
      break;
  }
});

// default path 404, 405
Router::pathNotFound(function($path) {
  return DefaultController::page_404(array(
    'path' => $path
  ));
});
Router::methodNotAllowed(function($path, $method) {
  return DefaultController::page_405(array (
    'path' => $path,
    'method' => $method
  ));
});

Router::run('/');
