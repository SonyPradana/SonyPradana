<?php
use Model\CovidTracker\CovidTracker;
use Simpus\Apps\Service;
use System\Database\MyPDO;
use WebScrap\CovidKabSemarang\CovidKabSemarang;
use WebScrap\CovidKabSemarang\CovidKabSemarangTracker;

class CovidKabSemarangService extends Service
{
  protected $PDO = null;

  public function __construct(Mypdo $PDO = null)
  {
    $this->error = new DefaultService();
    $this->PDO = $PDO ?? MyPDO::getInstance();
  }

  private function versionControl(): array
  {
    return array(
      'status'    => 'error',
      'code'      => 200,
      'error'     => 'endpoint version not support',
      'info'      => array (
        'support_version' => 'ver1.1',
      ),
      'headers'       => ['HTTP/1.1 200 Oke']
    );
  }

  /**
   * Get data (short), costume location
   *
   * @param array $request http header request
   * @return array Index covid data status
   */
  public function tracker(array $request)
  {
    // option
    $date_format    = $request['date_format'] ?? 'd/m h:i';
    if ($date_format != 'd/m h:i') {
      // force format
      $date_format = 'Y-m-d h:i:sa';
    }

    $covid_tracker  = new CovidKabSemarangTracker($this->PDO);

    // configurasi
    $list_date      = $covid_tracker->listOfDate();
    $list_date      = array_column($list_date, 'date');

    $date           = $request['range_waktu'] ?? $list_date[0];
    $date = explode('-', $date);
    foreach ($date as $date_cek) {
      if (! in_array($date_cek, $list_date)) {
        return $this->error(400);
      }
    }

    $lokasi = $request['lokasi'] ?? [''];

    $covid_tracker->setFiltersDate( $date )->setFiltersLocation( $lokasi );
    // var_dump($date);exit;

    $result = [];
    foreach ($covid_tracker->result_count() as $covid_data) {
      $result[] = array(
        "location"          => "kab. semarang",
        "time"              => date($date_format, $covid_data['date_create']),
        "kasus_posi"        => $covid_data['konfirmasi_symptomatik'],
        "kasus_isol"        => $covid_data['konfirmasi_asymptomatik'],
        "kasus_semb"        => $covid_data['konfirmasi_sembuh'],
        "kasus_meni"        => $covid_data['konfirmasi_meninggal'],
        "suspek"            => $covid_data['suspek'],
        "suspek_discharded" => $covid_data['suspek_discharded'],
        "suspek_meninggal"  => $covid_data['suspek_meninggal']
      );
    }

    $end_point['data']    = count($result) == 1 ? $result[0] : $result;
    $end_point['status']  = 'ok';
    $end_point['headers'] = ['HTTP/1.1 200 Oke'];

    return $end_point;
  }

  /**
   * Get data (short), all location
   *
   * @param array $request http header request
   * @return array Index covid data status
   */
  public function tracker_all(array $request)
  {
    // option
    $date_format = $request['date_format'] ?? 'd/m h:i';
    if ($date_format != 'd/m h:i') {
      // force format
      $date_format = 'Y-m-d h:i:sa';
    }

    $covid_tracker  = new CovidKabSemarangTracker();

    // configurasi - date
    $list_date      = $covid_tracker->listOfDate();
    $list_date      = array_column($list_date, 'date');

    $date           = $request['range_waktu'] ?? $list_date[0];
    $date = explode('-', $date);
    $covid_tracker->setFiltersDate($date);

    $result = [];
    foreach ($covid_tracker->result_count() as $covid_data) {
      $result[] = array(
        "location"          => "kab. semarang",
        "time"              => date($date_format, $covid_data['date_create']),
        "kasus_posi"        => $covid_data['konfirmasi_symptomatik'],
        "kasus_isol"        => $covid_data['konfirmasi_asymptomatik'],
        "kasus_semb"        => $covid_data['konfirmasi_sembuh'],
        "kasus_meni"        => $covid_data['konfirmasi_meninggal'],
        "suspek"            => $covid_data['suspek'],
        "suspek_discharded" => $covid_data['suspek_discharded'],
        "suspek_meninggal"  => $covid_data['suspek_meninggal']
      );
    }

    $end_point['data']      = count($result) == 1 ? $result[0] : $result;
    $end_point['status']    = 'ok';
    $end_point['code']      = 200;
    $end_point['headers']   = ['HTTP/1.1 200 Oke'];

    return $end_point;
  }

