<?php
session_name('simpus');
session_set_cookie_params(['secure' => true, 'httponly' => true,]);
session_start();

use Simpus\Apps\{Route, Controller, Middleware};
use Simpus\Auth\{Auth, User};

require_once dirname(__DIR__) . '/vendor/autoload.php';

$app   = new Route();
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
$app->get('/', function() {
  return (new HomeController())->index();
});

// auth
$app->match(['get', 'post'], '/login', function() {
  return (new AuthController())->login();
});
$app->match(['get', 'post'], '/logout', function() {
  return (new AuthController())->logout();
});
$app->match(['get', 'post'], '/profile', function() {
  return (new AuthController())->profile();
});
$app->match(['get', 'post'], '/register', function() {
  return (new AuthController())->register();
});
$app->match(['get', 'post'], '/reset-password', function() {
  return (new AuthController())->reset();
});
$app->match(['get', 'post'], '/forgot/(:text)', function(string $action) {
  if ($action == 'reset') {
    return (new AuthController())->hardReset();
  } elseif ( $action == 'send') {
    return (new AuthController())->send();
  } else {
    return DefaultController::page_404(array(
      'path' => '/forgot/'.$action
    ));
  }
});

$app->get('/admin', function() {
  return (new AdminController())->index();
});
$app->get('/admin/(:any)', function($any) {
  return (new AdminController())->index();
});
$app->get('/admin/(:any)/(:any)', function($any, $second_any) {
  return (new AdminController())->index();
});

// message
$app->get('/messages/public', function() {
  return (new MessageController())->public();
});

// halaman standar
$app->get('/About', function() {
  return (new HomeController())->about();
});
$app->get('/Ourteam', function() {
  return (new ContactController())->ourTeam();
});
$app->match(['get', 'post'], '/Contactus', function() {
  return (new ContactController())->contactUs();
});

// info
$app->get('/info/(:any)', function(string $page) {
  if (Controller::view_exists('info/' . $page)) {
    return (new InfoController())->show( $page );
  } else {
    return DefaultController::page_404(array (
      'path' => '/info/'.$page
    ));
  }
});

// aricle
$app->get('/read/(:any)', function(string $title) {
  // TODO: article exist cheacker before call index
  return (new ArticleController())->index($title);
});

// unit kerja
// pendaftaran
$app->get('/pendaftaran', function() {
  return (new RegistrationMRController)->index();
});
// rekam-medis
$app->get('/rekam-medis', function() {
  return (new RekamMedisController())->index();
});
$app->match(['get', 'post'], '/rekam-medis/(:text)', function(string $page) {
  if (Controller::view_exists('rekam-medis/' . $page)) {
    return (new RekamMedisController())->show( strtolower($page) );
  } else {
    return (new RekamMedisController())->index();
  }
});
// kia-anak biodata/posyandu
$app->match(['get', 'post'], '/kia-anak/(:text)/(:text)', function(string $action, string $unit) {
  if (Controller::view_exists('kia-anak/'. $unit . '/'. $action)) {
    return (new KiaAnakController)->show($action, $unit);
  } else {
    return DefaultController::page_404(array (
      'path' => '/kia-anak/'.$action.'/'.$unit
    ));
  }
});

// API
$app->match(['get', 'put', 'post'], '/API/([0-9a-zA-Z.]*)/(:any)/(:any).json', function($version, $unit, $action) {
  return (new ApiController())->index($unit, $action, $version);
});
// API - Mix
$app->get('/css/mix.style.css', function() {
  return (new MixController())->mix_css();
});
$app->get('/js/mix.app.js', function() {
  return (new MixController())->mix_javascript();
});

// Trivia
$app->match(array('get', 'post'), '/trivia/submit', function() {
  return (new TriviaController())->submit();
});

// Stories
$app->get('/stories', function() {
  return (new StoriesController)->index();
});

$app->get('/stories/view/(:any)', function($storyID) {
  return (new StoriesController)->preview($storyID);
});
$app->get('/stories/roll/(:any)', function($group_name) {
  return (new StoriesController)->roll($group_name);
});

$app->get('/QnA', function() {
  return (new QuestionAnswerController)->index();
});
$app->get('/question/(:id)/(:slug)', function($thread_ID, $slug) {
  return (new QuestionAnswerController)->thread($thread_ID);
});
$app->get('/question/ask', function() {
  return (new QuestionAnswerController)->ask();
});
$app->get('/question/answer/(:id)', function($id) {
  return (new QuestionAnswerController)->answer($id);
});

// sitemap generator
$app::get('/sitemap.(:text)', function($ext) {
  $ext = strtolower($ext);
  $respone = new SiteMapController();

  if ($ext === "html" || $ext === "txt") {
    return $respone->html();
  } elseif ($ext === "xml") {
    return $respone->index();
  } else {
    return (new DefaultController)->page_404([]);
  }
});

// default path 404, 405
$app->pathNotFound(function($path) {
  return DefaultController::page_404(array(
    'path' => $path
  ));
});
$app->methodNotAllowed(function($path, $method) {
  return DefaultController::page_405(array (
    'path' => $path,
    'method' => $method
  ));
});

$app->run('/');
