<?php
namespace Simpus\Simpus;

use Simpus\Database\MyPDO;
use Simpus\Helper\StringValidation;
/**
 * Perent Class fungsinya untuk menampung semua filed Rekam Medis
 */
class MedicalRecord{
    /** @var MyPDO Instant PDO */
    private $PDO;

    /** @var int id record */ 
    protected $_id;
    /** @var string No Rekam Medis */
    protected $_nomorRM;
    /** tanggal data dibuat */
    protected $_dataDibuat;   
    /** @var string Nama lengkap */
    protected $_nama;
    /** @var string tanggal lahir 30-12-1990*/
    protected $_tanggalLahir;
    /** @var string Almat tanpa rt rw */
    protected $_alamat;
    /** @var int Nomor Rt */
    protected $_nomorRt;
    /** @var int Nomor RW */
    protected $_nomorRw;    
    /** @var string Nama Kepla Keluarga*/
    protected $_namaKK;
    /** @var string No Rekam Medis Kepala Keluarga*/
    protected $_nomorRM_KK;
    /** @var string Status grub kesehatan (string in array) */
    protected $_status = "null";

    /** @var string last query used */
    private $_last_query;

#region getter dan setter
    // getter
    /**
     * get nomor rm
     * @return string nomor rekam medis
     */
    public function getNomorRM(){
        return $this->_nomorRM;
    }
    /**
     * get data dibuat
     * @return int tanggal dibuat
     */
    public function getDataDibuat(){
        return $this->_dataDibuat;
    }
    /**
     * get nama lengkap
     * @return string Nama lengkap pasien
     */    
    public function getNama(){
        return $this->_nama;
    }    
    /**
     * get tanggal lahir
     * @return string tanggal lahir
     */
    public function getTangalLahir(){
        return $this->_tanggalLahir;
    }
    /**
     * get alamat pasien jk luar wilayah tulis lengkap
     * @return string tanggal dibuat
     */
    public function getAlamat(){
        return $this->_alamat;
    }    
    /**  @return string Alamat Lengkap  */
    public function getAlamatLengkap(){
        return $this->_alamat . ' Rt '. $this->_nomorRt . '/ Rw ' . $this->_nomorRw;
    }
    /**
     * get nomor rt alamat dalam wilayah
     * @return int nomor rt
     */
    public function getNomorRt(){
        return $this->_nomorRt;
    }    
    /**
     * get nomor rw alamat dalam wilayah
     * @return int nomor rw
     */
    public function getNomorRw(){
        return $this->_nomorRw;
    }
    /**
     * get nama kepla keluarga
     * @return int tanggal dibuat
     */
    public function getNamaKK(){
        return $this->_namaKK;
    }
    /**
     * get nomor rekam medis kepala keluarga jika ada
     * @return int nomor rekam medis jka ada
     */
    public function getNomorRM_KK(){
        return $this->_nomorRM_KK;
    }
    /**
     * get status grup kesehatan
     * @return string Get status grup kesehatan
     */
    public function getStatus(){
        return $this->_status;
    }

    /**
     * get last use query
     * @return string last query used
     */
    public function getLastQuery():string{
        return $this->_last_query;
    }

    public function getData():array{
        return $this->convertToData();
    }

    // setter


    /**
     * set nomor rekam medis
     * 
     * data hanya disimpan jika format benar (nomor rm)
     * @param string $val 6 digit nomor rekam medis
     * 
     */
    public function setNomorRM($val){        
        $verify = StringValidation::NumberValidation($val,1,6);
        if( $verify){
            $len = strlen($val);
            $max = 6 - $len;
            for ($i=0; $i < $max ; $i++) { 
                $val = 0 . $val;
            }
            $this->_nomorRM = (string) $val;

        }
    }
    /**
     * set data dibuat dalam time span
     * @param string $val data dibuat 
     */
    public function setDataDibuat($val){
        $this->_dataDibuat = $val;
    }
    /**
     * set nama pasien
     * @param string $val nama pasien
     */
    public function setNama($val){
        $verify = StringValidation::NoHtmlTagValidation($val);
        if( $verify ){
            $val = strtolower($val);
            $this->_nama = $val; 
        }       
    }
    /**
     * set tanggal lahir
     * @param string $val tanggal lahir
     */
    public function setTanggalLahir($val){
        $this->_tanggalLahir = $val;
    }
    /**
     * set alamat tanpa rt rw
     * @param string $val alamat
     */
    public function setAlamat($val){
        $verify = StringValidation::NoHtmlTagValidation($val);
        if( $verify ){
            $val = strtolower($val);
            $this->_alamat = $val;
        }
    }
    /**
     * set nomor rt
     * @param string $val nomor rt
     */
    public function setNomorRt($val){
        $verify = StringValidation::NumberValidation($val,1,2);
        if( $verify ){
            $this->_nomorRt = (int) $val;
        }
    }
    /**
     * set nomor rw
     * @param string $val nomor rw
     */
    public function setNomorRw($val){
        $verify = StringValidation::NumberValidation($val,1,2);
        if( $verify ){
            $this->_nomorRw = (int) $val;
        }
    }
    /**
     * set nama kepal keluarga
     * @param string $val noma kepla keluarga
     */
    public function setNamaKK($val){
        $verify = StringValidation::NoHtmlTagValidation($val);
        if( $verify ){
            $val = strtolower($val);
            $this->_namaKK = $val;
        }
    }
    /**
     * set nomor rm kepala keluarga
     * @param string $val nomor rt
     */
    public function setNomorRM_KK($val){
        $verify = StringValidation::NumberValidation($val,0,6);
        if( $verify ){
            $len = strlen($val);
            $max = 6 - $len;
            for ($i=0; $i < $max ; $i++) { 
                $val = 0 . $val;
            }
            $this->_nomorRM_KK = $val;
        }
    }
    /**
     * Set status grup kesehatan
     */
    public function setStatus(string $val){
        $verify = StringValidation::NoHtmlTagValidation( $val );
        if( $verify ){
            $val = strtolower( $val );
            $this->_status = $val;
        }
    }

