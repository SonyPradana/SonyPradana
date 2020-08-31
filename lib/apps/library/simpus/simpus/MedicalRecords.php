<?php

namespace Simpus\Simpus;

use Simpus\Database\MyPDO;
use Simpus\Helper\StringValidation;

/**
 * class ini befungsi untuk mengambil data rekam medis dari data base
 * dengan parameter tertentu, seperti nama, tanggal lahir alamat, dan nama kepala keluarga
 * (mengumpulkan data berdasarkan filter yg ada) 
 * 
 * @author sonypradana@gmail.com
 */
class MedicalRecords{
    # filert by
    /** @var string 6 digit */
    private $_filter_nomor_rm;
    private $_filter_nama;
    private $_filter_tanggal_lahir;
    private $_filter_alamat;    
    private $_filter_rt;
    private $_filter_rw;
    private $_filter_nama_kk;
    private $_filter_noRm_kk;

    # filter add
    private $_filters_alamat = [];

    #costume filter
    private $_filter_range_min_tgl;
    private $_filter_range_max_tgl;
    private $_filter_status_kk = false;

    #konfigurasi    
    private $_current_pos = 1;  # set oleh user
    private $_limit = 10;
    private $_sort = 'id';
    /** @var string order by ACD | DEC */
    private $_order = "ASC";    

    #property
    public function filterByNomorRm($val){        
        $verify = StringValidation::NumberValidation($val, 1,6);
        if ($verify) {
            $len = strlen($val);
            $max = 6 - $len;
            for ($i=0; $i < $max ; $i++) { 
                $val = 0 . $val;
            }
            $this->_filter_nomor_rm = $val;
        }
    }
    /** 
     * @param string $val filter berdasarkan nama 
     * */
    public function filterByNama($val){
        $verify = StringValidation::NoHtmlTagValidation($val);
        if( $verify){
            $val = strtolower($val);
            $this->_filter_nama = $val;
        }
    }    
    /** 
     * @param string $val filter menurut tanggal lahir 
     */
    public function filterByTgl($val){
        $this->_filter_tanggal_lahir = $val;
    }
    /** 
     * @param string $val filter alamat tanpa rt / rw 
     */
    public function filterByAlamat($val){  
        $verify = StringValidation::NoHtmlTagValidation($val);
        if( $verify){
            $val = strtolower($val);
            $this->_filter_alamat = $val;
        }
    }
    /** 
     * @param string $val filter berdasarkan rt
     */
    public function filterByRt($val){
        if( is_numeric($val) ){
            $this->_filter_rt = (int) $val;
        }
    }
    /** 
     * @param string $val filter nama 
     */
    public function filterByRw($val){
        if( is_numeric($val) ){
            $this->_filter_rw = (int) $val;
        }
    }
    /** 
      * @param string $val filter nama kepala keluarga
      */
    public function filterByNamaKK($val){
        $verify = StringValidation::NoHtmlTagValidation($val);
        if ($verify){
            $val = strtolower($val);
            $this->_filter_nama_kk = $val;
        }
    }
    /** 
     * @param string $val filter nomor rm kepala keluarga 
     */
    public function filterByNomorRmKK($val){
        $verify = StringValidation::NumberValidation($val, 1,6);
        if ($verify) {
            $len = strlen($val);
            $max = 6 - $len;
            for ($i=0; $i < $max ; $i++) { 
                $val = 0 . $val;
            }
            $this->_filter_noRm_kk = $val;
        }
    }   
    /**
     * filter tanggal berdasarkan selisih tahun
     * @param date $minVal tahun termuda
     * @param date $maxVal tahun tertua
     */
    public function filterRangeTanggalLahir($minVal, $maxVal){
        //handle int
        // if( is_numeric($minVal) && is_numeric($maxVal)){
            #min max handle
            // $minVal = $minVal > 0 ? $minVal : 0;
            // $maxVal = $maxVal < $minVal ? $minVal : $maxVal;
            
            $this->_filter_range_min_tgl = $minVal;
            $this->_filter_range_max_tgl = $maxVal;
        // }
    }

    /**
     * filter berdasarkan alamat-alamat
     * 
     * fungsi ini bisa ditulis berulang
     * @param string $val filter alamat tanpa rt / rw
     */
    public function filtersAddAlamat($val){        
        $verify = StringValidation::NoHtmlTagValidation($val);
        if( $verify){
            $val = strtolower($val);
            $this->_filters_alamat[] = $val;
        }
    }

