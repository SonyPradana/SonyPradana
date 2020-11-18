<?php

namespace Model\Simpus;

use System\Database\MyPDO;

class Relation
{
    /** @var MyPDO Instant PDO */
    private $PDO;
    private $ID_HASH;
    private $TIME_STAMP;

    public function __construct($id_hash, $time_stamp)
    {
        $this->PDO = new MyPDO();
        $this->ID_HASH = $id_hash;
        $this->TIME_STAMP = $time_stamp;
    }

    public function creat()
    {
        $this->PDO->query("INSERT INTO 
                        table_relation (
                            `id_hash`, `time_stamp`
                        ) 
                    VALUES (
                            :id_hash, :time_stamp
                        )
                    ");
        $this->PDO->bind(':id_hash', $this->ID_HASH);
        $this->PDO->bind(':time_stamp', $this->TIME_STAMP);
        // menyimpan ke data base
        $this->PDO->execute();
        if( $this->PDO->rowCount() > 0){
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
