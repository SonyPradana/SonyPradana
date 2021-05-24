<?php

use Simpus\Apps\Service;
use Model\JadwalKia\JadwalKia;
use System\Database\MyPDO;

class JadwalPelayananService extends Service
{
  protected $PDO = null;
  public function __construct(MyPDO $PDO = null)
  {
    $this->error = new DefaultService();
    $this->PDO = $PDO ?? MyPDO::getInstance();
  }

  public function Imunisasi(array $params) :array
  {
    $month = $params['month'] ?? date('m');
    $year  = $params['year'] ?? date('Y');

    $jadwal = new JadwalKia($this->PDO);
    $result = $jadwal->getdata($month, $year);

    return array (
      'status'  => 'ok',
      'code'    => 200,
      'data'    => $result,
      'error'   => false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function create_jadwal(array $request)
  {
    $this->useAuth();

    $create = new JadwalKia($this->PDO);
    $success = $create->autoCreatJadwal(date('m'), date('Y'));

    $error = false;
    if (! $success) {
      $error['server'] = 'gagal menyimpan data / data sudah tersedia';
    }

    return array (
      'status'  => $success ? 'ok' : 'not save',
      'code'    => 200,
      'data'    => array(),
      'error'   => $error,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }
}
