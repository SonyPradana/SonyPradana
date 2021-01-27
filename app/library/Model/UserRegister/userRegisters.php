<?php namespace Model\UserRegister;

use System\Database\MyModel;
use System\Database\MyPDO;

class userRegisters extends MyModel
{
  /**
   * @param MyPDO $PDO DataBase class Dependency Injection
   */
  public function __construct(MyPDO $PDO = null)
  {
    $this->_TABELS[]  = 'registration';
    $this->PDO = $PDO ?? new MyPDO();
    $this->_COLUMNS = array('id', 'user', 'email', 'disp_name', 'stat');
  }
}
