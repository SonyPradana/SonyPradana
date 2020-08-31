<?php

namespace Simpus\Simpus;

class KIAAnakRecords{
    // identitas
    private $_filterbyNama;
    private $_filterbyTanggalLahir;
    private $_filterbyAlamat;
    private $_filterByRT;
    private $_filterByRW;
    private $_filterByNamaKK;
    // biodata
    private $_filterbyJenisKelamin;
    private $_filterbyBbl;
    private $_filterbyPbl;
    private $_filterbyKia;
    private $_filterbyImd;
    private $_filterbyAsiEks;
    private $_filterbyAlamatPosyandu;
    private $_filterbyNamaPosyandu;
    // setting
    private $_sort;
    private $_sort2;
    private $_order;
    private $_postion;
    private $_limit;

    // setter setting
    public function setCurrentPage(int $page_number){
        $page_number = $page_number < 0 ? 0 : $page_number;
        $this->_postion = $page_number;
    }

    // setter - identitas
    public function filterByNama(string $val){
        if( $val != null){
            $this->_filterbyNama = $val;
        }
        return $this;
    }
    public function filterByTanggalLahir(string $val){
        $this->_filterbyTanggalLahir = $val;
        return $this;
    }
    public function filterByAlamat(string $val){
        $this->_filterbyAlamat = $val;
        return $this;
    }
    public function filterByRT(int $val){
        $this->_filterByRT = $val;
        return $this;
    }
    public function filterByRW(int $val){
        $this->_filterByRW = $val;
        return $this;
    }
    public function filterByNamaKK(string $val){
        $this->_filterByNamaKK = $val;
        return $this;
    }
    // setter - biodata
    public function filterByJenisKelamin(bool $is_male){
        $this->_filterbyJenisKelamin = $is_male ? "1" : "0";
        return $this;
    }
    public function filterByBbl(int $val){
        $this->_filterbyBbl = $val;
        return $this;
    }
    public function filterByPbl(int $val){
        $this->_filterbyPbl = $val;
        return $this;
    }
    public function filterByKia(string $val){
        $this->_filterbyKia = $val;
        return $this;
    }
    public function filterByImd(int $val){
        $this->_filterbyKia = $val;
        return $this;
    }
    public function filterByAsiEks(bool $val){
        $this->_filterbyKia = $val ? 1 : 0;
        return $this;
    }
    // setter - posyandu
    public function filterByAlamatPosyandu(string $val){
        $this->_filterbyAlamatPosyandu = $val;
        return $this;
    }
    public function filterByNamaPosyandu(string $val){
        $this->_filterbyNamaPosyandu = $val;
        return $this;
    }
    public function filltersFromArray(array $arr_filter){
        // filter biodata
        $this->_filterbyNama            = $arr_filter['nama'] ?? '';
        $this->_filterbyTanggalLahir    = $arr_filter['tanggal_lahir'] ?? '';
        $this->_filterbyAlamat          = $arr_filter['alamat'] ?? '';
        $this->_filterByRT              = $arr_filter['nomor_rt'] ?? '';
        $this->_filterByRW              = $arr_filter['nomor_rw'] ?? '';
        // filter idetintas
        $this->_filterbyJenisKelamin    = $arr_filter['jenis_kelamin'] ?? '';
        $this->_filterbyBbl             = $arr_filter['bbl'] ?? '';
        $this->_filterbyPbl             = $arr_filter['pbl'] ?? '';
        $this->_filterbyKia             = $arr_filter['kia'] ?? '';
        $this->_filterbyImd             = $arr_filter['imd'] ?? '';
        $this->_filterbyAsiEks          = $arr_filter['asi_eks'] ?? '';
        $this->_filterbyAlamatPosyandu  = $arr_filter['desa'] ?? '';
        $this->_filterbyNamaPosyandu    = $arr_filter['tempat_pemeriksaan'] ?? '';
    }
    // getter
    public function getQuery(bool $strict = true):string{
        return $this->query( $this->queryfilters( $strict ) );
    }
    public function getMaxData(bool $strict = false){
        $query_filter = $this->queryfilters( $strict );
        $query_filter = $query_filter != "" ?  "WHERE " . $query_filter : "";

        $query   = "SELECT
                        (
                            SELECT
                                COUNT(data_kia_anak.id)
                            FROM
                                data_kia_anak
                            INNER JOIN table_relation ON table_relation.id_hash = data_kia_anak.id_hash
                            INNER JOIN data_rm ON data_rm.data_dibuat = table_relation.time_stamp
                            INNER JOIN groups_posyandu ON groups_posyandu.id = data_kia_anak.grups_posyandu
                                $query_filter
                        ) AS total_data_rm,
                        (
                            SELECT
                                COUNT(data_kia_anak.id)
                            FROM
                                data_kia_anak
                            INNER JOIN table_relation ON table_relation.id_hash = data_kia_anak.id_hash
                            INNER JOIN staging_rm ON staging_rm.data_dibuat = table_relation.time_stamp
                            INNER JOIN groups_posyandu ON groups_posyandu.id = data_kia_anak.grups_posyandu
                                $query_filter
                            ) AS total_staging_rm
                    FROM
                        dual
                    ";
        $link     = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        $result   = mysqli_query($link, $query);
        $feedback = mysqli_fetch_assoc( $result );
        $count    = $feedback['total_staging_rm'] + $feedback['total_data_rm'] ;
       
        return (int) $count;
    }
    public function getMaxPage(bool $strict = false){
        $count = (float) $this->getMaxData( $strict ) / $this->_limit ;
        return (int) ceil( $count );
    }

