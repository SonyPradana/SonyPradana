<?php
/**
 * TODO:
 * - antrain pendaftaran
 * - update antrian bisa langsung banyak (beberapa poli sekaligus)
 */

require BASEURL . '/vendor/autoload.php';

use Model\Antrian\{antrianCRUD, antrianModel};
use Simpus\Apps\Middleware;
use Simpus\Helper\HttpHeader;

class AntrianPoliService extends Middleware
{  
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

  private function errorhandler()
  {
    HttpHeader::printJson(['status' => 'bad request'], 500, [
        "headers" => [
            'HTTP/1.1 400 Bad Request',
            'Content-Type: application/json'
        ]
    ]);
  }
  private function pusher($data)
  {
    $options = array(
      'cluster' => 'ap1',
      'useTLS' => true
    );
    $pusher = new Pusher\Pusher(
      '6c9ed3de96e572726af9',
      '5c3c760eac0a35d99093',
      '1069541',
      $options
    );

    $pusher->trigger('my-channel', 'my-event', $data);
  }

  public function antrian(array $params): array
  {
    $antrian = new antrianModel();
    $get_antrian = $antrian->resultAll();
    
    $get_antrian = array_map(function($x) {
      $x['date_time'] = date('d F Y', $x['date_time']);
      $x['current_times'] = date('h:i a', $x['current_times']);
      return $x;
    }, $get_antrian);
  
    $this->pusher($get_antrian);
    return array(
      'status' => 'ok',
      'last' => $antrian->lastUpdate(),
      'date' => '15 Oct 2020',
      'data'    => $get_antrian,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function dipanggil($params): array
  {
    $this->useAuth();

    $update_poli = $this->validPoli($params);
    $update_antrian = $this->validInput($params);

    $antrian_poli =  new antrianCRUD();
    $antrian_poli->setID(strtoupper($update_poli));
    $antrian_poli->setCurrent($update_antrian);
    $antrian_poli->setCurrentTime(time());
    $status = $antrian_poli->update();
    $get_antrian = $antrian_poli->getAll()[0];
    $get_antrian['date_time'] = date('d F Y', $get_antrian['date_time']);
    $get_antrian['current_times'] = date('h:i a', $get_antrian['current_times']);

    $this->pusher($get_antrian);

    return array(
      'status'  => $status ? 'ok' : 'error',
      'data' => $get_antrian,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function baru($params): array
  {
    $this->useAuth();
    
    $update_poli = $this->validPoli($params);
    $update_antrian = $this->validInput($params);

    $antrian_poli =  new antrianCRUD();
    $antrian_poli->setID(strtoupper($update_poli));
    $antrian_poli->setQueueing($update_antrian);
    $antrian_poli->setQueueingTime(time());
    $status = $antrian_poli->update();
    $get_antrian = $antrian_poli->getAll()[0];
    $get_antrian['date_time'] = date('d F Y', $get_antrian['date_time']);
    $get_antrian['queueing_times'] = date('h:i a', $get_antrian['current_times']);
    $get_antrian['current_times'] = date('h:i a', $get_antrian['current_times']);

    $this->pusher($get_antrian);

    return array(
      'status'  => $status ? 'ok' : 'error',
      'data' => $get_antrian,
      'headers' => array('HTTP/1.1 200 Oke')
    );

  }

  public function reset($params): array
  {
    $this->useAuth();

    $poli = $params['poli'] ?? $this->errorhandler();
    $antrian = new antrianCRUD();
    
    $data = [];
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
      'data' => $data,
      'headers' => array('HTTP/1.1 200 Oke')
    );

  }

  // helper function
  private function validInput(array $params)
  {
    $number = $params['antrian'] ?? $this->errorhandler();
    return $number < 0 ? 0 : $number;
  }

  private function validPoli(array $params): string
  {
    $poli = $params['poli'] ?? $this->errorhandler();
    $poli = strtoupper($poli);
    return in_array($poli, ['A', 'B', 'C', 'D']) ? $poli : $this->errorhandler();
  }
}

