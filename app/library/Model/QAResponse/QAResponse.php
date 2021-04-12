<?php

namespace Model\QAResponse;

use System\Database\MyCRUD;
use System\Database\MyPDO;
use System\Database\MyQuery;

class QAResponse extends MyCRUD
{
  // getter
  public function getID()
  {
    return $this->ID['id'] ?? null;
  }

  // setter
  public function setID(int $val)
  {
    $this->ID = array (
      'id' => $val
    );
    return $this;
  }

  public function setEventID(string $val)
  {
    $this->COLUMNS['event_id'] = $val;
    return $this;
  }

  public function setUserID(string $val)
  {
    $this->COLUMNS['user_id'] = $val;
    return $this;
  }

  public function unVote()
  {
    $this->COLUMNS['respone'] = 'unvote';
    return $this;
  }

  public function addLike()
  {
    $this->COLUMNS['respone'] = 'like:1';
    return $this;
  }

  public function addDislike()
  {
    $this->COLUMNS['respone'] = 'dislike:1';
    return $this;
  }

  public function __construct()
  {
    $this->PDO = MyPDO::getInstance();
    $this->TABLE_NAME = 'response_log';
    $this->COLUMNS = array(
      'id' => null,
			'date_create' => time(),
			'event_name' => 'respone_qna',
			'event_id' => null,
			'user_id' => null,
			'respone' => null,
    );
  }

  // function
  public function findEnventID(string $event_id)
  {
    $db = new MyQuery($this->PDO);
    return $db($this->TABLE_NAME)
      ->select()
      ->equal('event_name', 'respone_qna')
      ->equal('event_id', $event_id)
      ->single();
  }
}
