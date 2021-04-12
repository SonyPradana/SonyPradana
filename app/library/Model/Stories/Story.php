<?php

namespace Model\Stories;

use System\Database\MyCRUD;
use System\Database\MyPDO;

class Story extends MyCRUD
{
  // getter
  public function getID()
  {
    return $this->ID['id'];
  }

  public function getViewer()
  {
    return $this->getter('viewer') ?? 0;
  }

  public function getImageID(): string
  {
    return $this->COLUMNS['image_id'];
  }

  public function getCaption(): string
  {
    return $this->COLUMNS['caption'];
  }

  public function getUploader(): string
  {
    return $this->COLUMNS['uploader'] ?? 'default';
  }

  // setter
  public function setID(string $val)
  {
    $this->ID['id'] = $val;
    return $this;
  }

  public function setDateTaken(int $val)
  {
    $this->setter('date_taken', $val);
    return $this;
  }

  public function setDateEnd(int $val)
  {
    $this->setter('date_end', $val);
    return $this;
  }

  public function setImageID(string $val)
  {
    $this->setter('image_id', $val);
    return $this;
  }

  public function setCaption(string $val)
  {
    $this->setter('caption', $val);
    return $this;
  }

  public function setUploader(string $val)
  {
    $this->setter('uploader', $val);
    return $this;
  }

  public function setViewer(int $val)
  {
    $this->setter('viewer', $val);
    return $this;
  }

  public function setGroup(string $val)
  {
    $this->setter('uploader', $val);
    return $this;
  }

  public function __construct(MyPDO $PDO = null)
  {
    $this->PDO = $PDO ?? MyPDO::getInstance();
    $this->TABLE_NAME = 'stories';
    $this->COLUMNS = array(
      'id' => '',
			'date_taken' => null,
			'date_end' => null,
			'image_id' => null,
			'caption' => null,
			'uploader' => null,
      'viewer' => null

    );
  }

}
