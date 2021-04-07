<?php

use Model\JadwalVaksin\JadwalVaksins;
use Simpus\Apps\Service;
use System\Database\MyModel;

class JadwalVaksinService extends Service
{


  public function __construct()
  {
    parent::__construct();
    // put your code here
  }

  public function lansia(array $request): array
  {

    $jadwal = new JadwalVaksins();
    $jadwal->filterKategory('lansia');
    $jadwal->order('id', MyModel::ORDER_ASC);

    return array(
      'status'  => 'ok',
      'code'    => 200,
      'data'    => $jadwal->result(),
      'error'   => false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }
}

