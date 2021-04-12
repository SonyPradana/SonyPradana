<?php namespace Model\JadwalVaksin;

use System\Database\MyModel;
use System\Database\MyPDO;

class JadwalVaksins extends MyModel
{

  public function filterKategory(string $val)
  {
    $this->_FILTERS['kelompok'] = [
      'value'   => $val,
      'option'  => array(
        "imperssion" => [":", ""],
        "operator"   => "="
      )
    ];

    return $this;
  }

  /**
   * @param MyPDO $PDO DataBase class Dependency Injection
   */
  public function __construct(MyPDO $PDO = null)
  {
    $this->_TABELS[]  = 'jadwal_vaksin';
    $this->PDO = $PDO ?? MyPDO::getInstance();
  }
}
