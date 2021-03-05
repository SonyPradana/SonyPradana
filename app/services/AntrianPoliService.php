<?php
/**
 * TODO:
 * - antrain pendaftaran
 * - update antrian bisa langsung banyak (beberapa poli sekaligus)
 */

use Model\Antrian\{antrianCRUD, antrianModel};
use Simpus\Apps\Service;
use System\Database\MyPDO;

class AntrianPoliService extends Service
{
  private function pusher($data)
  {
    $this->Pusher->trigger('my-channel', 'my-event', $data);
  }

  protected $PDO = null;
  protected $Pusher = null;

  public function __construct(MyPDo $PDO = null)
  {
    $this->error = new DefaultService();
    $this->PDO = $PDO ?? new MyPDO();
    $this->Pusher = new Pusher\Pusher(
      PUSHER_APP_KEY,
      PUSHER_APP_SECRET,
      PUSHER_APP_ID,
      array (
        'cluster' => PUSHER_APP_CLUSTER,
        'useTLS' => true
      )
    );;
  }

  public function antrian(array $params): array
  {
    $antrian = new antrianModel($this->PDO);
    $get_antrian = $antrian->resultAll();

    $get_antrian = array_map(function($x) {
      $x['date_time'] = date('d F Y', $x['date_time']);
      $x['current_times'] = date('h:i a', $x['current_times']);
      return $x;
    }, $get_antrian);

    $this->pusher($get_antrian);
    return array (
      'status'  => 'ok',
      'code'    => 200,
      'last'    => $antrian->lastUpdate(),
      'date'    => '15 Oct 2020',
      'data'    => $get_antrian,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function dipanggil($params): array
  {
    $this->useAuth();

    $update_poli = $this->validPoli($params);
    $update_antrian = $this->validInput($params);

    $antrian_poli =  new antrianCRUD($this->PDO);
    $status = $antrian_poli
      ->setID($update_poli)
      ->setCurrent($update_antrian)
      ->setCurrentTime()
      ->update();

    $get_antrian = $antrian_poli->getAll()[0];
    $get_antrian['date_time'] = date('d F Y', $get_antrian['date_time']);
    $get_antrian['current_times'] = date('h:i a', $get_antrian['current_times']);

    $this->pusher($get_antrian);

    return array(
      'status'  => $status ? 'ok' : 'error',
      'code'    => 200,
      'data'    => $get_antrian,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function baru($params): array
  {
    $this->useAuth();

    $update_poli = $this->validPoli($params);
    $update_antrian = $this->validInput($params);

    $antrian_poli =  new antrianCRUD($this->PDO);
    $status = $antrian_poli
      ->setID($update_poli)
      ->setQueueing($update_antrian)
      ->setQueueingTime()
      ->update();

    $get_antrian = $antrian_poli->getAll()[0];
    $get_antrian['date_time'] = date('d F Y', $get_antrian['date_time']);
    $get_antrian['queueing_times'] = date('h:i a', $get_antrian['current_times']);
    $get_antrian['current_times'] = date('h:i a', $get_antrian['current_times']);

    $this->pusher($get_antrian);

    return array (
      'status'  => $status ? 'ok' : 'error',
      'code'    => 200,
      'data' => $get_antrian,
      'headers' => array('HTTP/1.1 200 Oke')
    );

  }

  public function reset($params): array
  {
    $this->useAuth();

    $poli = $params['poli'] ?? $this->errorHandler(405);
    $antrian = new antrianCRUD($this->PDO);

    $data = array();
    $status = false;

    if ($poli == 'full_reset') {
      // poli kia
      $antrian->setID('A')->reset(true);
      $antrian->update();
      $data[] = $antrian->getAll()[0];
      // poli gigi
      $antrian->setID('B')->reset(true);
      $antrian->update();
      $data[] = $antrian->getAll()[0];
      // poli umum
      $antrian->setID('C')->reset(true);
      $antrian->update();
      $data[] = $antrian->getAll()[0];
      // poli lansia
      $antrian->setID('D')->reset(true);
      $status = $antrian->update();
      $data[] = $antrian->getAll()[0];

      $data = array_map(function($x) {
        $x['date_time'] = date('d F Y', $x['date_time']);
        $x['queueing_times'] = date('h:i a', $x['queueing_times']);
        $x['current_times'] = date('h:i a', $x['current_times']);
        return $x;
      }, $data);
    } else {
      $antrian->setID(strtoupper($poli))->reset();
      $status = $antrian->update();
      $data = $antrian->getAll()[0];

      $data['date_time'] = date('d F Y', $data['date_time']);
      $data['queueing_times'] = date('h:i a', $data['current_times']);
      $data['current_times'] = date('h:i a', $data['current_times']);
    }
    $this->pusher($data);


    return array(
      'status'  => $status ? 'ok' : 'error',
      'code'    => 200,
      'data'    => $data,
      'headers' => array('HTTP/1.1 200 Oke')
    );

  }

  // helper function
  private function validInput(array $params)
  {
    $number = $params['antrian'] ?? null;
    if (! is_int($number)) {
      $this->errorHandler(405);
    }
    return $number < 0 ? 0 : $number;
  }

  private function validPoli(array $params): string
  {
    $poli = $params['poli'] ?? $this->errorHandler(405);
    $poli = strtoupper($poli);
    return in_array($poli, ['A', 'B', 'C', 'D']) ? $poli : $this->errorHandler(405);
  }
}