    /**
     * filter berdasarkan satus kepala keluarga
     * @param boolean $val Staus kepala keluarga
     */
    public function filterStatusKK($val = true){
        if( $val ===true){
            $this->_filter_status_kk = true;
        }else{
            $this->_filter_status_kk = false;
        }
    }

    /**
     * Mengambil hasil full query dari query yang dibuat (query yang digunakan di result).
     * rekomendasi:
     * - filter = true,  strict = true
     * - filter = false, strict = false
     * @param  bool   $filters query menggunakan multi search atau tidak
     * @param  bool   $strict  true, query menggunakan logica AND
     * @return string query string (sebelum di esekusi / dipanggil)
     */
    public function getQuery(bool $filters = false, bool $strict = false):string{
        $filter_type = $filters ? $this->filters( $strict ) : $this->filter( $strict );

        return $this->query( $filter_type );
    }

    /**
     * Mengambil hasil 'where condition' dari query yang dibuat.
     * rekomendasi:
     * - filter = true,  strict = true
     * - filter = false, strict = false
     * @param  bool   $filters query menggunakan multi search atau tidak
     * @param  bool   $strict  true, query menggunakan logica AND
     * @return string filter string (raw dari query)
     */
    public function getQueryStatment(bool $filters = false, bool $strict = false):string{
        return $filters ? $this->filters( $strict ) : $this->filter( $strict );
    }

    /**
     * jumlah data yang ditampilkan per halaman
     * min 10, maks 100
     */
    public function limitView($val){
        $verify = StringValidation::NumberValidation($val, 1, 3);
        if( $verify){
            $val = $val < 10 ? 10 : $val;
            $val = $val > 100 ? 100 : $val;
            $this->_limit =  $val;
        }
    }
    /**
     * jumlah data yang ditampilkan per halaman
     * paksa sesaui angak yg dikehendaki
     */
    public function forceLimitView($val){
        $verify = StringValidation::NumberValidation($val, 1, 3);
        if( $verify){
            $this->_limit =  $val;
        }
    }

    private $_sortSupport = ['id', 'nomor_rm', 'nama', 'tanggal_lahir', 'alamat', 'nomor_rw', 'nama_kk', 'nomor_rm_kk'];
    /**
     * Mengurutkan data berdasarkan kategory
     * 
     * 'id', 'nomor_rm', 'nama', 'tanggal_lahir', 'alamat', 'nomor_rw', 'nama_kk', 'nomor_rm_kk'
     * @param string $val mengurutkan berdasarkan category
     */
    public function sortUsing($val){
        $val = strtolower($val);
        $this->_sort = in_array($val, $this->_sortSupport) ? $val : 'id';
    }

    /**
     * Mengurutkan data secara Ascending (Membesar) / Descending (Mengecil)
     * @param string $val ASC|DESC
     */
    public function orderUsing($val = "ASC"){
        $val = strtoupper($val);
        if( $val == "ASC"){
            $this->_order = "ASC";
        }else{
            $this->_order = "DESC";
        }
    }

    /**
     * me-set posisi halaman, posisi halaman tergantung dengan jumalah data dan data yg ditampilkan setiap halam.
     * 
     * bila posisi halman tidak disetting halman akan menunjuk pada halaman awal
     * @param interger $val posisi halaman
     */
    public function currentPage($val){
        $val = is_numeric( $val ) ? $val : 1;
        $val = $val < 1 ? 1 : $val;
        $val = $val > 100 ? 100 : $val;
        $val= floor($val);

        $this->_current_pos = $val;
    }

    /**
     * menampilkan jumlah data yg disajikan setalah filter di atur
     * 
     * @return int jumlah data 
     */
    public function maxData(){
        $query_filter = $this->filter();
        $query_filters = $this->filters(false);

        $merge_filter = $query_filter . $query_filters;   
        $merge_filter = $merge_filter != '' ? "WHERE " . $merge_filter : "";

        $query = "SELECT COUNT(id) FROM data_rm $merge_filter";

        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");      
        $result = mysqli_query($link, $query);
        $feedback = mysqli_fetch_assoc( $result );

        return (int) $feedback['COUNT(id)'];    
    }

    /**
     * jumlah halaman maksimum yang dapat di tampilkan
     * 
     * @return interger jumlah halaman
     */
    public function maxPage(){
        return (int) ceil($this->maxData() / $this->_limit);
    }

    /**
     * mengambil column data base yang dapat di sort/diorder
     * @return array nama column
     */
    public function getColumnSupport():array{
        return $this->_sortSupport;
    }

    public function __construct(){
        
    }   

