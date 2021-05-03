<?php

namespace Model\Simpus;

use System\Database\MyModel;
use System\Database\MyPDO;

class PersonalRecords extends MyModel
{
  private $_options = Array (
    'equal' => array (
      "imperssion" => [":", ""],
      "operator"   => "="
    ),
    'like' => array (
      "imperssion" => [":", ""],
      "operator"   => "LIKE"
    )
  );

  public function filterByNik(int $nik)
  {
    $this->_FILTERS[] = array (
      'id'      => rand(1, 10),
      'param'   => 'nik',
      'value'   => $nik,
      'option'  => $this->_options['equal'],
      'type'    => null
    );
    return $this;
  }

  public function filterByJaminan(int $nomor_jaminan)
  {
    $this->_FILTERS[] = array (
      'id'      => rand(1, 10),
      'param'   => 'nomor_jaminan',
      'value'   => $nomor_jaminan,
      'option'  => $this->_options['equal'],
      'type'    => null
    );
    return $this;
  }

  /**
   * @param MyPDO $PDO DataBase class Dependency Injection
   */
  public function __construct(MyPDO $PDO = null)
  {
    $this->_TABELS[]  = 'data_personal';
    $this->PDO = $PDO ?? MyPDO::getInstance();
  }
}
