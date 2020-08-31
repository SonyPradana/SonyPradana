<?php

namespace Simpus\Database;

class QueryBuilder{
    /** kumpulan array filter */
    protected $_GROUP_FILTER = [];
    /** primery filter */
    protected $_FILTERS = [];
    /** table yang dugunakan */
    protected $_TABELS  = [];
    /** column yang dugunaka */
    protected $_COLUMNS = [];
    /** logica where stactment [AND / OR] */
    protected $_STRICT_SEARCH = true;

    /** @var MyPDO */
    protected $PDO;

    // setter
    public function setStrictSearch(bool $strict_mode){
        $this->_STRICT_SEARCH = $strict_mode;
        return $this;
    }
    // getter
    public function getQuery():string{
        return $this->query();
    }
    public function getWhere():string{
        return $this->grupQueryFilters( $this->mergeFilters() );
    }
     public function getMegerFilters(){
         return $this->mergeFilters();
     }

    /**
     * HELPER
     * 
     * menggabungkan primery filter array dengan groups filter, tanpa merubah isi groups class. 
     * karean query di-runing dalam bentuk group filter
     * @return array New Groups array
     */
    protected function mergeFilters():array{
        $new_grups_filters      = $this->_GROUP_FILTER;
        // menambahkan filter group yg sudah ada, dengan filter
        if( empty($this->_FILTERS) == false){
            $new_grups_filters[]    = [
                "filters" => $this->_FILTERS,
                "strict"  => $this->_STRICT_SEARCH
            ];
        }
        // membuat group filter baru tanpa merubah grups filter dr classs
        return $new_grups_filters;
    }
    
    /**
     * mengenered grups filter ke-dalam format sql query (preapre statment)
     * @return string query yg siap di esekusi
     */
    protected function query():string{
        $table = $this->_TABELS[0];        
        $where_statment = $this->getWhere();

        $my_query = "SELECT * FROM `$table` WHERE  $where_statment";

        return $my_query;
    }

    /**
     * sql query tanpa menggunkan where statment
     */
    protected function originQuery():string{
        $table = $this->_TABELS[0];
        
        $my_query = "SELECT * FROM `$table`";

        return $my_query;
        
    }

    // main function 
    protected function grupQueryFilters(array $grup_fillters){
        $where_statment = [];
        foreach( $grup_fillters as $filter ){
            $query = $this->queryfilters($filter['filters'], $filter['strict']);
            $where_statment[] = '(' . $query . ')';
        }
        return implode(" AND ", $where_statment);
    }
    protected function queryfilters(array $filters, bool $strict = true){
        $querys   = [];
        // identitas
        foreach( $filters as $key => $val){
            if( isset( $val['value']) && $val['value'] != ''){
                $option   = $val['option'] ?? ["imperssion" => [":", ""], "operator"   => "="];
                $querys[] = $this->queryBuilder($key, $key, $option);
            }
        }

        $arr_query = array_filter($querys);
        return $strict ? implode(' AND ', $arr_query) : implode(' OR ', $arr_query);
    }
    protected function queryBuilder($key, $val, array $option = ["imperssion" => ["'%", "%'"], "operator" => "LIKE"]){
        $operator = $option["operator"];
        $sur = $option["imperssion"][0];
        $pre = $option["imperssion"][1];
        if( isset( $val ) && $val != ''){
            return "($key $operator $sur$val$pre)";
        }
        return "";
    }
    
    /** 
     * menampilkan data dari hasil query yang ditentukan sebelumnya
     */
    public function result():array{
        if( $this->PDO == null) return [];                              // return null jika db belum siap
        $this->PDO->query( $this->query() );
        
        foreach( $this->mergeFilters() as $filters){
            foreach( $filters['filters'] as $key => $val){
                if( isset( $val['value']) && $val['value'] != ''){
                    $type = $val['type'] ?? null;
                    $this->PDO->bind(":" . $key, $val['value'], $type);
                }
            }
        }
        return $this->PDO->resultset();
    }

    /** 
     * menmpilkan semua data yang tersedia
     */
    public function resultAll():array{
        if( $this->PDO == null) return [];                          // return null jika db belum siap
        $this->PDO->query( $this->originQuery() );
        
        foreach( $this->mergeFilters() as $filters){
            foreach( $filters['filters'] as $key => $val){
                if( isset( $val['value']) && $val['value'] != ''){
                    $type = $val['type'] ?? null;
                    $this->PDO->bind(":" . $key, $val['value'], $type);
                }
            }
        }
        return $this->PDO->resultset();
    }

}