    private function query(string $query_filter){
        $sort  = $this->_sort;
        $order = $this->_order;
        $start_data = ($this->_current_pos * $this->_limit) - $this->_limit;
        $limit = $this->_limit;
        $sort_order = ', tanggal_lahir';
        return "SELECT * FROM data_rm WHERE $query_filter ORDER BY $sort $order $sort_order LIMIT $start_data, $limit";  
    }

    /**
     * filter hasil query data base berdasarkan proprty yg telah ditentukan sebeeelumnya
     * filter dapat dikombinasikan atau di filter tunggal
     * 
     * @param boolean $stirich
     * - ***true*** = menggnakan logia **"and"** (hasil harus sesui dengan property)
     * - ***false*** = menggukan logika **"or"** (hasil query lebih fleksibel)
     * @return string string query
     */
    private function filter($stirich = true){
        # buat string query
        $q = [];
        $q[] = $this->queryBuilder( 'nomor_rm', $this->_filter_nomor_rm);
        $q[] = $this->queryBuilder( 'nama', $this->_filter_nama);
        $q[] = $this->queryBuilder( 'tanggal_lahir', $this->_filter_tanggal_lahir);
        $q[] = $this->queryBuilder( 'alamat', $this->_filter_alamat);
        $q[] = $this->queryBuilder( 'nomor_rt', $this->_filter_rt, false);
        $q[] = $this->queryBuilder( 'nomor_rw', $this->_filter_rw, false);
        $q[] = $this->queryBuilder( 'nama_kk', $this->_filter_nama_kk);
        $q[] = $this->queryBuilder( 'nomor_rm_kk', $this->_filter_noRm_kk, false);            
       
        $query = '';        
        if( $stirich ){
            #oprasi and (harus ada)            
            foreach( $q as $res){
                if( $res == '' ) continue;
                $query .= ($res) ?  $res . ' AND ': '' ;
            }           

            $strLen = strlen ($query) ;
            $query = substr_replace($query, '', $strLen - 5, -1);
        }else{            
            #oprasi or (salah satu harus ada)                    
            foreach( $q as $res){
                if( $res == '' ) continue;
                $query .= ($res) ?  $res . ' OR ': '' ;
            }           

            $strLen = strlen ($query) ;
            $query = substr_replace($query, '', $strLen - 4, -1);
        }
        return $query;

    }
    /** helper untuk menkonvert proprty ke string query
     * @param string $parameter nama kolom data base
     * @param string $obj isi kolom data base
     * @param boolean $use_persent 
     * - true : mencari value / string daintara text yg ada
     * - false " mecari data sesuai value mutlak sama
     */
    private function queryBuilder($parameter, $obj, $use_persent = true){
        if( isset( $obj ) AND $obj != ''){
            if( $use_persent ){
                return "($parameter LIKE '%$obj%')";
            } else {
                return "($parameter LIKE '$obj')";
            }
        }
        return '';
    }
    /**
     * filter hasil query data base berdasarkan proprty yg telah ditentukan sebeeelumnya. 
     * filter dapat dikombinasikan atau di filter tunggal.
     * 
     * Dalam satu property bisa memiliki nilai/value lebih dari satu
     * 
     * @param boolean $stirich
     * - ***true*** = menggnakan logia **"and"** (hasil harus sesui dengan property)
     * - ***false*** = menggukan logika **"or"** (hasil query lebih fleksibel)
     * @return string string query     * 
     */
    private function filters($stirich = true){
        $q = []; // query builder

        $filters_alamat = $this->_filters_alamat;
        foreach( $filters_alamat as $f){
            $q[] = $this->queryBuilder( 'alamat', $f);
        }

        $query = '';        
        if( $stirich ){
            #oprasi and (harus ada)            
            foreach( $q as $res){
                if( $res == '' ) continue;
                $query .= ($res) ?  $res . ' AND ': '' ;
            }           

            $strLen = strlen ($query) ;
            $query = substr_replace($query, '', $strLen - 5, -1);
        }else{            
            #oprasi or (salah satu harus ada)                    
            foreach( $q as $res){
                if( $res == '' ) continue;
                $query .= ($res) ?  $res . ' OR ': '' ;
            }           

            $strLen = strlen ($query) ;
            $query = substr_replace($query, '', $strLen - 4, -1);
        }
        // enkapsulasi query
        $query = $query == '' ? $query : '( ' . $query . ') ';
        # menambah query lain
        # query range
        $min = $this->_filter_range_min_tgl;
        $max = $this->_filter_range_max_tgl;
        if( isset($min) && isset($max)){
            $AND = $query != "" ? "AND " : "";
            $query .=  "$AND(tanggal_lahir BETWEEN DATE('$max') AND DATE('$min'))";
        }
        # query status kk
        if( $this->_filter_status_kk ){
            $AND = $query != "" ? "AND " : "";
            $query .=  "$AND(nomor_rm = nomor_rm_kk)";
        }
        
        return $query;
    }

