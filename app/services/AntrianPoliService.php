<?php
/**
 * TODO:
 * - antrain pendaftaran
 * - update antrian bisa langsung banyak (beberapa poli sekaligus)
 */

use Model\Antrian\{antrianCRUD, antrianModel};
use Simpus\Apps\Service;
use System\Broadcast\Broadcast;
use System\Database\MyPDO;

class AntrianPoliService extends Service
{
  protected $PDO = null;
  /** @var Broadcast */
  protected $pusher;

  public function __construct(MyPDo $PDO = null)
  {
    $this->error = new DefaultService();
    $this->PDO = $PDO ?? MyPDO::getInstance();
    $this->pusher = new Broadcast('info', 'antrian');
  }

  public function antrian(array $request): array
  {
    $antrian = new antrianModel($this->PDO);
    $get_antrian = $antrian->resultAll();

    $get_antrian = array_map(function($x) {
      $x['date_time'] = date('d F Y', $x['date_time']);
      $x['current_times'] = date('h:i a', $x['current_times']);
      return $x;
    }, $get_antrian);

    return $this->pusher->trigerPusher([
      'status'  => 'ok',
      'code'    => 200,
      'last'    => $antrian->lastUpdate(),
      'data'    => $get_antrian,
      'headers' => array('HTTP/1.1 200 Oke')
    ]);
  }

  public function dipanggil($request): array
  {
    if ($this->isGuest()) {
      return $this->error->code_401();
    }

    $validate = new GUMP();
    $validate->validation_rules([
      'poli'    => 'required|contains,A;B;C;D;a;b;c;d;',
      'antrian' => 'required|numeric'
    ]);
    $validate->filter_rules([
      'antrian' => 'upper_case'
    ]);
    $validate->run($request);
    if ($validate->errors()) {
      return $this->error(405);;
    }

    $antrian_poli =  new antrianCRUD($this->PDO);
    $status = $antrian_poli
      ->setID($request['poli'])
      ->setCurrent($request['antrian'])
      ->setCurrentTime()
      ->update();

    $get_antrian = $antrian_poli->getAll()[0];
    $get_antrian['date_time'] = date('d F Y', $get_antrian['date_time']);
    $get_antrian['current_times'] = date('h:i a', $get_antrian['current_times']);

    return $this->pusher->trigerPusher([
      'status'  => $status ? 'ok' : 'error',
      'code'    => 200,
      'data'    => $get_antrian,
      'headers' => array('HTTP/1.1 200 Oke')
    ]);
  }

  public function baru($request): array
  {
    if ($this->isGuest()) {
      return $this->error->code_401();
    }

    $validate = new GUMP();
    $validate->validation_rules([
      'poli'    => 'required|contains,A;B;C;D;a;b;c;d;',
      'antrian' => 'required|numeric'
    ]);
    $validate->filter_rules([
      'antrian' => 'upper_case'
    ]);
    $validate->run($request);
    if ($validate->errors()) {
      return $this->error(405);;
    }

    $antrian_poli =  new antrianCRUD($this->PDO);
    $status = $antrian_poli
      ->setID($request['poli'])
      ->setQueueing($request['antrian'])
      ->setQueueingTime()
      ->update();

    $get_antrian = $antrian_poli->getAll()[0];
    $get_antrian['date_time'] = date('d F Y', $get_antrian['date_time']);
    $get_antrian['queueing_times'] = date('h:i a', $get_antrian['current_times']);
    $get_antrian['current_times'] = date('h:i a', $get_antrian['current_times']);

    return $this->pusher->trigerPusher([
      'status'  => $status ? 'ok' : 'error',
      'code'    => 200,
      'data' => $get_antrian,
      'headers' => array('HTTP/1.1 200 Oke')
    ]);

  }

  public function reset($request): array
  {
    if ($this->isGuest()) {
      return $this->error->code_401();
    }

    $poli = $request['poli'] ?? $this->errorHandler(405);
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

    return $this->pusher->trigerPusher([
      'status'  => $status ? 'ok' : 'error',
      'code'    => 200,
      'data'    => $data,
      'headers' => array('HTTP/1.1 200 Oke')
    ]);

  }
}