  /**
   * Get costume complate data (kecamatan)
   *
   * @param array $request http header request
   * @return array Index covid data status
   */
  public function tracker_data(array $request)
  {
    // option
    $date_format = $request['date_format'] ?? 'd/m h:i';
    if ($date_format != 'd/m h:i') {
      // force format
      $date_format = 'Y-m-d h:i:sa';
    }
    
    $covid_tracker  = new CovidKabSemarangTracker();

    // configurasi

    // configurasi - date
    $list_date      = $covid_tracker->listOfDate();
    $list_date      = array_column($list_date, 'date');

    $date           = $request['range_waktu'] ?? $list_date[0];
    $date = explode('-', $date);
    foreach ($date as $date_cek) {
      if (! in_array($date_cek, $list_date)) {
        return $this->error(400);
      }
    }

    // configurasi - lokasi
    if (isset($request['kecamatan'])) {
      $location = explode('--', $request['kecamatan']);
    } else {
      $list_kecamatan = (new CovidKabSemarang())->Daftar_Kecamatan;
      $location = array_keys($list_kecamatan);
    }

    $covid_tracker->setFiltersLocation($location);
    $covid_tracker->setFiltersDate( $date );

    $result = [];
    foreach ( $date as $this_date) {
      $session            = $covid_tracker->result()[ $this_date ];

      // counting - tingkat kabupaten
      $kasus_positif      = 0;
      $kasus_isolasi      = 0;
      $kasus_sembuh       = 0;
      $kasus_meninggal    = 0;
      $suspek             = 0;
      $suspek_dischraded  = 0;
      $suspek_meninggal   = 0;

      // counting - tingkat kecamatan
      $groups                  = array();
      $last_kecamatan          = '';
      $last_kasus_positif      = 0;
      $last_kasus_isolasi      = 0;
      $last_kasus_sembuh       = 0;
      $last_kasus_meninggal    = 0;
      $last_suspek             = 0;
      $last_suspek_dischraded  = 0;
      $last_suspek_meninggal   = 0;

      foreach($session as $data_desa){
        $kasus_positif      += $data_desa['konfirmasi_symptomatik'];
        $kasus_isolasi      += $data_desa['konfirmasi_asymptomatik'];
        $kasus_sembuh       += $data_desa['konfirmasi_sembuh'];
        $kasus_meninggal    += $data_desa['konfirmasi_meninggal'];
        $suspek             += $data_desa['suspek'];
        $suspek_dischraded  += $data_desa['suspek_discharded'];
        $suspek_meninggal   += $data_desa['suspek_meninggal'];

        $kecamatan  = $data_desa['kecamatan'];
        if ($last_kecamatan != $kecamatan) {
          $group = array(
            'kecamatan'         => $kecamatan,
            "data"              => []
          );
          $groups[]                = $group;
          $last_kecamatan          = $kecamatan;
          $last_kasus_positif      = 0;
          $last_kasus_isolasi      = 0;
          $last_kasus_sembuh       = 0;
          $last_kasus_meninggal    = 0;
          $last_suspek             = 0;
          $last_suspek_dischraded  = 0;
          $last_suspek_meninggal   = 0;
        }

        $last_kasus_positif      += $data_desa['konfirmasi_symptomatik'];
        $last_kasus_isolasi      += $data_desa['konfirmasi_asymptomatik'];
        $last_kasus_sembuh       += $data_desa['konfirmasi_sembuh'];
        $last_kasus_meninggal    += $data_desa['konfirmasi_meninggal'];
        $last_suspek             += $data_desa['suspek'];
        $last_suspek_dischraded  += $data_desa['suspek_discharded'];
        $last_suspek_meninggal   += $data_desa['suspek_meninggal'];

        $key = array_search($kecamatan, array_column($groups, 'kecamatan', 0));
        $groups[$key]['kasus_posi']         = $last_kasus_positif;
        $groups[$key]['kasus_isol']         = $last_kasus_isolasi;
        $groups[$key]['kasus_semb']         = $last_kasus_sembuh;
        $groups[$key]['kasus_meni']         = $last_kasus_meninggal;
        $groups[$key]['suspek']             = $last_suspek;
        $groups[$key]['suspek_discharded']  = $last_suspek_dischraded;
        $groups[$key]['suspek_meninggal']   = $last_suspek_meninggal;
        $groups[$key]['data'][] = array(
          "desa"          => ucwords($data_desa['desa']),
          "pdp"           => array(
            "dirawat"   => $data_desa['suspek'],
            "sembuh"    => $data_desa['suspek_discharded'],
            "meninggal" => $data_desa['suspek_meninggal'],
          ),
          "positif"       => array(
            "dirawat"   => $data_desa['konfirmasi_symptomatik'],
            "isolasi"   => $data_desa['konfirmasi_asymptomatik'],
            "sembuh"    => $data_desa['konfirmasi_sembuh'],
            "meninggal" => $data_desa['konfirmasi_meninggal'],
          )
        );
      }

      $result[] = array(
        "kabupaten"         => "semarang",
        "kasus_posi"        => $kasus_positif,
        "kasus_isol"        => $kasus_isolasi,
        "kasus_semb"        => $kasus_sembuh,
        "kasus_meni"        => $kasus_meninggal,
        "suspek"            => $suspek,
        "suspek_discharded" => $suspek_dischraded,
        "suspek_meninggal"  => $suspek_meninggal,
        "data"              => $groups,
      );
    }

    $end_point              = count($result) == 1 ? $result[0] : $result;
    $end_point['status']    = 'ok';
    $end_point['headers']   = ['HTTP/1.1 200 Oke'];

    return $end_point;
  }

