<?php

use Simpus\Apps\Controller;
use Simpus\Apps\Middleware;
use Simpus\Auth\Auth;

class AdminController extends Controller
{
  public function __construct()
  {
    $token = Middleware::getMiddleware()['auth']['token'];
    $auth = new Auth($token, Auth::USER_NAME_AND_USER_AGENT_IP);

    if (! $auth->privilege('admin')) {
      echo 'You don’t have permission to access this page!';
      header('HTTP/1.1 403 Forbidden');
      exit();
    }
  }

  public function index()
  {
    $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
    $error = array();

    return $this->view('admin/index', array (
      "auth"          => $this->getMiddleware()['auth'],
      "DNT"           => $this->getMiddleware()['DNT'],
      "redirect_to"   => $_GET['redirect_to'] ?? '/',
      "meta"          => array (
        "title"         => "Dashbord - SIMPUS LEREP",
        "discription"   => "Dashbord Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
        "keywords"      => "dashbord simpus lerep"
      ),
      "header"        => array (
        "active_menu"   => 'null',
         "header_menu"   => MENU_MEDREC
      ),
      "contents" => array (

      ),
      'error' => $error,
      "message" => array (
        "show"      => $msg['show'],
        "type"      => $msg['type'],
        "content"   => $msg['content']
      )
    ));
  }
}
