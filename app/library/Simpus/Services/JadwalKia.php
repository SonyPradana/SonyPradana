<?php namespace Simpus\Services;

use System\Database\MyPDO;
use \DateTime;
use System\Database\MyQuery;

class JadwalKia
{
  /** @var MyPDO */
  protected $PDO = null;

  /**
   * Membuat kelas untuk memenage jadwal poli KIA
   * @param MyPDO Database Dependecy Injection
   */
  public function __construct(MyPDO $PDO = null)
  {
    $this->PDO = $PDO ?? new MyPDO();
  }

  public function getdata(string $bulan, string $tahun): array
  {
    // array untuk dikembalikan di result
    $date = array();
    $data = array();
    $first_week = array();   $date_fw = date("Y-m-d", strtotime("first friday $tahun-$bulan"));
    $third_week = array();   $date_tw = date("Y-m-d", strtotime("third friday $tahun-$bulan"));

    // koneksi data base ambil berdasarkan kriteria yang dibuat (bulan / tahun)
    $this->PDO->query (
      "SELECT
        *
      FROM
        `list_of_services`
      WHERE
        `event`=:event AND MONTH(Date) = :m
      ORDER BY `date` ASC"
    );
    $this->PDO->bind(':event', "imunisasi anak");
    $this->PDO->bind(':m', $bulan);

    // mengisi array sesuai hasil ditemukan didata base
    foreach ($this->PDO->resultset() as $row) {
      // mengisi array jenis vaksin
      $data[$row['event_detail']][] = date("d M", strtotime($row['date']));
      // mengisi array tanggal berdasarkan jenis vaksin
      $date[] = date("d M", strtotime($row['date']));

      // mendapatkan list vakin berdasarkan minggun I & III
      if ($date_fw == $row['date']) {
        $first_week[] = $row['event_detail'];
      }
      if( $date_tw == $row['date']){
        $third_week[] = $row['event_detail'];
      }
    }

    // mengurutkan dan menghapus duplikat
    $date = array_values( array_unique( $date ) );

    // menyusun hasil data
    // kembalian array
    return array (
      "version" => "1.0",
      "bulan" => date("M Y", strtotime("$tahun-$bulan-1")),
      "jadwal" => $date,
      "jumat pertama" => $first_week,
      "jumat ketiga" => $third_week,
      "data" => $data
    );
}

  /**
   * Mengambil list bulan yang sudah terisi
   */
  public function getAvilabeMonth()
  {
    // Koneksi data base
    $db = new MyQuery($this->PDO);
    $result = $db
      ->select('list_of_services')
      ->equal('event', 'imunisasi anak');

    $alreadyIndex = [];
    $id = 0;
    // mengkovert foormat dari YYYY-MM-dd menjai M
    foreach ($result->all() as $row) {
      if (! in_array(date("Y-m", strtotime($row['date'])), $alreadyIndex)) {
        $newRow = [];
        $newRow['id'] = $id;
        $newRow['date_mont'] = date("m", strtotime($row['date']));
        $newRow['date_mont_string'] = date("M", strtotime($row['date']));
        $newRow['date_year'] = date("Y", strtotime($row['date']));
        $alreadyIndex[] = date("Y-m", strtotime($row['date']));

        $arr[] = $newRow;
        $id++;
      }
    }
    // List bulan yang sudah tersedia
    return $arr;
  }

  public function getAvilabeDate(): array
  {
    return [];
  }

  /**
   * Mengecek jadwal sudah dibuat ata belum (data tidak boleh kembar)
   * @param string $date Tanggal / Jadwal format YYYY-MM-DD
   * @param string $vaksin Jenis vaksin
   * @return bool True ketika data sudah ada
   */
  public function cekJadwal($date, $vaksin): bool
  {
      // koneksi data base
      $this->PDO->query("SELECT `date`, `event_detail` FROM `list_of_services` WHERE `date`=:tanggal AND `event_detail`= :ev_dt");
      $this->PDO->bind(':tanggal', $date);
      $this->PDO->bind(':ev_dt', $vaksin);
      if ($this->PDO->single()) {
          // data sudah ada
          return true;
      }
      // data tidak ditemukan
      return false;
  }

