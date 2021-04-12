<?php namespace Model\QuestionAnswer;

use PHPUnit\TextUI\XmlConfiguration\Constant;
use System\Database\MyModel;
use System\Database\MyPDO;

class asks extends MyModel
{
  // property
  private $_options = ["imperssion" => [":", ""], "operator"   => "="];
  const SORTORDER_ASC = 0;
  const SORTORDER_DESC = 1;

  // filtter

  public function fillterPerentOnly()
  {
    return $this;
  }

  public function fillterPerentID($val)
  {
    $this->_FILTERS[] = [
      'id'      => 1,
      'param'   => 'perent_id',
      'value'   => $val,
      'option'  => $this->_options,
      'type'    => \PDO::PARAM_STR
    ];

    return $this;
  }

  // sort order and result setting

  public function sortByNewst(int $Sort_Order = self::SORTORDER_ASC){
    if ($Sort_Order == 0) {
      $this->_SORT_ORDER = "`id` ASC";
    } else {
      $this->_SORT_ORDER = "`id` DESC";
    }
    return $this;
  }

  /**
   * @param MyPDO $PDO DataBase class Dependency Injection
   */
  public function __construct(MyPDO $PDO = null)
  {
    $this->_TABELS[]  = 'public_quest';
    $this->PDO = $PDO ?? MyPDO::getInstance();
  }
}
