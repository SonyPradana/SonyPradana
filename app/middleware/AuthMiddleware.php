<?php

use Simpus\Apps\{AbstractMiddleware, Middleware};
use Simpus\Auth\{Auth, User};

class AuthMiddleware extends AbstractMiddleware
{
  public function handle()
  {
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
  }
}