  /**
   * Membuat jadwal imunisai poli kia
   * @param string $date Jadwal pelyanan dengan format YYYY-MM-DD
   * @param string $vaksin Jenis vaksin
   * @return bool True kita data berhasil dibuat/disimpan
   */
  public function buatJadwal($date, $vaksin): bool
  {
      // cek data kembar
      if ($this->cekJadwal($date, $vaksin)) return false;
      // koneksi data base
      $this->PDO->query("INSERT INTO `list_of_services` (`id`, `date`, `unit`, `event`, `event_detail`) VALUES (:id, :tanggal, :unit, :ev, :ev_dt )");
      $this->PDO->bind(':id', "");
      $this->PDO->bind(':tanggal', $date);
      $this->PDO->bind(':unit', "kia");
      $this->PDO->bind(':ev', "imunisasi anak");
      $this->PDO->bind(':ev_dt', $vaksin);
      $this->PDO->execute();
      if ($this->PDO->rowCount() > 0) {
          // data berhasil disimpan
          return true;
      }
      // data gagal sisimpan
      return false;
  }

  /**
   * Mengedit jadwal Pelayan KIA
   * @param string $from_date     Tanggal yang ingin dirubah
   * @param string $from_vaksin   Jenis vaksin yang ingin dirubah
   * @param string $to_date       Tanggal baru
   * @param string $to_vaksin   Jenis vaksin baru
   */
  public function editJadwal($from_date, $from_vaksin, $to_date, $to_vaksin)
  {
      // koneksi data base
      $this->PDO->query("UPDATE `list_of_services` SET `date` = :t_tanggal', `event_detail` = :t_ev_dt WHERE `date` = :f_tanggal AND `event_detail`= :f_ev_dt");
      $this->PDO->bind(':f_tanggal', $from_date);
      $this->PDO->bind(':f_ev_dt', $from_vaksin);
      $this->PDO->bind(':t_tanggal', $to_date);
      $this->PDO->bind(':t_ev_dt', $to_vaksin);
      $this->PDO->execute();
  }

  /**
   * Membuat jadwal imunisasi dalam satu bulan secara otomatis, hari libur tetap di index
   * @param int $bulan Bulan yang akan di index
   * @param int $tahun Tahun yang akan di index (base on bulan)     *
   */
  public function autoCreatJadwal(int $bulan, int $tahun): bool
  {
    // mengambil  jadwal jumat pertama dan ketiga
    $jumat_pertama = date("Y-m-d", strtotime("first friday $tahun-$bulan"));
    $jumat_ketiga  = date("Y-m-d", strtotime("third friday $tahun-$bulan"));

    // use Transaction
    $tc = []; // tc -> transactionCek
    $this->PDO->beginTransaction();
    $success = true;

    // loop hari setiap jumat pada bulan XXX
    $date = new DateTime("first Friday $tahun-$bulan");
    $thisMonth = $date->format('m');
    while ($date->format('m') === $thisMonth) {
      // hasil -> tanggal pada hari jumat
      $hari_ini = $date->format('Y-m-d');

      if ($hari_ini == $jumat_pertama) {
        // membuat jadwal pada jumat pertama
        $tc[] = $this->buatJadwal($hari_ini, "BCG");
        $tc[] = $this->buatJadwal($hari_ini, "Campak");
        $tc[] = $this->buatJadwal($hari_ini, "Rubella (MR)");
      } elseif ($hari_ini == $jumat_ketiga) {
        // membuat jadwal pada jumat ketiga
        $tc[] = $this->buatJadwal($hari_ini, "Campak");
        $tc[] = $this->buatJadwal($hari_ini, "Rubella (MR)");
      }

      // membuat jadwal pada setiap hari jumat
      $tc[] = $this->buatJadwal($hari_ini, "Hib");
      $tc[] = $this->buatJadwal($hari_ini, "HB");
      $tc[] = $this->buatJadwal($hari_ini, "DPT");
      $tc[] = $this->buatJadwal($hari_ini, "IPV");

      if (in_array(false, $tc) ) {
        // cancel transactionCek and break loop
        $this->PDO->cancelTransaction();
        $success = false;
        break;
      }

      // next looping hari jumat
      $date->modify('next Friday');
    }

    // end transtion
    if ($success) {
      $this->PDO->endTransaction();
    }

    return $success;
  }

}