  public function fetch(array $request)
  {
    $id     = $request['kecamatan'] ?? null;
    $data   = new CovidKabSemarang();
    $daftar = $data->Daftar_Kecamatan;

    if ($id == null) {
      // akumulasi semua data (se-kabupaten)
      $kasus_positif = 0;
      $kasus_isolasi = 0;
      $kasus_sembuh = 0;
      $kasus_meninggal = 0;
      $suspek             = 0;
      $suspek_discharded  = 0;
      $suspek_meninggal   = 0;
      $res = [];
      // me loop semua kecamatan terdaftar
      foreach ($daftar as $key => $value) {
        $res[]              = $data->getData($key);
        $kasus_positif      += $data->positifDirawat();
        $kasus_isolasi      += $data->positifIsolasi();
        $kasus_sembuh       += $data->positifSembuh();
        $kasus_meninggal    += $data->positifMeninggal();
        $suspek             += $data->suspek();
        $suspek_discharded  += $data->suspekDischarded();
        $suspek_meninggal   += $data->suspekMeninggal();
      }
      // menyun hasil dari data yang telah di konvert
      return array(
        "kabupaten"  => "semarang",
        "kasus_posi" => $kasus_positif,
        "kasus_isol" => $kasus_isolasi,
        "kasus_semb" => $kasus_sembuh,
        "kasus_meni" => $kasus_meninggal,
        "suspek"            => $suspek,
        "suspek_discharded" => $suspek_discharded,
        "suspek_meninggal"  => $suspek_meninggal,
        "data"       => $res,
        'status'     => 'ok',
        'headers'    => ['HTTP/1.1 200 Oke']
      );
    } else {
      // data sesauai id
      if (! array_key_exists($id, $daftar)) {
        return $this->error(400);
      }
      $result = $data->getData( $id );
      return array_merge(
        $result,
        array(
          'status' => 'ok',
          'headers'    => ['HTTP/1.1 200 Oke']
        )
      );
    }

    return $this->error(204);
  }

