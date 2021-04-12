<?php

namespace Model\Stories;

use System\Database\MyModel;
use System\Database\MyPDO;

class Stories extends MyModel
{
  private $_options = ["imperssion" => [":", ""], "operator"   => "="];

  public function filterByUploader(string $val)
  {
    $this->_FILTERS["uploader"] = [
        'value'     => $val,
        'option'    => $this->_options,
        'type'      => \PDO::PARAM_STR
    ];

    return $this;
  }

  public function __construct(MyPDO $PDO = null)
  {
    $this->_TABELS[] = 'stories';
    $this->PDO = $PDO ?? MyPDO::getInstance();
    $this->_SORT_ORDER = " `date_taken` DESC";
  }

  public function selector(array $select_column)
  {
    $this->_COLUMNS = $select_column;
  }

  public function getGroub()
  {
    $this->PDO->query(
      "SELECT `uploader` FROM stories GROUP BY `uploader`"
    );
    return $this->PDO->resultset();
  }

}
