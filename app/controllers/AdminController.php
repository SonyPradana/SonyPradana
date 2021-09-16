<?php

use Provider\Session\Session;
use System\Router\Controller;
use Simpus\Auth\Auth;

class AdminController extends Controller
{
  public function __construct()
  {
    $token  = Session::getSession()['auth']['token'];
    $auth   = new Auth($token, Auth::USER_NAME_AND_USER_AGENT_IP);

    if (! $auth->privilege('admin')) {
      DefaultController::page_401(['link' => '/admin']);
    }
  }

  public function index()
  {
    $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
    $error = array();

    return $this->view('admin/index', array (
      "auth"          => Session::getSession()['auth'],
      "DNT"           => Session::getSession()['DNT'],
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