  /**
   * Force indexing (1 hour a time),
   * what ever if data same or not
   *
   * @param array $request http header request
   * @return array Index covid data status
   */
  public function indexing(array $request)
  {
    $version =  $request['x-version'] ?? 'ver1.0';
    if ($version == 'ver1.0') {
      return $this->versionControl();
    }

    $last_index     = CovidTracker::getLastIndex();
    $next_index     = $last_index + 3600;
    $allow_index    = $next_index > (int) time() ? false : true;
    $index_status   = 'no index';

    // allow index only 1 hour a time
    if ($allow_index) {
      $new_reqeust = new CovidKabSemarangTracker($this->PDO);
      if ($new_reqeust->createIndex()) {
        // done
        $index_status = 'sussessful';
      }
    }

    return array(
      'status'        => 'ok',
      'code'          => 200,
      'last_index'    => date("Y/m/d h:i:sa", $last_index),
      'next_index'    => date("Y/m/d h:i:sa", $next_index),
      'index_status'  => $index_status,
      'headers'       => ['HTTP/1.1 200 Oke']
    );
  }

  /**
   * Indexing data, compare data with previus data
   *
   * @param array $request http header request
   * @return array Index covid data status
   */
  public function indexing_compiere(array $request)
  {
    $version =  $request['x-version'] ?? 'ver1.0';
    if ($version == 'ver1.0') {
      return $this->versionControl();
    }

    // get old data
    $old_raw = $this->tracker_data(array());
    $old_data = array(
      $old_raw['kasus_posi'],
      $old_raw['kasus_isol'],
      $old_raw['kasus_semb'],
      $old_raw['kasus_meni'],
      $old_raw['suspek'],
      $old_raw['suspek_discharded'],
      $old_raw['suspek_meninggal'],
    );

    // get last time data modife
    $last_index     = CovidTracker::getLastIndex();
    $next_index     = $last_index + 3600;

    // indexing data
    $index = new CovidKabSemarangTracker();
    $isNewData = $index->createIndex_compire($old_data);

    return array(
      'status'        => 'ok',
      'code'          => 200,
      'last_index'    => date("Y/m/d h:i:sa", $last_index),
      'next_index'    => date("Y/m/d h:i:sa", $next_index),
      'index_status'  => $isNewData ? 'sussessful' : 'no index',
      'headers'       => ['HTTP/1.1 200 Oke']
    );
  }

  public function info(array $request): array
  {
    $last_index     = CovidTracker::getLastIndex();
    $next_index     = $last_index + 3600;

    $last_index     = date("Y/m/d h:i:sa", $last_index);
    $next_index     = date("Y/m/d h:i:sa", $next_index);
    $allow_index    = $next_index > (int) time() ? false : true;

    return array(
      'status'        => 'ok',
      'code'          => 200,
      'last_index'    => $last_index,
      'next_index'    => $next_index,
      'allow_index'   => $allow_index,
      'headers'       => ['HTTP/1.1 200 Oke']
    );
  }

  public function track_record(array $request): array
  {
    $covid_tracker  = new CovidKabSemarangTracker();
    $to_string      = $request['toString'] ?? false;
    $filter         = $request['day'] ?? null;

    if ($filter != null) {
      // $filter = $filter < 1 ? 1 : $filter;
      $dateLimit = time() - ($filter * 86400);
      $result = $covid_tracker->listOfDate($dateLimit);
    } else {
      $result = $covid_tracker->listOfDate();
    }

    return array(
      'status'        => 'ok',
      'code'          => 200,
      'data'          => $to_string ? implode('-', array_values(array_column($result, 'date'))) : $result,
      'headers'       => ['HTTP/1.1 200 Oke']
    );
  }

  public function daftar_kecamatan(array $param): array
  {
    $data   = new CovidKabSemarang();
    $daftar = $data->Daftar_Kecamatan;

    return array(
      'time'      => time(),
      'data'      => $daftar,
      'status'    => 'ok',
      'code'      => 200,
      'headers'   => ['HTTP/1.1 200 Oke']
    );
  }
}
