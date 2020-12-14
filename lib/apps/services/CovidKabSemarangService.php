<?php
/**
 * TODO:
 * 1. tracker data - ifo data kecamatan tidak cocok
 */

use Simpus\Apps\Middleware;
use Simpus\Helper\Scheduler;
use System\Database\MyPDO;
use WebScrap\CovidKabSemarang\CovidKabSemarang;
use WebScrap\CovidKabSemarang\CovidKabSemarangTracker;

class CovidKabSemarangService extends Middleware
{
    protected $PDO = null;

    public function __construct(Mypdo $PDO = null)
    {
        $this->PDO = $PDO ?? new MyPDO();
    }


    private function versionControl(): array
    {
        return array(
            'status'    => 'error',
            'error'     => 'endpoint version not support',
            'info'      => array (
                'support_version' => 'ver1.1',
            ),
            'headers'       => ['HTTP/1.1 200 Oke']
        );
    }

    public function tracker(array $params)
    {
        // option
        $date_format    = $params['date_format'] ?? 'd/m h:i';
        if( $date_format != 'd/m h:i' ){
            // force format
            $date_format = 'Y-m-d h:i:sa';
        }

        $covid_tracker  = new CovidKabSemarangTracker($this->PDO);

        // configurasi
        $list_date      = $covid_tracker->listOfDate();
        $list_date      = array_column($list_date, 'date');

        $date           = $params['range_waktu'] ?? $list_date[0];
        $date = explode('-', $date);
        foreach( $date as $date_cek){
            if(! in_array($date_cek, $list_date) ){
                return [
                    'status'    => 'bad request',
                    'headers'   => ['HTTP/1.1 400 Bad Request']
                ];
            }
        }

        $lokasi         = $params['lokasi'] ?? [''];

        $covid_tracker->setFiltersDate( $date )->setFiltersLocation( $lokasi );
        // var_dump($date);exit;

        $result = [];
        foreach( $covid_tracker->result_count() as $covid_data) {
            $result[] = [
                "location"          => "kab. semarang",
                "time"              => date($date_format, $covid_data['date_create']),
                "kasus_posi"        => $covid_data['konfirmasi_symptomatik'],
                "kasus_isol"        => $covid_data['konfirmasi_asymptomatik'],
                "kasus_semb"        => $covid_data['konfirmasi_sembuh'],
                "kasus_meni"        => $covid_data['konfirmasi_meninggal'],
                "suspek"            => $covid_data['suspek'],
                "suspek_discharded" => $covid_data['suspek_discharded'],
                "suspek_meninggal"  => $covid_data['suspek_meninggal']
            ];
        }

        $end_point['data'] = count($result) == 1 ? $result[0] : $result;
        $end_point['status']    = 'ok';
        $end_point['headers']   = ['HTTP/1.1 200 Oke'];

        return $end_point;
    }
    public function tracker_all(array $params)
    {
        // option
        $date_format    = $params['date_format'] ?? 'd/m h:i';
        if( $date_format != 'd/m h:i' ){
            // force format
            $date_format = 'Y-m-d h:i:sa';
        }

        $covid_tracker  = new CovidKabSemarangTracker();

        // configurasi - date
        $list_date      = $covid_tracker->listOfDate();
        $list_date      = array_column($list_date, 'date');

        $date           = $params['range_waktu'] ?? $list_date[0];
        $date = explode('-', $date);
        $covid_tracker->setFiltersDate($date);

        $result = [];
        foreach( $covid_tracker->result_count() as $covid_data) {
            $result[] = [
                "location"          => "kab. semarang",
                "time"              => date($date_format, $covid_data['date_create']),
                "kasus_posi"        => $covid_data['konfirmasi_symptomatik'],
                "kasus_isol"        => $covid_data['konfirmasi_asymptomatik'],
                "kasus_semb"        => $covid_data['konfirmasi_sembuh'],
                "kasus_meni"        => $covid_data['konfirmasi_meninggal'],
                "suspek"            => $covid_data['suspek'],
                "suspek_discharded" => $covid_data['suspek_discharded'],
                "suspek_meninggal"  => $covid_data['suspek_meninggal']
            ];
        }

        $end_point['data']      = count($result) == 1 ? $result[0] : $result;
        $end_point['status']    = 'ok';
        $end_point['headers']   = ['HTTP/1.1 200 Oke'];

        return $end_point;
    }

