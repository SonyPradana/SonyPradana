<?php
/** 
 * class untuk memnaggil conection database,
 * 
 * untuk mepermudah proses penggatina alamat data base
 * */ 
class DbConfig{
    private $_conn;
    // 45.118.132.253
    // 203.114.74.128
    public function __construct($database_name = "simpusle_simpus_lerep"){              
        $conn = mysqli_connect("localhost", "root", "", $database_name);
        // = mysqli_connect("45.118.132.253", "simpusle_admin", "ulfamylove", $database_name);
        $this->_conn = $conn;        
    }
    public function StartConnection(){
        return $this->_conn;
    }    
}

