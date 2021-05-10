<?php

namespace Model\Simpus;

use System\Database\MyPDO;
use System\Database\MyQuery;

class Relation
{
  public static function creat(string $id_hash, int $time_stamp, MyPDO $PDO = null): bool
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
    return MyQuery::conn('table_relation', $PDO)
      ->select()
      ->equal($param, $val)
      ->all();
  }

  public static function has_hashId($hash_id)
  {
    return MyQuery::conn('table_relation')
      ->select(['id_hash'])
      ->equal('id_hash', $hash_id)
      ->single();
  }

  public static function has_timestamp($time_stamp)
  {
    return MyQuery::conn('table_relation')
      ->select(['time_stamp'])
      ->equal('time_stamp', $time_stamp)
      ->single();
  }
}
