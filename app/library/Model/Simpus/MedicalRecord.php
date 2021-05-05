<?php

namespace Model\Simpus;

use Helper\String\Str;
use System\Database\MyPDO;

/**
 * Perent Class fungsinya untuk menampung semua filed Rekam Medis
 */
class MedicalRecord
{
  /** @var MyPDO Instant PDO */
  private $PDO;

  /** @var int id record */
  protected $_id;
  /** @var string No Rekam Medis */
  protected $_nomorRM;
  /** tanggal data dibuat */
  protected $_dataDibuat;
  /** @var string Nama lengkap */
  protected $_nama;
  /** @var string tanggal lahir 30-12-1990*/
  protected $_tanggalLahir;
  /** @var int $_alamat_luar 0=dalam, 1=luar*/
  protected $_alamat_luar = 0;
  /** @var string Almat tanpa rt rw */
  protected $_alamat;
  /** @var int Nomor Rt */
  protected $_nomorRt;
  /** @var int Nomor RW */
  protected $_nomorRw;
  /** @var string Nama Kepla Keluarga*/
  protected $_namaKK;
  /** @var string No Rekam Medis Kepala Keluarga*/
  protected $_nomorRM_KK;
  /** @var string Status grub kesehatan (string in array) */
  protected $_status = "null";

  /** @var string last query used */
  private $_last_query;

  // getter
  /**
   * get nomor rm
   * @return string nomor rekam medis
   */
  public function getNomorRM()
  {
    return $this->_nomorRM;
  }

  /**
   * get data dibuat
   * @return int tanggal dibuat
   */
  public function getDataDibuat()
  {
    return $this->_dataDibuat;
  }

  /**
   * get nama lengkap
   * @return string Nama lengkap pasien
   */
  public function getNama()
  {
    return $this->_nama;
  }

  /**
   * get tanggal lahir
   * @return string tanggal lahir
   */
  public function getTangalLahir()
  {
    return $this->_tanggalLahir;
  }

  /**
   * get info alamat luar
   * @return bool True jika luar wilayah
   */
  public function getAlamatLuar(): bool
  {
    return $this->_alamat_luar == 0 ? false : true;
  }

  /**
   * get alamat pasien jk luar wilayah tulis lengkap
   * @return string tanggal dibuat
   */
  public function getAlamat()
  {
    return $this->_alamat;
  }

  /**  @return string Alamat Lengkap  */
  public function getAlamatLengkap()
  {
    return $this->_alamat . ' Rt '. $this->_nomorRt . '/ Rw ' . $this->_nomorRw;
  }

  /**
   * get nomor rt alamat dalam wilayah
   * @return int nomor rt
   */
  public function getNomorRt()
  {
    return $this->_nomorRt;
  }

  /**
   * get nomor rw alamat dalam wilayah
   * @return int nomor rw
   */
  public function getNomorRw()
  {
    return $this->_nomorRw;
  }

  /**
   * get nama kepla keluarga
   * @return int tanggal dibuat
   */
  public function getNamaKK()
  {
    return $this->_namaKK;
  }

  /**
   * get nomor rekam medis kepala keluarga jika ada
   * @return int nomor rekam medis jka ada
   */
  public function getNomorRM_KK()
  {
    return $this->_nomorRM_KK;
  }

  /**
   * get status grup kesehatan
   * @return string Get status grup kesehatan
   */
  public function getStatus()
  {
    return $this->_status;
  }

  /**
   * get last use query
   * @return string last query used
   */
  public function getLastQuery(): string
  {
    return $this->_last_query;
  }

  public function getData(): array
  {
    return $this->convertToData();
  }

  // setter

  /**
   * set nomor rekam medis
   *
   * data hanya disimpan jika format benar (nomor rm)
   * @param string $val 6 digit nomor rekam medis
   *
   */
  public function setNomorRM($val)
  {
    $this->_nomorRM = Str::fillText($val, 6, 0);
    return $this;
  }