    /**
     * Convert array ke parameter
     * @param array $data Array to convert
     */
    public function convertFromArray(array $data){
        $this->convertFromData( $data );
    }
#endregion

    /** buat class baru */
    public function __construct($pdo = null){
        if($pdo == null){
            $this->PDO = new MyPDO();
        }else{
            $this->PDO = $pdo;
        }
    }    
    
    /**
     * buat kelas baru menggunkan id
     * 
     * **multy contruct dost support by php**
     * @param int $id 
     * @return MedicalRecord
     */
    public static function withId($id){
        $instance = new MedicalRecord();
        $instance->_id = $id;
        $instance->refresh();
        return $instance;
    }

    /**
     * buat kelas baru menggunkan data
     * 
     * **multy contruct dost support by php**
     * @param array $data
     * @return MedicalRecord
     */
    public static function withData($data){
        $instance = new MedicalRecord();
        $instance->convertFromData($data);
        return $instance;
    }

    /**
     * fungsi untuk menkonfersi data bentuk array menjadi properti kelas
     * 
     * @param array $data
     * merubah data array ke property class
     * - id             -> id
     * - nomorRM        -> nomor_rm     * 
     * - dataDibuat     -> data_dibuat
     * - nama           -> nama
     * - tanggalLahir   -> tanggal_lahir
     * - alamat         -> alamat
     * - nomorRt        -> nomor_rt
     * - nomorRw        -> nomor_rw
     * - namaKK         -> nama_kk
     * - nomorRM_KK     -> nomor_rm_kk
     * - status         -> status
     * 
     */
    private function convertFromData($data){
        $this->_id           = $data['id'] ?? $this->_id;;
        $this->_nomorRM      = $data['nomor_rm'] ?? $this->_nomorRM;;
        $this->_dataDibuat   = $data['data_dibuat'] ?? $this->_dataDibuat;
        $this->_nama         = $data['nama'] ?? $this->_nama;
        $this->_tanggalLahir = $data['tanggal_lahir'] ?? $this->_tanggalLahir;
        $this->_alamat       = $data['alamat'] ?? $this->_alamat;
        $this->_nomorRt      = $data['nomor_rt'] ?? $this->_nomorRt;
        $this->_nomorRw      = $data['nomor_rw'] ?? $this->_nomorRw;
        $this->_namaKK       = $data['nama_kk'] ?? $this->_namaKK;
        $this->_nomorRM_KK   = $data['nomor_rm_kk'] ?? $this->_nomorRM_KK;
        $this->_status       = $data['status'] ?? $this->_status;
    }
    /**
     * fungsinya untuk mengkonfersi proprti kelas ke data array
     * @return array data dalam bentuk array assosiatif
     */
    private function convertToData(){
        $data = [];
        $data['nomor_rm'] = $this->_nomorRM;
        $data['data_dibuat'] = $this->_dataDibuat;
        $data['nama'] = $this->_nama;
        $data['tanggal_lahir'] = $this->_tanggalLahir;
        $data['alamat'] = $this->_alamat;
        $data['nomor_rt'] = $this->_nomorRt;
        $data['nomor_rm'] = $this->_nomorRw;
        $data['nama_kk'] = $this->_namaKK;
        $data['nomor_rm_kk'] = $this->_nomorRM_KK;
        $data['status'] = $this->_status;

        return $data;
    }

    /**
     * refresh/ambil/pull semua data dari database, munggunakn id
     * @return boolean 
     * bila berhasil di refresh nilainya true
     */
    public function refresh(){
        # memuat ulang data dari data base menggunakn id
        $db = new MyPDO();
        $db->query("SELECT * FROM `data_rm` WHERE `id` = :id");
        $db->bind(':id', $this->_id);
        if( $db->single() ){
            $this->convertFromData( $db->single() );
            return true;
        }
        return false;
    }

