<?php

use Simpus\Apps\Cache;
use Simpus\Apps\Service;

class KuotaVaksinService extends Service
{
  private $pusher;

  public function __construct()
  {
    parent::__construct();
    // put your code here
    $this->pusher = new \System\Broadcast\Broadcast('info', 'antrian-vaksin');
  }

  /**
   * Membuat jadwal beserta kuota vaksinasi,
   * setipa request akan dibuatkan baru (menimpa request lama)
   */
  public function createJadwal(array $request): array
  {
    // sample header request
    // {"date":"123","data":[{"kategory":"test","kuota":10,"dipakai":10},{"kategory":"test2","kuota":20,"dipakai":1}]}

    // must user login
    if ($this->isGuest()) {
      return $this->error->code_401();
    }
    // method must put
    if ($request['x-method'] != 'PUT') {
      return $this->error->code_403();
    }

    // validate
    $validate = new GUMP();
    $validate->validation_rules([
      'date'  => 'required',
      'data'  => 'required'
    ]);
    $validate->run($request);

    // handle validation
    if ($validate->errors()) {
      $respoone = $this->error->code_400();
      $respoone['error'] = $validate->get_errors_array();
      return $respoone;
    }

    // generate document id
    $key = 'kuota-vaksin';

    // force new data
    Cache::static()->clear($key);
    $data = Cache::remember($key, 60 * 60 * 24, fn() => [
      'date' => $request['date'],
      'data' => $request['data']
      // sample
      // [['kategory' => '', 'kuota' => 0, 'dipakai' => 0]]
      ]
    );

    return $this->pusher->trigerPusher([
      'status'  => 'ok',
      'code'    => 200,
      'data'    => $data
    ]);
  }

  /**
   * Melihat jumlah kuota tersedia,
   * bila tidak ada maka ada pesan error
   */
  public function getKuota(array $request): array
  {
    // generate document id
    $key = 'kuota-vaksin';
    $data = Cache::static()->getItem($key)->get() ?? null;

    $error = $data === null
      ? 'tidak ada kuota vaksin'
      : false;

    // send data
    return $this->pusher->trigerPusher([
      'status'  => 'oke',
      'code'    => 200,
      'data'    => $data,
      'error'   => $error,
    ]);
  }

  /**
   * Merefresh/mengupdate data baru
   */
  public function updateKuota(array $request)
  {
    // must user login
    if ($this->isGuest()) {
      return $this->error->code_401();
    }
    // method must put
    if ($request['x-method'] != 'PUT') {
      return $this->error->code_405();
    }

    // validate
    $validate = new GUMP();
    $validate->validation_rules([
      'kategory'  => 'required|alpha_space|min_len,4|max_len,25',
      'kuota'     => 'required|min_numeric,0',
      'dipakai'   => 'required|min_numeric,0'
    ]);
    $validate->run($request);

    // handle validation
    if ($validate->errors()) {
      $respoone = $this->error->code_400();
      $respoone['error'] = $validate->get_errors_array();
      return $respoone;
    }

    $key = 'kuota-vaksin';
    // cek exis data
    if (! Cache::static()->hasItem($key)) {
      return $this->error->code_405();
    }

    // get cache data and replase new data
    $raw_data = Cache::static()->getItem($key)->get() ?? [];
    $old_data = $raw_data['data'] ?? [];
    $new_data = [];
    $has_change = false;
    foreach ($old_data as $data) {
      if ($data['kategory'] === $request['kategory']) {
        $has_change = true;
        $data['kuota']    = $request['kuota'];
        $data['dipakai']  = $request['dipakai'];
      }
      $new_data[] = $data;
    }
    $respone_data = [
      'date'  => $raw_data['date'] ?? 0,
      'data'  => $new_data
    ];

    // save to cache storage
    $cache_item = Cache::static()->getItem($key);
    $cache_item->set($respone_data);
    Cache::static()->save($cache_item);

    // repone to client and push to bordcast
    return $this->pusher->trigerPusher([
      'status'  => 'ok',
      'code'    => $has_change ? 200 : 303,
      'data'    => $respone_data,
      'error'   => false
    ]);
  }

  /**
   * Menghapus data vaksin yg ada (force)
   */
  public function destroyKuota(array $request)
  {
    if ($this->isGuest()) {
      return $this->error->code_401();
    }

    Cache::static()->clear('kuota-vaksin');
    return $this->pusher->trigerPusher([
      'status'  => 'oke',
      'code'    => 200,
      'data'    => [
        'date'  => 0,
        'data'  => []
      ],
      'messaage'  => 'data dihapus/vaksin selesai'
    ]);
  }

}

