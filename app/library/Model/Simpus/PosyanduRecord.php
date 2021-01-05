<?php namespace Model\Simpus;

use System\Database\MyPDO;
use \PDO;

class PosyanduRecord
{    
    /** @var MyPDO Instant PDO */
    private $PDO;
    private $_code_hash;
    private $_isValid = false;

    private $_tanggalPemeriksaan;
    private $_tenagaPemeriksaan;
    private $_jenisPemeriksaan;
    private $_tempatPemeriksaan;
    
    // data kes
    private $_tinggiBadan;
    private $_beratBadan;
    private $_details; // dalam format json
    // helper
    private $_dataTersimpan = false;

    // getter
    public function getTenagaPemeriksaan():string{
        return $this->_tenagaPemeriksaan;
    }
    public function getTanggalPemeriksaan():string{
        return $this->_tanggalPemeriksaan;
    }
    public function getJenisPemeriksaan():string{
        return $this->_jenisPemeriksaan;
    }
    public function getTempatPemeriksaan():int{
        return $this->_tempatPemeriksaan;
    }
    public function getTinggiBadan():int{
        return $this->_tinggiBadan;
    }
    public function getBeratBadan():int{
        return $this->_beratBadan;
    }
    public function getDetails():int{
        return $this->_details;
    }
    public function IsValided():bool{
        return $this->_isValid;
    }

    // setter
    public function setTenagaPemeriksaan(string $val){
        $this->_tenagaPemeriksaan = $val;
        return $this;
    }
    public function setTanggalPemeriksaan(string $val){
        $this->_tanggalPemeriksaan = $val;
        return $this;
    }
    public function setJenisPemeriksaan(string $val){
        $this->_jenisPemeriksaan = $val;
        return $this;
    }
    public function setTempatPemeriksaan(string $val){
        $this->_tempatPemeriksaan = $val;
        return $this;
    }
    public function setTinggiBadan(int $val){
        $this->_tinggiBadan = $val;
        return $this;
    }
    public function setBeratBadan(int $val){
        $this->_beratBadan = $val;
        return $this;
    }
    public function setDetails(string $val){
        $this->_details = $val;
        return $this;
    }

    /**
     * Constructer
     * 
     * After make new instant you MUST validate before you can make CRUD
     * @param MyPDO $PDO DataBase class Dependency Injection
     */
    public function __construct(MyPDO $PDO = null)
    {
        $this->PDO = $PDO ?? new MyPDO();
        $this->_isValid = false;
    }
    
    public function validate(string $code_hash)
    {
        $this->_isValid = $this->isValid($code_hash);      //  TODO data pertama selalu bernilai true
        $this->_code_hash = $code_hash;
        return $this;
    }

    // function
    private function isValid($code_hash): bool
    {
        if( $code_hash == 0) return false;
        $this->PDO->query("SELECT `id_hash` FROM `data_kia_anak` WHERE `id_hash` = :id_hash LIMIT 1");
        $this->PDO->bind(":id_hash", $code_hash, PDO::PARAM_STR);
        if( $this->PDO->single() ){    
            return true;
        }
        return false;
    }

    public function isIDExist($id){
         $this->PDO->query("SELECT `id_hash` FROM `data_posyandu` WHERE `id` = :id LIMIT 1");
         $this->PDO->bind(":id_hash", $id, PDO::PARAM_INT);
        if(  $this->PDO->single() ){    
            return true;
        }
        return false;
    }

    public function convertFromArray(array $array_data){
        $this->_tanggalPemeriksaan = $array_data['tanggal_pemeriksaan'] ?? date("m/d/Y", time());
        $this->_tenagaPemeriksaan  = $array_data['tenaga_pemeriksaan']  ?? 'animus';
        $this->_jenisPemeriksaan   = $array_data['jenis_pemeriksaan']   ?? 'posyandu';
        $this->_tempatPemeriksaan  = $array_data['tempat_pemeriksaan']  ?? 0;
        // data kesehatan
        $this->_tinggiBadan        = $array_data['tinggi_badan'] ?? 0;
        $this->_beratBadan         = $array_data['berat_badan']  ?? 0;
        $this->_details            = $array_data['details']      ?? '{}';
    }

