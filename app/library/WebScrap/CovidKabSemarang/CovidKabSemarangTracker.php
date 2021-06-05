<?php

namespace WebScrap\CovidKabSemarang;

use System\Database\MyPDO;

/** class untuk minyimpan data kedalam data base */
class CovidKabSemarangTracker
{
  /** @var MyPDO */
  private $PDO;
  private $list_kecamatan  = [];
  private $_filters_lokasi = [];
  private $_filters_waktu  = [];

  public function setFiltersLocation(array $val)
  {
    $this->_filters_lokasi = $val;
    return $this;
  }

  public function setFiltersDate(array $val)
  {
    $this->_filters_waktu = $val;
    return $this;
  }

  /**
   * @param MyPDO $PDO DataBase class Dependency Injection
   */
  public function __construct(MyPDO $PDO = null)
  {
    $this->PDO              = $PDO ?? MyPDO::getInstance();
    $this->list_kecamatan   = $this->getListKecamatanDesa();
  }

  /** Menyimpan data kedalam database.
   *  Force save to DBS
   *
   * @return boolean true jika data berhasil disimpan
   */
  public function createIndex(): bool
  {
    $new_request    = new CovidKabSemarangAll();
    $time           = (int) time();

    foreach ($new_request->catchAll()['data'] as $kecamatan) {
      foreach ($kecamatan['data'] as $kelurahan) {
        // simpan data
        if (! $this->creat([
            'date'                      => (int) $time,
            'location'                  => $this->searchId($kecamatan, $kelurahan['desa']),
            'suspek'                    => (int) $kelurahan['pdp']['dirawat'],
            'suspek_discharded'         => (int) $kelurahan['pdp']['sembuh'],
            'suspek_meninggal'          => (int) $kelurahan['pdp']['meninggal'],
            'konfirmasi_symptomatik'    => (int) $kelurahan['positif']['dirawat'],
            'konfirmasi_asymptomatik'   => (int) $kelurahan['positif']['isolasi'],
            'konfirmasi_sembuh'         => (int) $kelurahan['positif']['sembuh'],
            'konfirmasi_meninggal'      => (int) $kelurahan['positif']['meninggal']
          ])
        ) {
          return false;
        }
      }
    }

    return true;
  }

  /** Menyimpan data kedalam database,
   *  dengan membandingkan data lama terlebih dahulu.
   *
   * @param array $old_data Data tobe compire
   * @return boolean true jika berhasil disimpan
   * */
  public function createIndex_compire(array $old_data): bool
  {
    $time = time();

    // get data
    $data_covid = CovidKabSemarangAll::instant()->catchAll();

    // prepare data untuk compire dengan old data
    $new_data = array (
      'kasus_posi' => $data_covid['kasus_posi'],
      'kasus_isol' => $data_covid['kasus_isol'],
      'kasus_semb' => $data_covid['kasus_semb'],
      'kasus_meni' => $data_covid['kasus_meni'],
      'suspek' => $data_covid['suspek'],
      'suspek_discharded' => $data_covid['suspek_discharded'],
      'suspek_meninggal' => $data_covid['suspek_meninggal'],
    );

    // compire new data dan old data
    if (array_values($new_data) != $old_data) {

      foreach ($data_covid['data'] as $kecamatan) {
        foreach ($kecamatan['data'] as $kelurahan) {
          // simpan data kedatabase
          $this->creat(array (
            'date'                      => (int) $time,
            'location'                  => $this->searchId($kecamatan['kecamatan'], $kelurahan['desa']),
            'suspek'                    => (int) $kelurahan['pdp']['dirawat'],
            'suspek_discharded'         => (int) $kelurahan['pdp']['sembuh'],
            'suspek_meninggal'          => (int) $kelurahan['pdp']['meninggal'],
            'konfirmasi_symptomatik'    => (int) $kelurahan['positif']['dirawat'],
            'konfirmasi_asymptomatik'   => (int) $kelurahan['positif']['isolasi'],
            'konfirmasi_sembuh'         => (int) $kelurahan['positif']['sembuh'],
            'konfirmasi_meninggal'      => (int) $kelurahan['positif']['meninggal']
          ));
        }
      }

      return true;
    }

    return false;
  }

  /** menampilkan data berserta rincian perdesa dalam satu waktu (filter)
   * @return array data covid per desa
   */
  public function result(): array
  {
    $grupByDate = [];

    foreach ($this->_filters_waktu as $date) {
      $this->PDO->query($this->queryBuilder());
      $this->PDO->bind(':date', $date);
      $grupByDate[$date] = $this->PDO->resultset();
    }
    return $grupByDate;
  }

  /** menghitung resume data dalam satu waktu (semua data)
   * @return array resume data covid
   */
  public function result_countAll(): array
  {
    $this->PDO->query($this->queryBuilder_count(null));
    return $this->PDO->resultset();
  }

  /** menghitung resume data dalam satu waktu (filter)
   * @return array resume data covid
   */
  public function result_count(): array
  {
    if ($this->_filters_waktu == null ) return [];
    $date = implode(', ', $this->_filters_waktu);
    $this->PDO->query($this->queryBuilder_count($date));
    return $this->PDO->resultset();
  }

