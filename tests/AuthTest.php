<?php

namespace Simpus\Apps;

use PHPUnit\Framework\TestCase;
use Simpus\Auth\Login;
use Simpus\Auth\User;

// TODO: update all auth class to proved test without distube main database 
final class AuthTest extends TestCase
{
  public function testLogin(): void
  {
    // success login
    $login_status = Login::PasswordVerify('unittest', 'unittest');
    $this->assertTrue($login_status);

    // failed login
    $login_status = Login::PasswordVerify('unittest', 'password');
    $this->assertFalse($login_status);
  }

  public function testUser(): void
  {
    // valid user
    $user = new User('unittest');
    $this->assertTrue($user->userVerify());
    $this->assertEquals('unittest@simpuslerep.com', $user->getEmail());
    // setter
    $user->setDisplayName('Unit Test');
    $user->setSection('Testing');
    $user->setDisplayPicture('/public/data/img/display-picture/user/angger.jpg');
    // get setter
    $this->assertEquals('Unit Test', $user->getDisplayName());
    $this->assertEquals('Testing', $user->getSection());
    $this->assertEquals('/public/data/img/display-picture/user/angger.jpg', $user->getDisplayPicture());
  
    // unvalid user
    $user = new User('null');
    $this->assertFalse($user->userVerify());
  }
}
