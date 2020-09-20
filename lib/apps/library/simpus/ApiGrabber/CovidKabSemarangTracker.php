<?php
/**
 * TODO:
 * 1. membuat cash data kedalam internal database
 * 2. buat schedule untuk otomatis menyimpan data kedata base apabila belum terindex pad hari trsebut
 * 3. buat method untuk mengambil langung dan mengambil dari databse
 * 
 * NOTE:
 * 1. client/user dapat memilih mengambil dari database (cash) atau langsung
 * 2. shadule dijalankan di ServiceController otomatis
 */

namespace Simpus\ApiGrabber;
use Simpus\ApiGrabber\CovidKabSemarang;
use Simpus\Database\MyPDO;
use Simpus\Helper\ConvertCode;
use Simpus\Helper\Scheduler;

class CovidKabSemarangTracker
{
    /** @var MyPDO */
    private $db;
    private $list_kecamatan  = [];
    private $_filters_lokasi = [];
    private $_filters_waktu  = [];
    private $_query;

    public function setFiltersLocation(array $val)
    {
        $this->_filters_lokasi = $val;
        return $this;
    }
    public function setFiltersDate(array $val)
    {
        $this->_filters_waktu = $val;
        return $this;
    }

    public function __construct()
    {
        $this->db               = new MyPDO();
        $this->list_kecamatan   = $this->getListKecamatanDesa();
    }

    public function createIndex()
    {
        $schadule       = new Scheduler(1);
        $schadule->read();
       
        $new_request    = new CovidKabSemarang();
        $daftar         = $new_request->Daftar_Kecamatan;
        
        $time       = (int) time();
        $id_track   = ConvertCode::ConvertToCode(time());
        foreach( $daftar as $kecamatan => $kecamatan_val){
            $section = $new_request->getData($kecamatan)['data'];
            foreach( $section as $desa => $desa_val){
                if(! $this->creat([
                    'date'                      => (int) $time, 
                    'location'                  => $this->searchId($kecamatan, $desa_val['desa']),
                    'suspek'                    => (int) $desa_val['pdp']['dirawat'], 
                    'suspek_discharded'         => (int) $desa_val['pdp']['sembuh'], 
                    'suspek_meninggal'          => (int) $desa_val['pdp']['meninggal'], 
                    'konfirmasi_symptomatik'    => (int) $desa_val['positif']['dirawat'],
                    'konfirmasi_asymptomatik'   => (int) $desa_val['positif']['isolasi'], 
                    'konfirmasi_sembuh'         => (int) $desa_val['positif']['sembuh'], 
                    'konfirmasi_meninggal'      => (int) $desa_val['positif']['meninggal']                
                ]) ){
                    return false;
                }
            }
        }

        $schadule->setLastModife((int) time());
        $schadule->update();
        return true;
    }

    public function result()
    {
        $grupByDate = [];
        foreach($this->_filters_waktu as $date){
            $this->db->query($this->queryBuilder($date));        
            $this->db->bind(':date', $date);
            $grupByDate[$date] = $this->db->resultset();
        }
        return $grupByDate;
    }
    
    public function result_count()
    {
        $grupByDate = [];
        foreach($this->_filters_waktu as $date){
            $this->db->query($this->queryBuilder_count($date));        
            $this->db->bind(':date', $date);
            $grupByDate[$date] = $this->db->resultset();
        }
        return $grupByDate;
    }