  /** list data yang tersedia di databse
   * @return array list tanggal format timestamp
   */
  public function listOfDate(int $date = 0) :array
  {
    $this->PDO->query(
      "SELECT
        `date`
      FROM
        `covid_tracker`
      WHERE
        `date` > :date
      GROUP BY `date`
      ORDER BY `date`
      DESC"
    );
    $this->PDO->bind(':date', $date);
    return $this->PDO->resultset();
  }

  // private method

  /** Query builder untuk menghitung jumlah per kasus dalam atu waktu
   * @param string tanggal data di-index
   * @return string pdo query string
   */
  private function queryBuilder_count($date): string
  {
    $where_statment = $date == null ? '' : "WHERE `date` IN ($date)";
    $query          =
      "SELECT
        covid_tracker.location,
        covid_tracker.date                          AS date_create,
        SUM(covid_tracker.suspek)                   AS suspek,
        SUM(covid_tracker.suspek_discharded)        AS suspek_discharded,
        SUM(covid_tracker.suspek_meninggal)         AS suspek_meninggal,
        SUM(covid_tracker.konfirmasi_symptomatik)   AS konfirmasi_symptomatik,
        SUM(covid_tracker.konfirmasi_asymptomatik)  AS konfirmasi_asymptomatik,
        SUM(covid_tracker.konfirmasi_sembuh)        AS konfirmasi_sembuh,
        SUM(covid_tracker.konfirmasi_meninggal)     AS konfirmasi_meninggal
      FROM `covid_tracker`
      INNER JOIN  `desa_kecamatan`
        ON desa_kecamatan.id = covid_tracker.location
      $where_statment
      GROUP BY `date`
      ";
    return $query;
  }

  /** Query builder untuk mendapat data berupa query,
   * (urut Ascending berdasrkan id)
   * @return string pdo query string
   */
  private function queryBuilder(): string
  {
    $listDate = implode("','", $this->_filters_lokasi);
    $query =
      "SELECT
        desa_kecamatan.kecamatan,
        desa_kecamatan.desa,
        covid_tracker.id,
        covid_tracker.date,
        covid_tracker.location,
        covid_tracker.suspek,
        covid_tracker.suspek_discharded,
        covid_tracker.suspek_meninggal,
        covid_tracker.konfirmasi_symptomatik,
        covid_tracker.konfirmasi_asymptomatik,
        covid_tracker.konfirmasi_sembuh,
        covid_tracker.konfirmasi_meninggal
      FROM `covid_tracker`
      INNER JOIN  `desa_kecamatan`
        ON desa_kecamatan.id = covid_tracker.location
      WHERE
        `date` = :date
        AND
          `kecamatan` IN ('$listDate')
      ORDER BY `id` ASC";
    return $query;
  }

  /** menyimpan data baru kedalam database (row by row)
   * @param array $params array data yang akan disimpan
   * @return bool true jika data berhasil disimpan
   */
  private function creat(array $params = array (
    'date'                    => '',
    'location'                => '',
    'suspek'                  => 0,
    'suspek_discharded'       => 0,
    'suspek_meninggal'        => 0,
    'konfirmasi_symptomatik'  => 0,
    'konfirmasi_asymptomatik' => 0,
    'konfirmasi_sembuh'       => 0,
    'konfirmasi_meninggal'    => 0
  )): bool
  {
    $this->PDO->query(
      "INSERT INTO
        `covid_tracker` (
          `id`,
          `date`,
          `location`,
          `suspek`,
          `suspek_discharded`,
          `suspek_meninggal`,
          `konfirmasi_symptomatik`,
          `konfirmasi_asymptomatik`,
          `konfirmasi_sembuh`,
          `konfirmasi_meninggal`
        )
      VALUES
        (
          :id,
          :date,
          :location,
          :suspek,
          :suspek_discharded,
          :suspek_meninggal,
          :konfirmasi_symptomatik,
          :konfirmasi_asymptomatik,
          :konfirmasi_sembuh,
          :konfirmasi_meninggal
        )"
      );

    $this->PDO->bind(':id', '');
    $this->PDO->bind(':date', $params['date']);
    $this->PDO->bind(':location', $params['location']);
    $this->PDO->bind(':suspek', $params['suspek']);
    $this->PDO->bind(':suspek_discharded', $params['suspek_discharded']);
    $this->PDO->bind(':suspek_meninggal', $params['suspek_meninggal']);
    $this->PDO->bind(':konfirmasi_symptomatik', $params['konfirmasi_symptomatik']);
    $this->PDO->bind(':konfirmasi_asymptomatik', $params['konfirmasi_asymptomatik']);
    $this->PDO->bind(':konfirmasi_sembuh', $params['konfirmasi_sembuh']);
    $this->PDO->bind(':konfirmasi_meninggal', $params['konfirmasi_meninggal']);

    $this->PDO->execute();
    if ($this->PDO->rowCount() > 0) return true;

    return false;
  }

  private function searchId($kecamatan, $desa)
  {
    $kecamatan  = strtolower($kecamatan);
    $desa       = strtolower($desa);
    $filter     = array_filter($this->list_kecamatan, function($val) use ($desa, $kecamatan) {
      return ($val['kecamatan'] == $kecamatan && $val['desa'] == $desa);
    });
    return array_values($filter)[0]['id'];
  }

  private function getListKecamatanDesa(): array
  {
    $this->PDO->query("SELECT * FROM `desa_kecamatan`");
    return $this->PDO->resultset();
  }
}
