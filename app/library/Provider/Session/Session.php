<?php

namespace Provider\Session;

class Session
{
  private static $_session = [
    "auth" => [
      "login" => false,
      "user_name" => null,
      "display_name" => null,
      "display_picture_small" => null
    ]
  ];

  private static $hasSet = false;
  public static function setSession(array $session){
    if (! self::$hasSet) {
      self::$_session = (array) $session;
      self::$hasSet = true;
    }
  }

  public static function getSession(...$nest) :array{
    return (array) self::$_session;
  }
}