    public function tracker_data(array $params)
    {
        // option
        $date_format    = $params['date_format'] ?? 'd/m h:i';
        if( $date_format != 'd/m h:i' ){
            // force format
            $date_format = 'Y-m-d h:i:sa';
        }
        // TODO:
        // 1. support filter berdasarkan wilayah
        $covid_tracker  = new CovidKabSemarangTracker();

        // configurasi

        // configurasi - date
        $list_date      = $covid_tracker->listOfDate();
        $list_date      = array_column($list_date, 'date');

        $date           = $params['range_waktu'] ?? $list_date[0];
        $date = explode('-', $date);
        foreach( $date as $date_cek){
            if(! in_array($date_cek, $list_date) ){
                return [
                    'status'    => 'bad request',
                    'headers'   => ['HTTP/1.1 400 Bad Request']
                ];
            }
        }

        // configurasi - lokasi
        if (isset($params['kecamatan'])) {
          $location = explode('--', $params['kecamatan']);
        } else {
          $list_kecamatan = (new CovidKabSemarang())->Daftar_Kecamatan;
          $location = array_keys($list_kecamatan);
        }

        $covid_tracker->setFiltersLocation($location);
        $covid_tracker->setFiltersDate( $date );

        $result = [];
        foreach( $date as $this_date ) {
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
                if( $last_kecamatan != $kecamatan ){
                    $group = [
                        'kecamatan'         => $kecamatan,
                        "data"              => []
                    ];
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
                $groups[$key]['data'][] = [
                "desa"              => ucwords($data_desa['desa']),
                "pdp"               => [
                        "dirawat"       => $data_desa['suspek'],
                        "sembuh"        => $data_desa['suspek_discharded'],
                        "meninggal"     => $data_desa['suspek_meninggal'],
                    ],
                    "positif"           => [
                            "dirawat"       => $data_desa['konfirmasi_symptomatik'],
                            "isolasi"       => $data_desa['konfirmasi_asymptomatik'],
                            "sembuh"        => $data_desa['konfirmasi_sembuh'],
                            "meninggal"     => $data_desa['konfirmasi_meninggal'],
                        ]
                    ];
            }

            $result[] = [
                "kabupaten"         => "semarang",
                "kasus_posi"        => $kasus_positif,
                "kasus_isol"        => $kasus_isolasi,
                "kasus_semb"        => $kasus_sembuh,
                "kasus_meni"        => $kasus_meninggal,
                "suspek"            => $suspek,
                "suspek_discharded" => $suspek_dischraded,
                "suspek_meninggal"  => $suspek_meninggal,
                "data"              => $groups,
            ];
        }

        $end_point              = count($result) == 1 ? $result[0] : $result;
        $end_point['status']    = 'ok';
        $end_point['headers']   = ['HTTP/1.1 200 Oke'];

        return $end_point;
    }
    public function fetch(array $params)
    {
        $id     = $params['kecamatan'] ?? null;
        $data   = new CovidKabSemarang();
        $daftar = $data->Daftar_Kecamatan;

        if( $id == null ){
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
            foreach( $daftar as $key => $value){
                $res[]            = $data->getData($key);
                $kasus_positif   += $data->positifDirawat();
                $kasus_isolasi   += $data->positifIsolasi();
                $kasus_sembuh    += $data->positifSembuh();
                $kasus_meninggal += $data->positifMeninggal();
                $suspek             += $data->suspek();
                $suspek_discharded  += $data->suspekDischarded();
                $suspek_meninggal   += $data->suspekMeninggal();
            }
            // menyun hasil dari data yang telah di konvert
            return [
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
            ];
        }else{
            // data sesauai id
            if( !array_key_exists($id, $daftar) ){
                return [
                    'status'    => 'bad request',
                    'headers'   => ['HTTP/1.1 400 Bad Request']
                ];
            }
            $result = $data->getData( $id );
            return array_merge(
                $result,
                [
                    'status' => 'ok',
                    'headers'    => ['HTTP/1.1 200 Oke']
                ]
            );
        }

        return [
            'status'    => 'no content',
            'headers'   => ['HTTP/1.1 204 No Content']
        ];
    }

