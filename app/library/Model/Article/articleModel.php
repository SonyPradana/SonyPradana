<?php namespace Model\Article;

use System\Database\MyModel;
use System\Database\MyPDO;

class articleModel extends MyModel
{
  private $_options = array(
    "imperssion" => [":", ""],
    "operator"   => "="
  );

  public function filterURLID(string $val)
  {
    $this->_FILTERS[] = [
      'id'      => 1,
      'param'   => 'slug',
      'value'   => $val,
      'option'  => $this->_options,
      'type'    => \PDO::PARAM_STR
    ];

    return $this;
  }

  public function filterStatus(string $val)
  {
    $this->_FILTERS[] = [
      'id'      => 2,
      'param'   => 'status',
      'value'   => $val,
      'option'  => $this->_options,
      'type'    => \PDO::PARAM_STR
    ];

    return $this;
  }

  public function selectColomn(array $val)
  {
    $this->_COLUMNS = $val;
  }

  public function __construct(MyPDO $PDO = null)
  {
    $this->_TABELS[]  = 'articles';
    $this->PDO = $PDO ?? MyPDO::getInstance();
    $this->_SORT_ORDER = "`create_time` DESC";
  }

  public function rowCount(): int
  {
    $this->PDO->query($this->query());

    foreach ($this->mergeFilters() as $filters) {
      foreach ($filters['filters'] as $key => $val) {
        if (isset( $val['value']) && $val['value'] != '') {
          $type = $val['type'] ?? null;
          $this->PDO->bind(":" . $key, $val['value'], $type);
        }
      }
    }
    return $this->PDO->rowCount();
  }
}