    // setting
    public function limitView(int $val){
        $this->_limit = $val < 1 ? 1 : $val;
        return $this;
    }    
    private $_sortSupport = ['posyandu', 'desa', 'nomor_rm', 'nama', 'tanggal_lahir', "alamat", 'nomor_rw', 'nama_kk', 'jenis_kelamin', 'tanggal_dibuat', 'bbl', 'pbl', 'kia', 'imd', 'asi_eks'];
    public function sortUsing($val){
        $val = strtolower( $val );
        $this->_sort = $val;
        return $this;
    }
    public function orderUsing(string $val = "ASC"){
        $val = strtoupper( $val );
        if( $val == 'ASC' ){
            $this->_order = 'ASC';
        }elseif( $val == 'DECS' ){
            $this->_order = "DESC";
        }
        return $this;
    }

    public function __construct(){
        $this->_sort    = "id";
        $this->_sort2   = 'tanggal_dibuat';
        $this->_order   = "ASC";
        $this->_postion = 1;
        $this->_limit   = 10;
    }

    private function query($query_filter){
        $sort  = $this->_sort;
        $order = $this->_order;
        $limit = $this->_limit;
        $start_data = ($this->_postion * $limit) - $limit;
        $start_data = $start_data < 0 ? 0 : $start_data;
        $second_sort = $this->_sort2;

        $query =   "SELECT 
                        *
                    FROM(
                        SELECT      
                            data_kia_anak.id_hash AS code_hash,
                            data_kia_anak.jenis_kelamin,
                            data_kia_anak.tanggal_dibuat,
                            data_kia_anak.bbl,
                            data_kia_anak.pbl,
                            data_kia_anak.kia,
                            data_kia_anak.imd,
                            data_kia_anak.asi_eks,

                            staging_rm.nomor_rm,
                            staging_rm.nama,
                            staging_rm.tanggal_lahir,
                            staging_rm.alamat,
                            staging_rm.nomor_rt,
                            staging_rm.nomor_rw,
                            staging_rm.nama_kk,
                            staging_rm.nomor_rm_kk,

                            groups_posyandu.id AS id_posyandu,
                            groups_posyandu.posyandu,
                            groups_posyandu.desa
                        FROM        data_kia_anak
                        INNER JOIN  table_relation 
                            ON      table_relation.id_hash = data_kia_anak.id_hash
                        INNER JOIN  staging_rm 
                            ON      staging_rm.data_dibuat = table_relation.time_stamp
                        INNER JOIN  groups_posyandu
                            ON      groups_posyandu.id = data_kia_anak.grups_posyandu

                        UNION ALL

                        SELECT      
                            data_kia_anak.id_hash AS code_hash,
                            data_kia_anak.jenis_kelamin,
                            data_kia_anak.tanggal_dibuat,
                            data_kia_anak.bbl,
                            data_kia_anak.pbl,
                            data_kia_anak.kia,
                            data_kia_anak.imd,
                            data_kia_anak.asi_eks,

                            data_rm.nomor_rm,
                            data_rm.nama,
                            data_rm.tanggal_lahir,
                            data_rm.alamat,
                            data_rm.nomor_rt,
                            data_rm.nomor_rw,
                            data_rm.nama_kk,
                            data_rm.nomor_rm_kk,

                            groups_posyandu.id AS id_posyandu,
                            groups_posyandu.posyandu,
                            groups_posyandu.desa
                        FROM        data_kia_anak
                        INNER JOIN  table_relation 
                            ON      table_relation.id_hash = data_kia_anak.id_hash
                        INNER JOIN  data_rm 
                            ON      data_rm.data_dibuat = table_relation.time_stamp
                        INNER JOIN  groups_posyandu
                            ON      groups_posyandu.id = data_kia_anak.grups_posyandu
                    ) AS costume_table
                    WHERE 
                        $query_filter 
                    ORDER BY 
                        $sort $order, $second_sort 
                    LIMIT
                        $start_data, $limit
            ";
            // echo $query;
        return $query;
    }