    public function indexing(array $params)
    {
        $version =  $params['x-version'] ?? 'ver1.0';
        if ($version == 'ver1.0') {
            return $this->versionControl();
        }

        $schadule   = new Scheduler($this->PDO);
        $schadule(1)->read();

        $next_index     = $schadule->getLastModife() + $schadule->getInterval();
        $allow_index    = $next_index > (int) time() ? false : true;
        $index_status   = 'no index';

        if($allow_index){
            $new_reqeust = new CovidKabSemarangTracker($this->PDO);
            if( $new_reqeust->createIndex() ){
                $schadule->setLastModife(time());
                $schadule->update();
                // done
                $index_status = 'sussessful';
            }
        }

        return [
            'status'        => 'ok',
            'last_index'    => date("Y/m/d h:i:sa", $schadule->getLastModife()),
            'next_index'    => date("Y/m/d h:i:sa", $schadule->getLastModife() + $schadule->getInterval()),
            'index_status'  => $index_status,
            'headers'       => ['HTTP/1.1 200 Oke']
        ];
    }

    public function indexing_compiere(array $params)
    {
        $version =  $params['x-version'] ?? 'ver1.0';
        if ($version == 'ver1.0') {
            return $this->versionControl();
        }

        // get old data
        $old_raw = $this->tracker_data(array());
        $old_data = [
            $old_raw['kasus_posi'],
            $old_raw['kasus_isol'],
            $old_raw['kasus_semb'],
            $old_raw['kasus_meni'],
            $old_raw['suspek'],
            $old_raw['suspek_discharded'],
            $old_raw['suspek_meninggal'],
        ];

        // get last time data modife
        $schadule   = new Scheduler($this->PDO);
        $schadule(1)->read();

        // indexing data
        $index = new CovidKabSemarangTracker();
        $isNewData = $index->createIndex_compire($old_data);

        return [
            'status'        => 'ok',
            'last_index'    => date("Y/m/d h:i:sa", $schadule->getLastModife()),
            'next_index'    => date("Y/m/d h:i:sa", $schadule->getLastModife() + $schadule->getInterval()),
            'index_status'  => $isNewData ? 'sussessful' : 'no index',
            'headers'       => ['HTTP/1.1 200 Oke']
        ];
    }

    public function info(array $params)
    {
        $schadule   = new Scheduler($this->PDO);
        $status     = $schadule(1)->read();

        $last_index     = date("Y/m/d h:i:sa", $schadule->getLastModife());
        $next_index     = date("Y/m/d h:i:sa", $schadule->getLastModife() + $schadule->getInterval());
        $allow_index    = $schadule->getLastModife() + $schadule->getInterval() > (int) time() ? false : true;

        return [
            'status'        => $status ? 'ok' : 'error',
            'last_index'    => $last_index,
            'next_index'    => $next_index,
            'allow_index'   => $allow_index,
            'headers'       => ['HTTP/1.1 200 Oke']
        ];
    }

    public function track_record(array $params) :array
    {
        $covid_tracker  = new CovidKabSemarangTracker();
        $to_string      = $params['toString'] ?? false;
        $filter         = $params['day'] ?? null;

        if ($filter != null) {
          // $filter = $filter < 1 ? 1 : $filter;
          $dateLimit = time() - ($filter * 86400);
          $result = $covid_tracker->listOfDate($dateLimit);
        } else {
          $result = $covid_tracker->listOfDate();

        }

        return [
            'status'        => 'ok',
            'data'          => $to_string ? implode('-', array_values(array_column($result, 'date'))) : $result,
            'headers'       => ['HTTP/1.1 200 Oke']
        ];
    }

    public function daftar_kecamatan(array $param)
    {
        $data   = new CovidKabSemarang();
        $daftar = $data->Daftar_Kecamatan;

        return [
            'time'      => time(),
            'data'      => $daftar,
            'status'    => 'ok',
            'headers'   => ['HTTP/1.1 200 Oke']
        ];
    }

}
