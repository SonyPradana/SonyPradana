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
    return $this->view('admin/index');
  }
}
