<?php
/**
 * class ini berfungsi untuk mendapatkan jadwal kia yang daimambil dari data base
 * 
 * @author sonypradana@gmail.com
 */
class jadwalKIA{
    /** @var int Bulan dalam angka */
    private $_month;
    /** @var int Tahun */
    private $_year;

    /**
     * Membuat kelas untuk memenage jadwal poli KIA
     * @param string $bulan Bulan dalam angka
     * @param string $year Tahun
     */
    public function __construct(string $bulan, string $tahun = "2020"){
        // cek bulan sudah terdaftar atau belum
        $this->_month = $bulan;
        $this->_year = $tahun;
    }

    public function getdata():array{
        $month = $this->_month;
        $year  = $this->_year;

        // array untuk dikembalikan di result
        $date = [];
        $data = [];
        $first_week = [];   $date_fw = date("Y-m-d", strtotime("first friday $year-$month"));
        $third_week = [];   $date_tw = date("Y-m-d", strtotime("third friday $year-$month"));

        // koneksi data base ambil berdasarkan kriteria yang dibuat (bulan / tahun)
        $db = new MyPDO();
        $db->query("SELECT `date`, `event_detail` FROM `list_of_services` WHERE `event`=:event AND MONTH(Date) = :m");
        $db->bind(':event', "imunisasi anak");
        $db->bind(':m', $month);
        // mengisi array sesuai hasil ditemukan didata base
        foreach( $db->resultset() as $row){
            // mengisi array jenis vaksin
            $data[$row['event_detail']][] = date("d M", strtotime($row['date']));
            // mengisi array tanggal berdasarkan jenis vaksin
            $date[] = date("d M", strtotime($row['date']));
            
            // mendapatkan list vakin berdasarkan minggun I & III
            if( $date_fw == $row['date']){
                $first_week[] = $row['event_detail'];
            }
            if( $date_tw == $row['date']){
                $third_week[] = $row['event_detail'];
            }
        }

        // mengurutkan dan menghapus duplikat
        $date = array_values( array_unique( $date ) );
        
        // menyusun hasil data
        $result = [
            "version" => "1.0",
            "bulan" => date("M Y", strtotime("$year-$month-1")),
            "jadwal" => $date,
            "jumat pertama" => $first_week,
            "jumat ketiga" => $third_week,
            "data" => $data
        ];

        // kembalian array
        return $result;
    }

    /**
     * Mengambil list bulan yang sudah terisi
     */
    public static function getAvilabeMonth():array{
        // Koneksi data base
        $db = new MyPDO();
        $db->query("SELECT `date` FROM `list_of_services` WHERE `event`=:event");
        $db->bind(':event', 'imunisasi anak');
        $arr = [];
        // mengkovert foormat dari YYYY-MM-dd menjai M
        foreach( $db->resultset() as $row){
            $arr[] = date("m", strtotime($row['date']));
        }
        // List bulan yang sudah tersedia
        return array_unique($arr);
    }

    /**
     * Mengecek jadwal sudah dibuat ata belum (data tidak boleh kembar) 
     * @param string $date Tanggal / Jadwal format YYYY-MM-DD
     * @param string $vaksin Jenis vaksin
     * @return bool True ketika data sudah ada
     */
    public function cekJadwal($date, $vaksin):bool{
        // koneksi data base
        $db = new MyPDO();
        $db->query("SELECT `date`, `event_detail` FROM `list_of_services` WHERE `date`=:tanggal AND `event_detail`= :ev_dt");
        $db->bind(':tanggal', $date);
        $db->bind(':ev_dt', $vaksin);
        if( $db->single() ){
            // data sudah ada
            return true;
        }
        // data tidak ditemukan
        return false;
    }

    /**
     * Membuat jadwal imunisai poli kia
     * @param string $date Jadwal pelyanan dengan format YYYY-MM-DD
     * @param string $vaksin Jenis vaksin
     * @return bool True kita data berhasil dibuat/disimpan
     */
    public function buatJadwal($date, $vaksin):bool{
        // cek data kembar
        if( $this->cekJadwal($date, $vaksin) ) return false;
        // koneksi data base
        $db = new MyPDO();
        $db->query("INSERT INTO `list_of_services` (`id`, `date`, `unit`, `event`, `event_detail`) VALUES (:id, :tanggal, :unit, :ev, :ev_dt )");
        $db->bind(':id', "");
        $db->bind(':tanggal', $date);
        $db->bind(':unit', "kia");
        $db->bind(':ev', "imunisasi anak");
        $db->bind(':ev_dt', $vaksin);
        $db->execute();
        if( $db->rowCount() > 0){
            // data berhasil disimpan
            return true;
        }
        // data gagal sisimpan
        return false;
    }

    /**
     * Mengedit jadwal Pelayan KIA
     * @param string $from_date     Tanggal yang ingin dirubah
     * @param string $from_vaksin   Jenis vaksin yang ingin dirubah
     * @param string $to_date       Tanggal baru
     * @param string $to_vaksin   Jenis vaksin baru
     */
    public function editJadwal($from_date, $from_vaksin, $to_date, $to_vaksin){
        // koneksi data base
        $db = new MyPDO();
        $db->query("UPDATE `list_of_services` SET `date` = :t_tanggal', `event_detail` = :t_ev_dt WHERE `date` = :f_tanggal AND `event_detail`= :f_ev_dt");
        $db->bind(':f_tanggal', $from_date);
        $db->bind(':f_ev_dt', $from_vaksin);
        $db->bind(':t_tanggal', $to_date);
        $db->bind(':t_ev_dt', $to_vaksin);
        $db->execute();
    }

    /**
     * Membuat jadwal imunisasi dalam satu bulan secara otomatis, hari libur tetap di index
     * @param int $bulan Bulan yang akan di index
     * @param int $tahun Tahun yang akan di index (base on bulan)     * 
     */
    public function autoCreatJadwal(int $bulan, int $tahun){
        // mengambil  jadwal jumat pertama dan ketiga
        $jumat_pertama = date("Y-m-d", strtotime("first friday $tahun-$bulan"));
        $jumat_ketiga  = date("Y-m-d", strtotime("third friday $tahun-$bulan"));

        // loop hari setiap jumat pada bulan XXX
        $date = new DateTime("first Friday $tahun-$bulan");
        $thisMonth = $date->format('m');
        while ($date->format('m') === $thisMonth) {
            // hasil -> tanggal pada hari jumat
            $hari_ini = $date->format('Y-m-d');

            if( $hari_ini == $jumat_pertama ){
                // membuat jadwal pada jumat pertama
                $this->buatJadwal($hari_ini, "BCG");
                $this->buatJadwal($hari_ini, "Campak");
                $this->buatJadwal($hari_ini, "Rubella (MR)");
            }elseif( $hari_ini == $jumat_ketiga ){
                // membuat jadwal pada jumat ketiga
                $this->buatJadwal($hari_ini, "Campak");
                $this->buatJadwal($hari_ini, "Rubella (MR)");
            }
            // membuat jadwal pada setiap hari jumat
            $this->buatJadwal($hari_ini, "Hib");
            $this->buatJadwal($hari_ini, "HB");
            $this->buatJadwal($hari_ini, "DPT");
            $this->buatJadwal($hari_ini, "IPV");
            
            // next looping hari jumat
            $date->modify('next Friday');
        }
    }

}