  /**
   * set data dibuat dalam time span
   * @param string $val data dibuat
   */
  public function setDataDibuat($val)
  {
    $this->_dataDibuat = $val;
    return $this;
  }

  /**
   * set nama pasien
   * @param string $val nama pasien
   */
  public function setNama($val)
  {
    $val = strtolower($val);
    $this->_nama = $val;
    return $this;
  }

  /**
   * set tanggal lahir
   * @param string $val tanggal lahir
   */
  public function setTanggalLahir($val)
  {
    $this->_tanggalLahir = $val;
    return $this;
  }

  /**
   * set alamat luar wilayah
   * false jika dalam, true jika luar
   */
  public function setAlamatLuar(bool $value)
  {
    if ($value) {
      $this->_alamat_luar = 1;
    } else {
      $this->_alamat_luar = 0;
    }
    return $this;
  }

  /**
   * set alamat tanpa rt rw
   * @param string $val alamat
   */
  public function setAlamat($val)
  {
    $val = strtolower($val);
    $this->_alamat = $val;
    return $this;
  }

  /**
   * set nomor rt
   * @param string $val nomor rt
   */
  public function setNomorRt($val)
  {
    $this->_nomorRt = (int) $val;
    return $this;
  }

  /**
   * set nomor rw
   * @param string $val nomor rw
   */
  public function setNomorRw($val)
  {
    $this->_nomorRw = (int) $val;
    return $this;
  }

  /**
   * set nama kepal keluarga
   * @param string $val noma kepla keluarga
   */
  public function setNamaKK($val)
  {
    $val = strtolower($val);
    $this->_namaKK = $val;
    return $this;
  }

  /**
   * set nomor rm kepala keluarga
   * @param string $val nomor rt
   */
  public function setNomorRM_KK($val)
  {
    $this->_nomorRM_KK = Str::fillText($val, 6, 0);
    return $this;
  }

  /**
   * Set status grup kesehatan
   */
  public function setStatus(string $val)
  {
    $val = strtolower($val);
    $this->_status = $val;
    return $this;
  }

  /**
   * Convert array ke parameter
   * @param array $data Array to convert
   */
  public function convertFromArray(array $data)
  {
    $this->convertFromData($data);
    return $this;
  }

  /**
   * buat class baru
   * @param MyPDO $PDO Intial PDO
   * */
  public function __construct(MyPDO $PDO = null)
  {
    $this->PDO = $PDO ?? MyPDO::getInstance();
  }

  /**
   * buat kelas baru menggunkan id
   *
   * **multy contruct dost support by php**
   * @param int $id
   * @param MyPDO $PDO Intial PDO
   * @return MedicalRecord
   */
  public static function withId(int $id, MyPDO $PDO = null)
  {
    $instance = new MedicalRecord($PDO);
    $instance->_id = $id;
    $instance->refresh();
    return $instance;
  }

  /**
   * buat kelas baru menggunkan data
   *
   * **multy contruct dost support by php**
   * @param array $data
   * @param MyPDO $PDO Intial PDO
   * @return MedicalRecord
   */
  public static function withData(array $data, MyPDO $PDO = null)
  {
    $instance = new MedicalRecord($PDO);
    $instance->convertFromData($data);
    return $instance;
  }

