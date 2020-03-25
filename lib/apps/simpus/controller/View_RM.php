<?php
/**
 * class ini befungsi untuk mengambil data-data rm dari data base
 * dengan parameter tertentu, seperti nama, tanggal lahir alamat, dan nama kepala keluarga
 * (mengumpulkan data berdasarkan filter yg ada)
 * 
 * 
 * @author sonypradana@gmail.com
 */
class View_RM{
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

    #konfigurasi    
    private $_startFrom = 0;
    private $_endTo = 10;
    private $_limit = 10;
    private $_sort = 'id';

    #property
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
    private $_sortSupport = ['id', 'nomor_rm', 'nama', 'tanggal_lahir', 'alamat', 'nomor_rw', 'nama_kk', 'nomor_rm_kk'];
    public function sortUsing($val){
        $val = strtolower($val);
        $this->_sort = in_array($val, $this->_sortSupport) ? $val : 'id';
    }

    public function __construct(){
        
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
        $q[] = $this->cekStringExis( 'nomor_rm', $this->_filter_nomor_rm);
        $q[] = $this->cekStringExis( 'nama', $this->_filter_nama);
        $q[] = $this->cekStringExis( 'tgl', $this->_filter_tanggal_lahir);
        $q[] = $this->cekStringExis( 'alamat', $this->_filter_alamat);
        $q[] = $this->cekStringExis( 'nomor_rt', $this->_filter_rt, false);
        $q[] = $this->cekStringExis( 'nomor_rw', $this->_filter_rw, false);
        $q[] = $this->cekStringExis( 'nama_kk', $this->_filter_nama_kk);
        $q[] = $this->cekStringExis( 'nomor_rm_kk', $this->_filter_noRm_kk, false);            
       
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
                $query .= ($res) ?  $res . ' AND ': '' ;
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
    private function cekStringExis($parameter, $obj, $use_persent = true){
        if( isset( $obj ) AND $obj != ''){
            if( $use_persent ){
                return "($parameter LIKE '%$obj%')";
            } else {
                return "($parameter LIKE '$obj')";
            }
        }
        return '';
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
        $conn = new DbConfig();
        $link = $conn->StartConnection();
        # query data 
        $queryFilter = $this->filter($strict);
        if( $queryFilter == ''){
            # mencegah (query kosong) ketika filter tidak di setting
            // $query = "SELECT * FROM data_rm ORDER BY id ASC";
            return [];
        } else{
            $query = "SELECT * FROM data_rm WHERE " . $queryFilter . " ORDER BY ". $this->_sort ." ASC LIMIT " . $this->_limit;
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

    public function resultAll(){
        $limit = $this->_limit; #limited views
        $sort = $this->_sort;

        $conn = new DbConfig();
        $link = $conn->StartConnection();
        $query = "SELECT * FROM data_rm ORDER BY $sort ASC LIMIT $limit";
        $result = mysqli_query($link, $query);
        $data = [];
        while ( $feedback = mysqli_fetch_assoc( $result ) ){
            $data[] = $feedback;
        }
        return $data;
    }

}
