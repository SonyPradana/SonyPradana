<?php

namespace Simpus\Simpus;

use Simpus\Database\MyPDO;

class Relation{
    private $ID_HASH;
    private $TIME_STAMP;

    public function __construct($id_hash, $time_stamp){
        $this->ID_HASH = $id_hash;
        $this->TIME_STAMP = $time_stamp;
    }

    public function creat(){
        $db = new MyPDO();
        $db->query("INSERT INTO 
                        table_relation (
                            `id_hash`, `time_stamp`
                        ) 
                    VALUES (
                            :id_hash, :time_stamp
                        )
                    ");
        $db->bind(':id_hash', $this->ID_HASH);
        $db->bind(':time_stamp', $this->TIME_STAMP);
        // menyimpan ke data base
        $db->execute();
        if( $db->rowCount() > 0){
            return true;
        }
        return false;
    }

    public static function where($param, $val, $pdo = null):array{
        $pdo = $pdo == null ? new MyPDO() : $pdo;
        $pdo->query("SELECT
                        *
                    FROM
                        table_relation
                    WHERE
                        $param = :val");
        $pdo->bind(":val", $val);
        
        return  $pdo->resultset();
    }
}
