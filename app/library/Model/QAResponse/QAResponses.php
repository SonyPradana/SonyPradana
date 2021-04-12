<?php namespace Model\QAResponse;

use System\Database\MyModel;
use System\Database\MyPDO;

class QAResponses extends MyModel
{
  // property
  private $_options = ["imperssion" => [":", ""], "operator"   => "="];

  public function filterEventID(string $eventID)
  {
    $this->_FILTERS[] = [
      'id'      => 1,
      'param'   => 'event_id',
      'value'   => $eventID,
      'option'  => $this->_options,
      'type'    => \PDO::PARAM_STR
    ];

    return $this;
  }

  public function filterUserID(string $eventID)
  {
    $this->_FILTERS['user_id'] = [
      'value'     => $eventID,
      'option'    => $this->_options,
      'type'      => \PDO::PARAM_STR
    ];

    return $this;
  }

  /**
   * @param MyPDO $PDO DataBase class Dependency Injection
   */
  public function __construct(MyPDO $PDO = null)
  {
    $this->_TABELS[]  = 'response_log';
    $this->PDO = $PDO ?? MyPDO::getInstance();

    $this->_FILTERS['event_name'] = [
      'value'     => 'respone_qna',
      'option'    => $this->_options,
      'type'      => \PDO::PARAM_STR
    ];

  }
}