  /**
   * fungsi untuk menkonfersi data bentuk array menjadi properti kelas
   *
   * @param array $data
   * merubah data array ke property class
   * - id             -> id
   * - nomorRM        -> nomor_rm     *
   * - dataDibuat     -> data_dibuat
   * - nama           -> nama
   * - tanggalLahir   -> tanggal_lahir
   * - alamat         -> alamat
   * - nomorRt        -> nomor_rt
   * - nomorRw        -> nomor_rw
   * - namaKK         -> nama_kk
   * - nomorRM_KK     -> nomor_rm_kk
   * - status         -> status
   *
   */
  private function convertFromData(array $data)
  {
    $this->_id           = $data['id'] ?? $this->_id;;
    $this->_nomorRM      = $data['nomor_rm'] ?? $this->_nomorRM;;
    $this->_dataDibuat   = $data['data_dibuat'] ?? $this->_dataDibuat;
    $this->_nama         = $data['nama'] ?? $this->_nama;
    $this->_tanggalLahir = $data['tanggal_lahir'] ?? $this->_tanggalLahir;
    $this->_alamat_luar  = $data['alamat_luar'] ?? $this->_alamat_luar;
    $this->_alamat       = $data['alamat'] ?? $this->_alamat;
    $this->_nomorRt      = $data['nomor_rt'] ?? $this->_nomorRt;
    $this->_nomorRw      = $data['nomor_rw'] ?? $this->_nomorRw;
    $this->_namaKK       = $data['nama_kk'] ?? $this->_namaKK;
    $this->_nomorRM_KK   = $data['nomor_rm_kk'] ?? $this->_nomorRM_KK;
    $this->_status       = $data['status'] ?? $this->_status;

    return $this;
  }

  /**
   * fungsinya untuk mengkonfersi proprti kelas ke data array
   * @return array data dalam bentuk array assosiatif
   */
  private function convertToData(): array
  {
    return array(
      'nomor_rm'      => $this->_nomorRM,
      'data_dibuat'   => $this->_dataDibuat,
      'nama'          => $this->_nama,
      'tanggal_lahir' => $this->_tanggalLahir,
      'alamat_luar'   => $this->_alamat_luar,
      'alamat'        => $this->_alamat,
      'nomor_rt'      => $this->_nomorRt,
      'nomor_rm'      => $this->_nomorRw,
      'nama_kk'       => $this->_namaKK,
      'nomor_rm_kk'   => $this->_nomorRM_KK,
      'status'        => $this->_status,
    );
  }

  public function filter()
  {
    $this->_nomorRM     = Str::fillText($this->_nomorRM, 6, 0);
    $this->_nomorRM_KK  = Str::fillText($this->_nomorRM_KK, 6, 0);
    $this->_nama        = strtolower($this->_nama);
    $this->_namaKK      = strtolower($this->_namaKK);
    $this->_alamat      = strtolower($this->_alamat);

    return $this;
  }

  /**
   * refresh/ambil/pull semua data dari database, munggunakn id
   * @return boolean
   * bila berhasil di refresh nilainya true
   */
  public function refresh(): bool
  {
    // memuat ulang data dari data base menggunakn id
    $this->PDO->query("SELECT * FROM `data_rm` WHERE `id` = :id");
    $this->PDO->bind(':id', $this->_id);
    if ($data = $this->PDO->single()) {
      $this->convertFromData($data);
      return true;
    }
    return false;
  }