    /**
     * refresh/ambil/pull semua data dari database, munggunakn hash_id
     * @return boolean 
     * True jika data berhasil diambil
     */
    public function refreshUsingIdHash($Id_hash){
        // ambil id dari hash_code nya
        $this->PDO->query("SELECT *
                    FROM(
                        SELECT
                            *
                        FROM
                            `data_rm`
                        UNION
                        SELECT
                            *
                        FROM
                            `staging_rm`
                        ) AS U
                    WHERE
                        U.data_dibuat = :tmstamp                        
                    ");
        $this->PDO->bind(":tmstamp", $Id_hash);
        if( $this->PDO->single() ){
            $this->convertFromData( $this->PDO->single() );
            return true;
        }
        return false;
    }

    // method
    /**
     *  update / simpan data ke data base
     * @return boolean 
     * bila berhasil disimpan nilaninya true
     */
    public function save($table_nama = "data_rm"){
        $table_nama = $table_nama == 'data_rm' ? 'data_rm' : 'staging_rm';              // mencegah input nama table lain
        # memuat ulang data dari data base menggunakn id
        $id = $this->_id;
        $nomor_rm = $this->_nomorRM;
        $nama =  $this->_nama;
        $data_dibuat = $this->_dataDibuat;
        $tanggal_lahir = $this->_tanggalLahir;
        $alamat = $this->_alamat;
        $nomor_rt = $this->_nomorRt;
        $nomor_rw = $this->_nomorRw;
        $nama_kk = $this->_namaKK;
        $nomor_rm_kk = $this->_nomorRM_KK;
        $status = $this->_status;
        
        # jika nama dan no rm kosong tidak disimpan
        if( $nomor_rm == '' && $nama == '') return false;  

        $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        $query = "UPDATE `$table_nama` SET 
                                    `nomor_rm` = '$nomor_rm',
                                    `data_dibuat` = '$data_dibuat',
                                    `nama` = '$nama',
                                    `tanggal_lahir` = '$tanggal_lahir',
                                    `alamat` = '$alamat',
                                    `nomor_rt` = '$nomor_rt',
                                    `nomor_rw` = '$nomor_rw',
                                    `nama_kk` = '$nama_kk',
                                    `nomor_rm_kk` = '$nomor_rm_kk',
                                    `status` = '$status'
                                WHERE `id` = '$id' ";
        #simpan query
        mysqli_query($link, $query);
        # bila berhasil return true
        if( mysqli_affected_rows($link) > 0){
            $this->_last_query = $query;
            return true;
        }
        #defult nya adalah salah
        return false;
    }

    /**
     * delet rm ini dari data base
     * @return boolean 
     * bila berhasil dihapus nilainya true
     */
    public function delete(){
        $id = $this->_id;

        $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        $query = "DELETE FROM data_rm WHERE id  = $id";

        #esekusi query /delet
        mysqli_query($link, $query);
        # bila berhasil return true
        if( mysqli_affected_rows($link) > 0){            
            $this->_last_query = $query;
            return true;
        }
        #defult nya adalah salah
        return false;
    }

    /**
     * pre validasi semua isi form sebelum di esekusi perintah berikutnya
     * berisis warning jika ada fileld yg salah
     * 
     * @return array list
     * - [0] => true / false
     * - [1] => nomor rm format tidak didukung
     * - [2] => format nama tidak didukung
     * - [3] => format tanggal lahir tidak didukung
     * - [4] => format alamat tidak didukung
     * - [5] => format nomor rt tidak didukung
     */
    public function preValidation(){
        $error_msg = [];
        return $error_msg;
    }

    /**
     * membuat data rm baru kedata base
     * 
     * @param int $id
     * opsonal bila kosong maka akan diteruskan dari id terakhir
     * @return boolean 
     * bila berhasil disimpan nilainya true
     */
    public function insertNewOne($id = '', $table_name = 'data_rm'){
        # menimpan data ke data base menggunakn id
        $nomor_rm = $this->_nomorRM;
        $data_dibuat = (int) $this->_dataDibuat;
        $nama = $this->_nama;
        $tanggal_lahir = $this->_tanggalLahir;
        $alamat = $this->_alamat;
        $nomor_rt = (int) $this->_nomorRt;
        $nomor_rw = (int) $this->_nomorRw;
        $nama_kk = $this->_namaKK;
        $nomor_rm_kk = $this->_nomorRM_KK;
        $status = $this->_status;
        
        # jika nama dan no rm kosong tidak disimpan
        if( $nomor_rm == '' && $nama == '') return false;        
        
        $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        $query = "INSERT INTO `$table_name` VALUES ('$id', 
                                       '$nomor_rm',
                                       '$data_dibuat',
                                       '$nama',
                                       '$tanggal_lahir',
                                       '$alamat',
                                       '$nomor_rt',
                                       '$nomor_rw',
                                       '$nama_kk',
                                       '$nomor_rm_kk',
                                       '$status'
                                       )";
            
        #esekusi query
        mysqli_query($link, $query);
        # bila berhasil return true
        if( mysqli_affected_rows($link) > 0){
            $this->_last_query = $query;
            return true;
        }
        #defult nya adalah salah
        return false;
    }

    public function cekAxis(){
        # memuat ulang data dari data base menggunakn id
        $id = $this->_id;
        $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        $query = mysqli_query($link, "SELECT id FROM data_rm WHERE id = '$id' ");
        if( mysqli_num_rows( $query ) == 1 ){
            return true;
        }
        return false;
    }

}
