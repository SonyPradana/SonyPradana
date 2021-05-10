<?php

namespace Model\Simpus;

use System\Database\MyModel;
use System\Database\MyPDO;

class RegistrationRecords extends MyModel
{
  /**
   * filter between the date
   * @param int $tanggal Tanggal yg akan difilter (format timestamp)
   */
  public function fillterTanggal(int $tanggal)
  {
    $start = $tanggal;
    $end = $tanggal + 86400;

    $this->costumeWhere(
      "tanggal_dibuat BETWEEN :startDate AND :endDate",
      array(
        [':startDate', $start],
        [':endDate', $end]
      )
    );

    return $this;
  }
  /**
   * @param MyPDO $PDO DataBase class Dependency Injection
   */
  public function __construct(MyPDO $PDO = null)
  {
    $this->_TABELS[]  = 'data_kunjungan';
    $this->_COLUMNS = [
      'data_kunjungan.id as id',
      // 'data_kunjungan.id_hash',
			'data_kunjungan.tanggal_dibuat',
			'data_kunjungan.last_update',
			'data_kunjungan.nomor_rm',
			'data_kunjungan.poli',
			'data_kunjungan.status',
			'data_kunjungan.jenis_peserta',
      'data_poli.diagnosa',
      'data_rm.nama',
      'data_rm.tanggal_lahir as umur',
    ];
    $this->_COSTUME_JOIN = "
      LEFT JOIN data_poli ON data_poli.id = data_kunjungan.poli_id
      LEFT JOIN data_rm ON data_rm.nomor_rm = data_kunjungan.nomor_rm
    ";
    $this->PDO = $PDO ?? MyPDO::getInstance();
  }
}
