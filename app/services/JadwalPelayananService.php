<?php

use Simpus\Apps\Middleware;
use Simpus\Helper\HttpHeader;
use Simpus\Services\JadwalKia;
use System\Database\MyPDO;

class JadwalPelayananService extends Middleware
{
  // private function
  private function useAuth()
  {
    // cek access
    if( $this->getMiddleware()['auth']['login'] == false ){
      HttpHeader::printJson(['status' => 'unauthorized'], 500, [
        "headers" => [
          'HTTP/1.0 401 Unauthorized',
          'Content-Type: application/json'
        ]
      ]);
    }
  }

  protected $PDO = null;
  public function __construct(MyPDO $PDO = null)
  {
    $this->PDO = $PDO ?? new MyPDO();
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