    /** Mereset semua parameter menjadi null */
    public function reset(){
       $this->_filter_nomor_rm = ''; 
       $this->_filter_nama = '';   
       $this->_filter_tanggal_lahir = '';   
       $this->_filter_alamat = '';   
       $this->_filter_rt = '';   
       $this->_filter_rw = '';   
       $this->_filter_nama_kk = '';   
       $this->_filter_noRm_kk = '';   
        # other
        $this->_filters_alamat = [];
    }    

    /** 
     * Hasil dari view berupa array data/ json
     * Hasil dapat diatur menggunkan filter 
     * 
     * @param boolean $strict 
     * - true = menggunkan logica AND, semua paremeter harus terpenuhi
     * - false = menggunakan logica Or, salah parameter terpenuhi
     * @param boolean $convert_To_Json
     * - true = mengmbalilkan data berbentuk json
     * - false = mengembalikan data berbentuk array data
     * @return array or json
     */
    public function result($strict = true, $convert_To_Json = false){
        # koneksi data base
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        # query data 
        $queryFilter = $this->filter($strict);
        if( $queryFilter == ''){
            # mencegah (query kosong) ketika filter tidak di setting
            return [];
        } else{
            $query = $this->query( $queryFilter );
        }
        # mengambil dari table
        $result = mysqli_query($link, $query);
        # menampung hasil dari result
        $data = [];
        while ( $feedback = mysqli_fetch_assoc( $result ) ){
            // # convert ke MedicalRecord class
            // $data[] = MedicalRecord::withData( $feedback );
            $data[] = $feedback;
        }
        # konfert ke json jika di butuhkan
         if( $convert_To_Json ){
             return json_encode($data);
         }
         return $data;
    }  

    /**
     * Hasil dari view berupa array data/ json
     * 
     * Mengengmbalikan semua data yg ada di databe tanpa ada filter
     * 
     * @return array or json
     */
    public function resultAll(){
        $limit = $this->_limit; #limited views
        $sort = $this->_sort;
        $order = $this->_order;
        $start_data = ($this->_current_pos * $this->_limit) - $this->_limit;
        $sort_order = ', tanggal_lahir';

        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        $query = "SELECT * FROM data_rm ORDER BY $sort $order $sort_order LIMIT $start_data, $limit";
        $result = mysqli_query($link, $query);
        $data = [];
        while ( $feedback = mysqli_fetch_assoc( $result ) ){
            $data[] = $feedback;
        }
        return $data;
    }

    /** 
     * Hasil berupa array data/ json. 
     * Hasil dapat diatur menggunkan filter-filter
     * 
     * Dalam satu peoperty bisa ada beberapa Value
     * 
     * Request Property: filtersAdd() 
     * 
     * @param boolean $strict 
     * - true = menggunkan logica AND, semua paremeter harus terpenuhi
     * - false = menggunakan logica Or, salah parameter terpenuhi
     * @param boolean $convert_To_Json
     * - true = mengmbalilkan data berbentuk json
     * - false = mengembalikan data berbentuk array data
     * @return array or json
     */
    public function results($strict = false, $convert_To_Json = false){
       # koneksi data base
       $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
       # query data 
       $queryFilter = $this->filters($strict);
       if( $queryFilter == ''){
           # mencegah (query kosong) ketika filter tidak di setting
           return [];
       } else{
           $query = $this->query( $queryFilter );
       }
       # mengambil dari table
       $result = mysqli_query($link, $query);
       # menampung hasil dari result
       $data = [];
       while ( $feedback = mysqli_fetch_assoc( $result ) ){
           // # convert ke MedicalRecord class
           // $data[] = MedicalRecord::withData( $feedback );
           $data[] = $feedback;
       }
       # konfert ke json jika di butuhkan
        if( $convert_To_Json ){
            return json_encode($data);
        }
        return $data;
    }

    /**
     * Shorthand untuk mengambil data di data base data_rm
     * @param string $statement String query setelah where condition
     * @param array  $params Parameter dalam array untuk mengisi bind pada query
     * @return array Array assosiatif hasil pencarian
     */
    public function where(string $statement, array $params){
        $db = new MyPDO();
        $db->query("SELECT * FROM `data_rm` WHERE $statement");
        foreach( $params as $param_key => $param_val){
            $db->bind($param_key, $param_val);
        }
        return $db->resultset();
    }
}
