<?php

namespace Model\Simpus;

use Helper\String\Str;
use System\Database\MyPDO;
use System\Database\MyQuery;

/**
 * Class ini mengambil data rekam medis berupa data pribadi dari data base
 *
 * @author sonypradana@gmail.com
 */
class PersonalRecord
{
  // table info
  private $_tanggal_dibuat = '';
  private $_last_update = '';
  // primery
  private $_hash_id = ''; // uniq id untuk mengakses data
  private $_NIK = '';
  private $_Nomor_KK = '';
  private $_Nomor_jaminan = '';
  private $_Nomor_Telpon = '';
  // biodata
  private $_jenis_kelamin = '';
  private $_agama = '';
  private $_pendidikan = '';
  private $_pekerjaan = '';
  private $_golongan_darah = '';
  private $_kategory; // kia-anak, kia-hamil, dan lain-lain
  // data kia - anak & ibu
  private $_list_anak; // dalam array (id)
  private $_list_hamil; // dalam array (id)

  // getter
	public function hash_id()
	{
		return $this->_hash_id;
	}

	public function tanggal_dibuat()
	{
		return $this->_tanggal_dibuat;
	}

	public function last_update()
	{
		return $this->_last_update;
	}

	public function nik()
	{
		return $this->_NIK;
	}

	public function nomor_kk()
	{
		return $this->_Nomor_KK;
	}

	public function nomor_jaminan()
	{
		return $this->_Nomor_jaminan;
	}

	public function nomor_telpon()
	{
		return $this->_Nomor_Telpon;
	}

	public function jenis_kelamin()
	{
		return $this->_Nomor_jaminan;
	}

	public function agama()
	{
    switch ($this->_agama) {
      case 0:
        return 'islam';
      case 1:
        return 'protestan';
      case 2:
        return 'katolik';
      case 3:
        return 'hindu';
      case 4:
        return 'budha';
      case 5:
        return 'konghucu';
      default:
        return '';
    }
	}

	public function pendidikan()
	{
		return $this->_pendidikan;
	}

	public function pekerjaan()
	{
		return $this->_pekerjaan;
	}

	public function golongan_darah()
	{
    switch ($this->_golongan_darah) {
      case 0:
        return 'O';
      case 1:
        return 'A';
      case 2:
        return 'B';
      case 3:
        return 'AB';
      default:
        return '';
    }
	}

  public function isValid()
  {
    $valid = false;

    if ($this->_NIK != '') {
      $valid = true;
    }
    if ($this->_Nomor_jaminan != '') {
      $valid = true;
    }

    return $valid;
  }

  // setter
  public function setHashId(string $hash_id)
  {
    $this->_hash_id = $hash_id;
    return $this;
  }

  public function setDataDibuat(int $data_dibuat)
  {
    $this->_tanggal_dibuat = $data_dibuat;
    return $this;
  }

  public function setDataDiupdate(int $data_diupdate)
  {
    $this->_last_update = $data_diupdate;
    return $this;
  }

  public function convertFromArray(array $data)
  {
    $this->_id              = $data['id'] ?? '';
    $this->_hash_id         = $data['hash_id'] ?? $this->_hash_id;
    $this->_tanggal_dibuat  = $data['tanggal_dibuat'] ?? $this->_tanggal_dibuat;
    $this->_last_update     = $data['last_update'] ?? $this->_last_update;
    $this->_NIK             = $data['nik'] ?? $this->_NIK;
    $this->_Nomor_KK        = $data['nomor_kk'] ?? $this->_Nomor_KK;
    $this->_Nomor_jaminan   = $data['nomor_jaminan'] ?? $this->_Nomor_jaminan;
    $this->_Nomor_Telpon    = $data['nomor_telpon'] ?? $this->_Nomor_Telpon;
    $this->_jenis_kelamin   = $data['jenis_kelamin'] ?? $this->_jenis_kelamin;
    $this->_agama           = $data['agama'] ?? $this->_agama;
    $this->_pendidikan      = $data['pendidikan'] ?? $this->_pendidikan;
    $this->_pekerjaan       = $data['pekerjaan'] ?? $this->_pekerjaan;
    $this->_golongan_darah  = $data['golongan_darah'] ?? $this->_golongan_darah;

    return $this;
  }

  public function convertToArray(): array
  {
    return [
      'hash_id'         => $this->_hash_id,
      'tanggal_dibuat'  => $this->_tanggal_dibuat,
      'last_update'     => $this->_last_update,
      'nik'             => $this->_NIK,
      'nomor_kk'        => $this->_Nomor_KK,
      'nomor_jaminan'   => $this->_Nomor_jaminan,
      'nomor_telpon'    => $this->_Nomor_Telpon,
      'jenis_kelamin'   => $this->_jenis_kelamin,
      'agama'           => $this->_agama,
      'pendidikan'      => $this->_pendidikan,
      'pekerjaan'       => $this->_pekerjaan,
      'golongan_darah'  => $this->_golongan_darah,
    ];
  }

  // function

  public function filter()
  {
    $this->_Nomor_jaminan = Str::fillText($this->_Nomor_jaminan, 13, 0);
    return $this;
  }

  public function create(): bool
  {
    // create data_personal
    return MyQuery::conn('data_personal')
      ->insert()
      ->values([
        'id'              => '',
        'hash_id'         => $this->_hash_id,
        'tanggal_dibuat'  => $this->_tanggal_dibuat,
        'last_update'     => $this->_last_update,
        'nik'             => $this->_NIK,
        'nomor_kk'        => $this->_Nomor_KK,
        'nomor_jaminan'   => $this->_Nomor_jaminan,
        'nomor_telpon'    => $this->_Nomor_Telpon,
        'jenis_kelamin'   => $this->_jenis_kelamin,
        'agama'           => $this->_agama,
        'pendidikan'      => $this->_pendidikan,
        'pekerjaan'       => $this->_pekerjaan,
        'golongan_darah'  => $this->_golongan_darah,
      ])
      ->execute();
  }

  public function update(): bool
  {
    return MyQuery::conn('data_personal')
      ->update()
      ->values([
        'last_update'     => time(),
        'nik'             => $this->_NIK,
        'nomor_kk'        => $this->_Nomor_KK,
        'nomor_jaminan'   => $this->_Nomor_jaminan,
        'nomor_telpon'    => $this->_Nomor_Telpon,
        'jenis_kelamin'   => $this->_jenis_kelamin,
        'agama'           => $this->_agama,
        'pendidikan'      => $this->_pendidikan,
        'pekerjaan'       => $this->_pekerjaan,
        'golongan_darah'  => $this->_golongan_darah,
      ])
      ->equal('hash_id', $this->_hash_id)
      ->execute();
  }

  public static function whereHashId(string $hash_id, MyPDO $PDO = null)
  {
    $instant = new static();
    $instant->convertFromArray(
      MyQuery::conn('data_personal', $PDO)
        ->select()
        ->equal('hash_id', $hash_id)
        ->single()
    );
    return $instant;
  }

  public static function whereNik(int $nik)
  {
    $instant = new static();
    $instant->convertFromArray(
      MyQuery::conn('data_personal')
        ->select()
        ->equal('nik', $nik)
        ->single()
    );
    return $instant;
  }

  public static function whereNoJaminan(int $nomor_jaminan)
  {
    $instant = new static();
    $instant->convertFromArray(
      MyQuery::conn('data_personal')
        ->select()
        ->equal('nomor_jaminan', $nomor_jaminan)
        ->single()
    );
    return $instant;
  }
}
