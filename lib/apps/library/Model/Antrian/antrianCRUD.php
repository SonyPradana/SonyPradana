<?php

namespace Model\Antrian;

use System\Database\MyCRUD;
use System\Database\MyPDO;

class antrianCRUD extends MyCRUD
{
  private $_date_time;
  private $_poli;
  private $_current;
  private $_current_times;
  private $_queueing;
  private $_queueing_times;

  public function setID(string $val)
  {
    $this->ID = ['poli' => $val];
    $this->COLUMNS['poli'] = $val;
    return $this;
  }
  public function setCurrent(int $val)
  {
    $this->COLUMNS['current'] = $val;
    return $this;
  }
  public function setCurrentTime(int $val)
  {
    $this->COLUMNS['current_times'] = $val;
    return $this;
  }
  public function setQueueing(int $val)
  {
    $this->COLUMNS['queueing'] = $val;
    return $this;
  }
  public function setQueueingTime(int $val)
  {
    $this->COLUMNS['queueing_times'] = $val;
    return $this;
  }

  public function getAll(): array
  {
    $this->PDO->query(
      "SELECT 
        *
      FROM
        `antrian`
      WHERE
        `poli` = :poli"
    );
    $this->PDO->bind(':poli', $this->ID['poli']);
    $this->PDO->execute();
    return $this->PDO->resultset();
  }

  public function reset(bool $reset_date_time = false)
  {
    $time = time();

    $this->COLUMNS = [
      'current' => '0',
      'current_times' => $time,
      'queueing' => '0',
      'queueing_times' => $time
    ];
    if ($reset_date_time) {
      // full reset date time
      $this->COLUMNS['date_time'] = $time;
    }
    return $this;
  }

  public function __construct()
  {
    $this->PDO = new MyPDO();
    $this->TABLE_NAME = 'antrian';
    $this->COLUMNS = [
      'date_time' => null,
      'poli' => null,
      'current' => null,
      'current_times' => null,
      'queueing' => null,
      'queueing_times' => null
    ];
  }

}
