<?php namespace System\Database;

use System\Database\CrudInterface;
use System\Database\MyPDO;

class MyCRUD implements CrudInterface
{
  /** @var MyPDO */
  protected $PDO;

  protected $TABLE_NAME;
  /** @var array */
  protected $COLUMNS = [];
  // TODO: merge ke FILTERS
  protected $ID;

  public function setter(string $key, $val)
  {
    $this->COLUMNS[$key] = $val;
    return $this;
  }
  public function getter($key)
  {
    return $this->COLUMNS[$key];
  }

  public function read(): bool
  {
    $get_colomn = $this->getColumn();
    $get_table  = $this->TABLE_NAME;
    $get_id_key = array_keys($this->ID)[0];
    $get_id_val = array_values($this->ID)[0];

    $this->PDO->query(
      "SELECT
        $get_colomn
      FROM
        $get_table
      WHERE
      `$get_id_key` = :$get_id_key"
    );

    $this->PDO->bind(':' . $get_id_key, $get_id_val);
    if( $this->PDO->single() ) {
      $this->COLUMNS = $this->PDO->single();
      return true;
    }
    return false;
  }

  public function cread(): bool
  {
    return false;
  }

  public function update(): bool
  {
    $get_table  = $this->TABLE_NAME;
    $get_id_key = array_keys($this->ID)[0];
    $get_id_val = array_values($this->ID)[0];
    $get_set = $this->queryFilters($this->COLUMNS);

    $this->PDO->query(
      "UPDATE
        $get_table
      SET
        $get_set
      WHERE
        `$get_id_key` = :$get_id_key"
    );

    // binding
    foreach( $this->COLUMNS as $key => $val) {
      if(isset($val) && $val !== '') {
        $this->PDO->bind(':' . $key, $val);
      }
    }
    $this->PDO->bind(':' . $get_id_key, $get_id_val);
    $this->PDO->execute();

    if( $this->PDO->rowCount() > 0){
        return true;
    }
    return false;
  }

  public function delete(): bool
  {
    return false;
  }

  public function isExist(): bool
  {
    return false;
  }
  public function convertFromArray(): bool
  {
    return false;
  }
  public function convertToArray(): array
  {
    return $this->COLUMNS;
  }

  // helper
  private function getColumn(): string
  {
    $get_column = array_keys($this->COLUMNS);
    $get_column = array_map(function($x){
      return '`' . $x . '`';
    }, $get_column);

    return implode(', ', $get_column);
  }

  protected function queryFilters(array $filters)
  {
    $query = [];
    foreach($filters as $key => $val) {
      if(isset($val) && $val !== '') {
        $query[] = $this->queryBuilder($key, $key, [
          'imperssion' => [':', ''],
          'operator' => '='
        ]);
      }
    }

    $arr_query = array_filter($query);
    return implode(', ', $arr_query);
  }

  protected function queryBuilder($key, $val, array $option = ["imperssion" => ["'%", "%'"], "operator" => "LIKE"])
  {
    $operator = $option["operator"];
    $sur = $option["imperssion"][0];
    $pre = $option["imperssion"][1];
    if( isset( $val ) && $val !== '') {
        return "$key $operator $sur$val$pre";
    }
    return "";
  }


}
