<?php namespace Simpus\ApiGrabber;

use Simpus\ApiGrabber\CovidKabSemarang;
use System\Database\MyPDO;
use Simpus\Helper\ConvertCode;
use Simpus\Helper\Scheduler;

/** class untuk minyimpan data kedalam data base */
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

    /** menyimpan data kedalam database
     * @return boolean true jika data berhasil disimpan
     */
    public function createIndex(): bool
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

    /** menyimpan data kedalam database,
     *  dengan membandingkan data lama terlebih dahulu.
     * @return boolean true jika berhasil disimpan
     * */
    public function createIndex_compire(array $old_data): bool
    {
        $time = time();

        // initial covid grabber
        $covid = new CovidKabSemarang();
        $kecamatan = $covid->Daftar_Kecamatan;
        
        // get data
        $data_covid = array();
        foreach( $kecamatan as $key => $val) {
            $session_data = $covid->getData($key);
            $data_covid[$val] = $session_data;
            // cek keberhasilan memuat data
            if ($session_data === false) return false;
        }

        // prepare data untuk compire dengan old data
        $new_data = array (
            'kasus_posi' => 0,
            'kasus_isol' => 0,
            'kasus_semb' => 0,
            'kasus_meni' => 0,
            'suspek' => 0,
            'suspek_discharded' => 0,
            'suspek_meninggal' => 0
        );
        foreach($data_covid as $key => $value) {
            $new_data['kasus_posi'] += $value['kasus_posi'];
            $new_data['kasus_isol'] += $value['kasus_isol'];
            $new_data['kasus_semb'] += $value['kasus_semb'];
            $new_data['kasus_meni'] += $value['kasus_meni'];
            $new_data['suspek'] += $value['suspek'];
            $new_data['suspek_discharded'] += $value['discharded'];
            $new_data['suspek_meninggal'] += $value['suspek_meninggal'];
        }
        
        // compire new data dan old data
        if (array_values($new_data) != $old_data) {
            $schadule   = new Scheduler(1);
            $schadule->read();
            // simpan data
            foreach ($data_covid as $key => $value) {
                $data_section = $value['data'];
                foreach($data_section as $desa => $desa_val) {
                    // simpan data kedatabase
                    $this->creat(array (
                        'date'                      => (int) $time, 
                        'location'                  => $this->searchId($value['kecamatan'], $desa_val['desa']),
                        'suspek'                    => (int) $desa_val['pdp']['dirawat'], 
                        'suspek_discharded'         => (int) $desa_val['pdp']['sembuh'], 
                        'suspek_meninggal'          => (int) $desa_val['pdp']['meninggal'], 
                        'konfirmasi_symptomatik'    => (int) $desa_val['positif']['dirawat'],
                        'konfirmasi_asymptomatik'   => (int) $desa_val['positif']['isolasi'], 
                        'konfirmasi_sembuh'         => (int) $desa_val['positif']['sembuh'], 
                        'konfirmasi_meninggal'      => (int) $desa_val['positif']['meninggal']
                    ));
                }
            }

            // ketika berhasil
            $schadule->setLastModife((int) time());
            $schadule->update();
            return true;
        }

        return false;
    }

    /** menampilkan data berserta rincian perdesa dalam satu waktu (filter)
     * @return array data covid per desa
     */
    public function result(): array
    {
        $grupByDate = [];
        foreach($this->_filters_waktu as $date){
            $this->db->query($this->queryBuilder());        
            $this->db->bind(':date', $date);
            $grupByDate[$date] = $this->db->resultset();
        }
        return $grupByDate;
    }

    /** menghitung resume data dalam satu waktu (semua data)
     * @return array resume data covid
     */
    public function result_countAll(): array
    {
        $this->db->query($this->queryBuilder_count(null));
        return $this->db->resultset();
    }
    
    /** menghitung resume data dalam satu waktu (filter)
     * @return array resume data covid
     */
    public function result_count(): array
    {
        if ($this->_filters_waktu == null ) return [];
        $date = implode(', ', $this->_filters_waktu);
        $this->db->query($this->queryBuilder_count($date));
        return $this->db->resultset();
    }

    /** list data yang tersedia di databse
     * @return array list tanggal format timestamp
     */
    public function listOfDate() :array
    {
        $this->db->query("SELECT `date` 
                            FROM `covid_tracker`
                            GROUP BY `date`
                            ORDER BY `date`
                            DESC");
        return $this->db->resultset();
    }

    // private method
    
    /** Query builder untuk menghitung jumlah per kasus dalam atu waktu
     * @param string tanggal data di-index
     * @return string pdo query string
     */
    private function queryBuilder_count($date): string
    {
        $where_statment = $date == null ? '' : "WHERE `date` IN ($date)";
        $query          = " SELECT 
                                covid_tracker.location,
                                covid_tracker.date                          AS date_create,               
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
                            $where_statment
                            GROUP BY `date`
                            ";
        return $query;
    }

    /** Query builder untuk mendapat data berupa query,
     * (urut Ascending berdasrkan id)
     * @return string pdo query string
     */
    private function queryBuilder(): string
    {
        $query = "SELECT 
                    desa_kecamatan.kecamatan,
                    desa_kecamatan.desa,
                    covid_tracker.id,
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
                ORDER BY `id` ASC
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

    /** menyimpan data baru kedalam database (row by row)
     * @param array $params array data yang akan disimpan
     * @return bool true jika data berhasil disimpan
     */
    private function creat(array $params = array (
        'date'                    => '', 
        'location'                => '',
        'suspek'                  => 0, 
        'suspek_discharded'       => 0, 
        'suspek_meninggal'        => 0, 
        'konfirmasi_symptomatik'  => 0,
        'konfirmasi_asymptomatik' => 0, 
        'konfirmasi_sembuh'       => 0, 
        'konfirmasi_meninggal'    => 0
    )): bool
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
