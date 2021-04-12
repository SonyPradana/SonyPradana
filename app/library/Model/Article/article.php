<?php

namespace Model\Article;

use System\Database\MyCRUD;
use System\Database\MyPDO;

class article extends MyCRUD
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


  public function __construct()
  {
    $this->PDO = MyPDO::getInstance();
    $this->TABLE_NAME = 'articles';
    $this->COLUMNS = array(
      'id' => null,
			'slug' => null,
			'author' => null,
			'title' => null,
			'discription' => null,
			'keywords' => null,
			'create_time' => null,
			'update_time' => null,
			'image_url' => null,
			'image_alt' => null,
			'media_note' => null,
			'raw_content' => null,
			'css' => null,
			'js' => null,
			'status' => null
    );
  }

}
