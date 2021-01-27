<?php

namespace Model\UserRegister;

use System\Database\MyCRUD;
use System\Database\MyPDO;

class UserRegister extends MyCRUD
{
  // getter
  public function getID()
  {
    return $this->ID['id'] ?? null;
  }

  // setter
  public function setID(int $val)
  {
    $this->ID = array (
      'id' => $val
    );
    return $this;
  }


  public function __construct()
  {
    $this->PDO = new MyPDO();
    $this->TABLE_NAME = 'registration';
    $this->COLUMNS = array(
      'id' => null,
			'user' => null,
			'email' => null,
			'pwd' => null,
			'disp_name' => null,
			'stat' => null,
			
    );
  }

}
