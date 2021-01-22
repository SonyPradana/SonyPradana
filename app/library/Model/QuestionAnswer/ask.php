<?php

namespace Model\QuestionAnswer;

use Dotenv\Parser\Value;
use System\Database\MyCRUD;
use System\Database\MyPDO;

class ask extends MyCRUD
{
  // getter
  public function getID()
  {
    return $this->ID['id'] ?? null;
  }

  public function getAll()
  {
    return $this->convertToArray();
  }

  public function getLike()
  {
    return $this->COLUMNS['like_post'];
  }

  public function getDislike()
  {
    return $this->COLUMNS['dislike_post'];
  }

  public function getAuthor()
  {
    return $this->COLUMNS['author'];
  }

  public function getTitle()
  {
    return $this->COLUMNS['title'];
  }

  public function getContent()
  {
    return $this->COLUMNS['content'];
  }

  // setter
  public function setID(int $val)
  {
    $this->ID = array(
      'id' => $val
    );
    return $this;
  }

  public function setPerentID(int $val)
  {
    return $this;
  }

  public function setLike(int $val)
  {
    $this->COLUMNS['like_post'] = $val;
    return $this;
  }

  public function setDislike(int $val)
  {
    $this->COLUMNS['dislike_post'] = $val;
    return $this;
  }




  public function __construct()
  {
    $this->PDO = new MyPDO();
    $this->TABLE_NAME = 'public_quest';
    $this->COLUMNS = array(
      'id' => null,
			'perent_id' => null,
			'date_creat' => null,
			'date_update' => null,
			'author' => null,
			'like_post' => null,
			'dislike_post' => null,
			'title' => null,
			'content' => null,
			'tag' => null,
			'image' => null,

    );
  }

}