    private function queryfilters(bool $strict = true){
        $querys   = [];
        $option = ["imperssion" => ["", ""], "operator"   => "="];
        // identitas
        $querys[] = $this->queryBuilder('nama', $this->_filterbyNama);
        $querys[] = $this->queryBuilder('tanggal_lahir', $this->_filterbyTanggalLahir);
        $querys[] = $this->queryBuilder('alamat', $this->_filterbyAlamat);
        $querys[] = $this->queryBuilder('nomor_rt', $this->_filterByRT);
        $querys[] = $this->queryBuilder('nomor_rw', $this->_filterByRW);
        $querys[] = $this->queryBuilder('nama_kk', $this->_filterByNamaKK);
        // biodata
        $querys[] = $this->queryBuilder('data_kia_anak.jenis_kelamin', $this->_filterbyJenisKelamin, $option);
        $querys[] = $this->queryBuilder('data_kia_anak.bbl', $this->_filterbyBbl, $option);
        $querys[] = $this->queryBuilder('data_kia_anak.pbl', $this->_filterbyPbl, $option);
        $querys[] = $this->queryBuilder('data_kia_anak.imd', $this->_filterbyImd, $option);
        $querys[] = $this->queryBuilder('data_kia_anak.kia', $this->_filterbyKia, $option);
        $querys[] = $this->queryBuilder('data_kia_anak.asi_eks', $this->_filterbyAsiEks, $option);
        // posyandu
        $option = ["imperssion" => ["'", "'"], "operator"   => "="];
        $querys[] = $this->queryBuilder('desa', $this->_filterbyAlamatPosyandu, $option);
        $querys[] = $this->queryBuilder('posyandu', $this->_filterbyNamaPosyandu, $option);

        $arr_query = array_filter($querys);
        return $strict ? implode(' AND ', $arr_query) : implode(' OR ', $arr_query);
    }

    private function queryBuilder($key, $val, array $option = ["imperssion" => ["'%", "%'"], "operator" => "LIKE"]){
        $operator = $option["operator"];
        $sur = $option["imperssion"][0];
        $pre = $option["imperssion"][1];
        if( isset( $val ) && $val != ''){
            return "($key $operator $sur$val$pre)";
        }
        return "";
    }

    public function resultAll(){        
        $sort  = $this->_sort;
        $order = $this->_order;
        $limit = $this->_limit;
        $start_data = ($this->_postion * $limit) - $limit;
        $second_sort = $this->_sort2;

        $res     = [];
        $biodata = ['data_rm', 'staging_rm'];

        foreach( $biodata as $table){
            $res[]   = "SELECT
                            *
                        FROM
                            data_kia_anak
                        INNER JOIN
                            table_relation 
                            ON 
                            table_relation.id_hash = data_kia_anak.id_hash
                        INNER JOIN
                            $table
                            ON
                            $table.data_dibuat = table_relation.time_stamp
                        ORDER BY 
                            data_kia_anak.$sort $order, data_kia_anak.$second_sort 
                        LIMIT
                            $start_data, $limit
                        ";
        }
        
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        $querys = implode(';', $res);
        $data   = [];
        if( mysqli_multi_query($link, $querys) ){
            do{
                if( $res = mysqli_store_result( $link )){
                    while($feedback = mysqli_fetch_assoc( $res )){
                        $data[] = $feedback;
                    }
                    mysqli_free_result( $res );
                }
                if( mysqli_more_results(( $link ))){
                    /* print divider */
                }
            } while( mysqli_next_result($link));
        }
        mysqli_close($link);

        return $data;
    }

    public function result(bool $strict = true){        
      # koneksi data base
      $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
      # query data 
      $queryFilter = $this->queryfilters($strict);
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
       return $data;
    }
}