  /**
   * refresh/ambil/pull semua data dari database, munggunakn hash_id
   * @return boolean
   * True jika data berhasil diambil
   */
  public function refreshUsingIdHash($Id_hash): bool
  {
    // ambil id dari hash_code nya
    $this->PDO->query(
      "SELECT *
        FROM (
          SELECT *
          FROM `data_rm`
        UNION
          SELECT *
          FROM `staging_rm`
        ) AS merge_table
      WHERE
        merge_table.data_dibuat = :time_stamp
      ");

    $this->PDO->bind(":time_stamp", $Id_hash);
    if ($this->PDO->single()) {
      $this->convertFromData( $this->PDO->single() );
      return true;
    }
    return false;
  }

  // method
  /**
   *  update / simpan data ke data base
   * @return boolean
   * bila berhasil disimpan nilaninya true
   */
  public function save($table_nama = "data_rm"): bool
  {
    $table_nama = $table_nama == 'data_rm' ? 'data_rm' : 'staging_rm';              // mencegah input nama table lain
    // memuat ulang data dari data base menggunakn id
    $id             = $this->_id;
    $nomor_rm       = $this->_nomorRM;
    $nama           = $this->_nama;
    $data_dibuat    = $this->_dataDibuat;
    $tanggal_lahir  = $this->_tanggalLahir;
    $alamat_luar     = $this->_alamat_luar;
    $alamat         = $this->_alamat;
    $nomor_rt       = $this->_nomorRt;
    $nomor_rw       = $this->_nomorRw;
    $nama_kk        = $this->_namaKK;
    $nomor_rm_kk    = $this->_nomorRM_KK;
    $status         = $this->_status;

    // jika nama dan no rm kosong tidak disimpan
    if ($nomor_rm == '' && $nama == '') return false;

    $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
    $query =
      "UPDATE `$table_nama`
      SET
        `nomor_rm` = '$nomor_rm',
        `data_dibuat` = '$data_dibuat',
        `nama` = '$nama',
        `tanggal_lahir` = '$tanggal_lahir',
        `alamat_luar` = '$alamat_luar',
        `alamat` = '$alamat',
        `nomor_rt` = '$nomor_rt',
        `nomor_rw` = '$nomor_rw',
        `nama_kk` = '$nama_kk',
        `nomor_rm_kk` = '$nomor_rm_kk',
        `status` = '$status'
      WHERE `id` = '$id'";
    // simpan query
    mysqli_query($link, $query);
    // bila berhasil return true
    if (mysqli_affected_rows($link) >= 0) {
      $this->_last_query = $query;
      return true;
    }
    // defult nya adalah salah
    return false;
  }

  /**
   * delet rm ini dari data base
   * @return boolean
   * bila berhasil dihapus nilainya true
   */
  public function delete()
  {
    $id = $this->_id;

    $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
    $query = "DELETE FROM data_rm WHERE id = $id";

    // esekusi query /delet
    mysqli_query($link, $query);
    // bila berhasil return true
    if( mysqli_affected_rows($link) > 0){
      $this->_last_query = $query;
      return true;
    }
    // defult nya adalah salah
    return false;
  }

  /**
   * membuat data rm baru kedata base
   *
   * @param int $id
   * opsonal bila kosong maka akan diteruskan dari id terakhir
   * @return boolean
   * bila berhasil disimpan nilainya true
   */
  public function insertNewOne($id = '', $table_name = 'data_rm'): bool
  {
    // menimpan data ke data base menggunakn id
    $nomor_rm       = $this->_nomorRM;
    $data_dibuat    = (int) $this->_dataDibuat;
    $nama           = $this->_nama;
    $tanggal_lahir  = $this->_tanggalLahir;
    $alamat_luar    = $this->_alamat_luar;
    $alamat         = $this->_alamat;
    $nomor_rt       = (int) $this->_nomorRt;
    $nomor_rw       = (int) $this->_nomorRw;
    $nama_kk        = $this->_namaKK;
    $nomor_rm_kk    = $this->_nomorRM_KK;
    $status         = $this->_status;

    // jika nama dan no rm kosong tidak disimpan
    if ($nomor_rm == '' && $nama == '') return false;

    $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
    $query =
      "INSERT INTO
        `$table_name`
      VALUES (
        '$id', '$nomor_rm',
        '$data_dibuat', '$nama',
        '$tanggal_lahir', '$alamat_luar', '$alamat',
        '$nomor_rt', '$nomor_rw',
        '$nama_kk', '$nomor_rm_kk',
        '$status'
      )";

    // esekusi query
    mysqli_query($link, $query);
    // bila berhasil return true
    if (mysqli_affected_rows($link) > 0) {
      $this->_last_query = $query;
      return true;
    }
    // defult nya adalah salah
    return false;
  }

  public function cekAxis(): bool
  {
    // memuat ulang data dari data base menggunakn id
    $id = $this->_id;
    $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
    $query = mysqli_query($link, "SELECT id FROM data_rm WHERE id = '$id' ");
    if (mysqli_num_rows( $query ) == 1) {
      return true;
    }
    return false;
  }

}