    public function listOfDate() :array
    {
        $this->db->query("SELECT `date` 
                            FROM `covid_tracker`
                            GROUP BY `date`
                            ORDER BY `date`
                            DESC");
        return $this->db->resultset();
    }
    
    private function queryBuilder_count($date)
    {
        $query          = " SELECT 
                                covid_tracker.location,               
                                SUM(covid_tracker.suspek)                   AS suspek,
                                SUM(covid_tracker.suspek_discharded)        AS suspek_discharded,
                                SUM(covid_tracker.suspek_meninggal)         AS suspek_meninggal,      
                                SUM(covid_tracker.konfirmasi_symptomatik)   AS konfirmasi_symptomatik, 
                                SUM(covid_tracker.konfirmasi_asymptomatik)  AS konfirmasi_asymptomatik,
                                SUM(covid_tracker.konfirmasi_sembuh)        AS konfirmasi_sembuh,
                                SUM(covid_tracker.konfirmasi_meninggal)     AS konfirmasi_meninggal
                            FROM `covid_tracker`
                            INNER JOIN  `desa_kecamatan`
                                    ON desa_kecamatan.id = covid_tracker.location
                            WHERE
                                `date` = :date 
                            GROUP BY `date`
                            ";
        return $query;
    }
    private function queryBuilder($date)
    {
        $query          = " SELECT 
                                desa_kecamatan.kecamatan,
                                desa_kecamatan.desa,
                                covid_tracker.date,
                                covid_tracker.location,               
                                covid_tracker.suspek,                 
                                covid_tracker.suspek_discharded,
                                covid_tracker.suspek_meninggal,      
                                covid_tracker.konfirmasi_symptomatik, 
                                covid_tracker.konfirmasi_asymptomatik,
                                covid_tracker.konfirmasi_sembuh,
                                covid_tracker.konfirmasi_meninggal
                            FROM `covid_tracker`
                            INNER JOIN  `desa_kecamatan`
                                    ON desa_kecamatan.id = covid_tracker.location
                            WHERE
                                `date` = :date 
                            ";
        return $query;
    }

    private function whereStatment($colomn_name, array $array_val)
    {
        $query          = [];
        foreach( $array_val as $filter ){
            $query[] = "$colomn_name = :date_$filter";
        }
        return implode(' AND ', $query);
    }

    private function creat($params = [    
        'date'                    => '', 
        'location'                => '',
        'suspek'                  => 0, 
        'suspek_discharded'       => 0, 
        'suspek_meninggal'        => 0, 
        'konfirmasi_symptomatik'  => 0,
        'konfirmasi_asymptomatik' => 0, 
        'konfirmasi_sembuh'       => 0, 
        'konfirmasi_meninggal'    => 0
    ])
    {
        $this->db->query("INSERT INTO `covid_tracker`
                            (`id`, `date`, `location`, `suspek`, `suspek_discharded`, `suspek_meninggal`, `konfirmasi_symptomatik`, `konfirmasi_asymptomatik`, `konfirmasi_sembuh`, `konfirmasi_meninggal`)
                          VALUES
                            (
                            :id,
                            :date, 
                            :location,
                            :suspek, 
                            :suspek_discharded, 
                            :suspek_meninggal, 
                            :konfirmasi_symptomatik, 
                            :konfirmasi_asymptomatik, 
                            :konfirmasi_sembuh, 
                            :konfirmasi_meninggal
                            )
                        ");
        $this->db->bind(':id', '');
        $this->db->bind(':date', $params['date']);
        $this->db->bind(':location', $params['location']);
        $this->db->bind(':suspek', $params['suspek']);
        $this->db->bind(':suspek_discharded', $params['suspek_discharded']);
        $this->db->bind(':suspek_meninggal', $params['suspek_meninggal']);
        $this->db->bind(':konfirmasi_symptomatik', $params['konfirmasi_symptomatik']);
        $this->db->bind(':konfirmasi_asymptomatik', $params['konfirmasi_asymptomatik']);
        $this->db->bind(':konfirmasi_sembuh', $params['konfirmasi_sembuh']);
        $this->db->bind(':konfirmasi_meninggal', $params['konfirmasi_meninggal']);

        $this->db->execute();
        if( $this->db->rowCount() > 0) return true;

        return false;
    }

    private function searchId($kecamatan, $desa)
    {
        $kecamatan  = strtolower($kecamatan);
        $desa       = strtolower($desa);
        $filter     = array_filter($this->list_kecamatan, function($val) use ($desa, $kecamatan){
            return ($val['kecamatan'] == $kecamatan && $val['desa'] == $desa);
        });
        return array_values($filter)[0]['id'];
    }

    private function getListKecamatanDesa() :array
    {
        $this->db->query("SELECT * FROM `desa_kecamatan`");
        return $this->db->resultset();
    }
}
