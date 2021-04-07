<?php

namespace Model\JadwalVaksin;

use System\Database\MyCRUD;
use System\Database\MyPDO;

class JadwalVaksin extends MyCRUD
{
  // getter
  public function getID()
  {
    return $this->ID['id'] ?? null;
  }

  // setter
  public function setID(int $val)
  {
    $this->ID = array(
      'id' => $val
    );
    return $this;
  }

  public function __construct()
  {
    $this->PDO = new MyPDO();
    $this->TABLE_NAME = 'jadwal_vaksin';
    $this->COLUMNS = array(
      'id' => null,
			'tanggal' => null,
			'jumlah' => null,
			'desa' => null,
			'kelompok' => null,

    );
  }

}
