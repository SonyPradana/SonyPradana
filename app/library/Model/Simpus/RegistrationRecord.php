<?php

namespace Model\Simpus;

use System\Database\MyCRUD;
use System\Database\MyPDO;

class RegistrationRecord extends MyCRUD
{
  // getter
  public function getID()
  {
    return $this->ID['id'] ?? null;
  }

  // getter
	public function id_hash()
	{
		return $this->COLUMNS['id_hash'];
	}

	public function tanggal_dibuat()
	{
		return $this->COLUMNS['tanggal_dibuat'];
	}

	public function last_update()
	{
		return $this->COLUMNS['last_update'];
	}

	public function nomor_rm()
	{
		return $this->COLUMNS['nomor_rm'];
	}

	public function poli()
	{
		return $this->COLUMNS['poli'];
	}

	public function status()
	{
		return $this->COLUMNS['status'];
	}

	public function jenis_peserta()
	{
		return $this->COLUMNS['jenis_peserta'];
	}

	public function poli_id()
	{
		return $this->COLUMNS['poli_id'];
	}


  // setter
  public function setID($val)
  {
    $this->ID = array (
      'id' => $val
    );
    return $this;
  }

  // setter
	public function setId_hash($val)
	{
		$this->COLUMNS['id_hash'] = $val;
		return $this;
	}

	public function setTanggal_dibuat(int $val)
	{
		$this->COLUMNS['tanggal_dibuat'] = $val;
		return $this;
	}

	public function setLast_update(int $val)
	{
		$this->COLUMNS['last_update'] = $val;
		return $this;
	}

	public function setNomor_rm($val)
	{
		$this->COLUMNS['nomor_rm'] = $val;
		return $this;
	}

	public function setPoli($val)
	{
    if ($val !== null) {
      $this->COLUMNS['poli'] = $val;
    }
		return $this;
	}

	public function setStatus($val)
	{
    if ($val !== null) {
      $this->COLUMNS['status'] = $val;
    }
		return $this;
	}

	public function setJenis_peserta($val)
	{
    if ($val !== null) {
      $this->COLUMNS['jenis_peserta'] = $val;
    }
		return $this;
	}

	public function setPoli_id($val)
	{
    if ($val !== null) {
      $this->COLUMNS['poli_id'] = $val;
    }
		return $this;
	}

  public function __construct()
  {
    $this->PDO = MyPDO::getInstance();
    $this->TABLE_NAME = 'data_kunjungan';
    $this->COLUMNS = array(
      'id' => '',
      'id_hash' => null,
			'tanggal_dibuat' => null,
			'last_update' => null,
			'nomor_rm' => null,
			'poli' => null,
			'status' => null,
			'jenis_peserta' => null,
			'poli_id' => null,
    );
  }
}
