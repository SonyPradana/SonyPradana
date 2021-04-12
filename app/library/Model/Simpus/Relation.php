<?php

namespace Model\Simpus;

use System\Database\MyPDO;

class Relation
{
    public static function creat(string $id_hash, int $time_stamp, MyPDO $PDO = null)
    {
        $PDO = $PDO ?? MyPDO::getInstance();
        $PDO->query(
            "INSERT INTO
                table_relation(`id_hash`, `time_stamp`)
            VALUES
                (:id_hash, :time_stamp)
        ");
        $PDO->bind(':id_hash', $id_hash, \PDO::PARAM_STR);
        $PDO->bind(':time_stamp', $time_stamp, \PDO::PARAM_INT);
        // menyimpan ke data base
        $PDO->execute();
        if ($PDO->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public static function where(string $param, $val, MyPDO $PDO = null): array
    {
        $PDO = $PDO ?? MyPDO::getInstance();
        $PDO->query(
            "SELECT
                *
            FROM
                table_relation
            WHERE
                $param = :val"
            );
        $PDO->bind(":val", $val);

        return  $PDO->resultset();
    }
}
