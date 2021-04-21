<?php

namespace Model\CovidTracker;

use System\Database\MyCRUD;
use System\Database\MyPDO;
use System\Database\MyQuery;

class CovidTracker extends MyCRUD
{
  // getter
  public function getID()
  {
    return $this->ID['id'] ?? null;
  }

  // getter
	public function date()
	{
		return $this->COLUMNS['date'];
	}

	public function location()
	{
		return $this->COLUMNS['location'];
	}

	public function suspek()
	{
		return $this->COLUMNS['suspek'];
	}

	public function suspek_discharded()
	{
		return $this->COLUMNS['suspek_discharded'];
	}

	public function suspek_meninggal()
	{
		return $this->COLUMNS['suspek_meninggal'];
	}

	public function konfirmasi_symptomatik()
	{
		return $this->COLUMNS['konfirmasi_symptomatik'];
	}

	public function konfirmasi_asymptomatik()
	{
		return $this->COLUMNS['konfirmasi_asymptomatik'];
	}

	public function konfirmasi_sembuh()
	{
		return $this->COLUMNS['konfirmasi_sembuh'];
	}

	public function konfirmasi_meninggal()
	{
		return $this->COLUMNS['konfirmasi_meninggal'];
	}


  // setter
  public function setID(int $val)
  {
    $this->ID = array (
      'id' => $val
    );
    return $this;
  }

  // setter
	public function setDate(int $val)
	{
		$this->COLUMNS['date'] = $val;
		return $this;
	}
	public function setLocation(int $val)
	{
		$this->COLUMNS['location'] = $val;
		return $this;
	}
	public function setSuspek(int $val)
	{
		$this->COLUMNS['suspek'] = $val;
		return $this;
	}
	public function setSuspek_discharded(int $val)
	{
		$this->COLUMNS['suspek_discharded'] = $val;
		return $this;
	}
	public function setSuspek_meninggal(int $val)
	{
		$this->COLUMNS['suspek_meninggal'] = $val;
		return $this;
	}
	public function setKonfirmasi_symptomatik(int $val)
	{
		$this->COLUMNS['konfirmasi_symptomatik'] = $val;
		return $this;
	}
	public function setKonfirmasi_asymptomatik(int $val)
	{
		$this->COLUMNS['konfirmasi_asymptomatik'] = $val;
		return $this;
	}
	public function setKonfirmasi_sembuh(int $val)
	{
		$this->COLUMNS['konfirmasi_sembuh'] = $val;
		return $this;
	}
	public function setKonfirmasi_meninggal(int $val)
	{
		$this->COLUMNS['konfirmasi_meninggal'] = $val;
		return $this;
	}

  public function __construct()
  {
    $this->PDO = MyPDO::getInstance();
    $this->TABLE_NAME = 'covid_tracker';
    $this->COLUMNS = array(
      'date' => null,
			'location' => null,
			'suspek' => null,
			'suspek_discharded' => null,
			'suspek_meninggal' => null,
			'konfirmasi_symptomatik' => null,
			'konfirmasi_asymptomatik' => null,
			'konfirmasi_sembuh' => null,
			'konfirmasi_meninggal' => null,
    );
  }

  public static function getLastIndex()
  {
    return MyQuery::conn('covid_tracker')
      ->select(['date'])
      ->order('date', MyQuery::ORDER_DESC)
      ->single()['date'] ?? time();
  }

}
