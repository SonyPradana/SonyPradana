<?php
/** 
 * class untuk memnaggil conection database,
 * 
 * untuk mepermudah proses penggatina alamat data base
 * */ 
class DbConfig{
    private $_conn;
    public function __construct($database_name = "simpusle_simpus_lerep"){              
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, $database_name);
        $this->_conn = $conn;        
    }
    public function StartConnection(){
        return $this->_conn;
    }    
}