    // function CRUD
    public function read($id) :bool
    {
        if( $this->_isValid ){
             $this->PDO->query("SELECT
                            *
                        FROM
                            `data_posyandu`
                        WHERE
                            `id_hash` = :code_hash
                            AND
                            `id` = :id
                        ");
             $this->PDO->bind(':code_hash', $this->_code_hash);
             $this->PDO->bind(':id', $id);
            if(  $this->PDO-> single() ){
                $this->convertFromArray( $this->PDO->single());
                return true;
            }
        }
        return false;
    }


    public function creat() :bool
    {
        if( $this->_isValid  ){
             $this->PDO->query("INSERT INTO
                            `data_posyandu`
                        VALUE (
                            :id,
                            :id_hash,
                            :tanggal_pemeriksaan,
                            :tenaga_pemeriksaan,
                            :jenis_pemeriksaan,
                            :tempat_pemeriksaan,
                            :tinggi_badan,
                            :berat_badan,
                            :details
                        )");
             $this->PDO->bind(':id', "");
             $this->PDO->bind(":id_hash", $this->_code_hash);
             $this->PDO->bind(":tanggal_pemeriksaan", $this->_tanggalPemeriksaan);
             $this->PDO->bind(":tenaga_pemeriksaan", $this->_tenagaPemeriksaan);
             $this->PDO->bind(":jenis_pemeriksaan", $this->_jenisPemeriksaan);
             $this->PDO->bind(":tempat_pemeriksaan", $this->_tempatPemeriksaan, PDO::PARAM_INT);
             $this->PDO->bind(":tinggi_badan", $this->_tinggiBadan, PDO::PARAM_INT);
             $this->PDO->bind(":berat_badan", $this->_beratBadan, PDO::PARAM_INT);
             $this->PDO->bind(":details", $this->_details);
            // simpan ke database biodata
             $this->PDO->execute();
            if(  $this->PDO->rowCount() > 0){
                return true;
            }
        }
        return false;
    }

    /**
     * Dont use covertFromArray() to prevent null/empety data
     */
    public function update($id) :bool
    {
        if( $this->_isValid ){
             $this->PDO->query("UPDATE
                            `data_posyandu`
                        SET
                            `tanggal_pemeriksaan` = :tanggal_pemeriksaan,
                            `tenaga_pemeriksaan` = :tenaga_pemeriksaan,
                            `jenis_pemeriksaan` = :jenis_pemeriksaan,
                            `tempat_pemeriksaan` = :tempat_pemeriksaan,
                            `tinggi_badan` = :tinggi_badan,
                            `berat_badan` = :berat_badan,
                            `details` = :details
                        WHERE
                            `id` = :id
                            AND
                            `id_hash` = :id_hash
                        ");
             $this->PDO->bind(":tanggal_pemeriksaan", $this->_tanggalPemeriksaan);
             $this->PDO->bind(":tenaga_pemeriksaan", $this->_tenagaPemeriksaan);
             $this->PDO->bind(":jenis_pemeriksaan", $this->_jenisPemeriksaan);
             $this->PDO->bind(":tempat_pemeriksaan", $this->_tempatPemeriksaan);
             $this->PDO->bind(":tinggi_badan", $this->_tinggiBadan);
             $this->PDO->bind(":berat_badan", $this->_beratBadan);
             $this->PDO->bind(":details", $this->_details);
             $this->PDO->bind(":id_hash", $this->_code_hash);
             $this->PDO->bind(":id", $id);
            // update biodata
             $this->PDO->execute();
            if(  $this->PDO->rowCount() > 0){
                return true;
            }
        }
        return false;
    }
}
