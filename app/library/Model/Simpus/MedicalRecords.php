<?php

namespace Model\Simpus;

use Simpus\Helper\StringValidation;
use System\Database\MyModel;
use System\Database\MyPDO;
use System\Database\MyQuery;

class MedicalRecords extends MyModel
{
  // setup
  /**
   * where option dengan like or equal
   */
  private $_options = Array (
    'equal' => array (
      "imperssion" => [":", ""],
      "operator"   => "="
    ),
    'like' => array (
      "imperssion" => [":", ""],
      "operator"   => "LIKE"
    )
  );

  private $_addresses = [
    "addresses" => [
      "filters" => [],
      "strict" => false
    ]
  ];
  private $_dataPerPage = 10;
  private $_orderUsing = MyModel::ORDER_ASC;
  private $_orderColumn = 'id';

  // property

  /**
   * filter/seacrh dengan nomor rm
   * @param int $val Nomor RM
   */
  public function filterByNomorRm(string $val)
  {
    $verify = StringValidation::NumberValidation($val, 1, 6);
    if ($verify) {
      $len = strlen($val);
      $max = 6 - $len;
      for ($i=0; $i < $max; $i++) {
        $val = 0 . $val;
      }
      $this->_FILTERS[] = array (
        'id'      => rand(1, 10),
        'param'  => 'nomor_rm',
        'value'  => $val,
        'option' => $this->_options['equal'],
        'type'   => \PDO::PARAM_STR
      );
    }
    return $this;
  }

  /**
   * filter/seacrh dengan nama
   * @param string $val Nama
   */
  public function filterByNama(string $val)
  {
    $verify = StringValidation::NoHtmlTagValidation($val);
    if ($verify && ! empty($val)) {
      $this->_FILTERS[] = array (
        'id'      => rand(1, 10),
        'param'   => 'nama',
        'value'   => strtolower("%$val%"),
        'option'  => array (
          "imperssion" => [':', ''],
          "operator"   => "LIKE"
        ),
        'type'    => \PDO::PARAM_STR
      );
    }
    return $this;
  }

  /**
   * filter/seacrh dengan tanggal lahir
   * @param string $val tanggal lahir
   */
  public function filterByTanggalLahir(string $val)
  {
    $this->_FILTERS[] = array (
      'id'      => rand(1, 10),
      'param'   => 'tanggal_lahir',
      'value'   => strtolower($val),
      'option'  => $this->_options,
      'type'    => \PDO::PARAM_STR
    );
    return $this;
  }

  /**
   * filter/seacrh dengan alamat
   * @param string $val Alamat
   */
  public function filterByAlamat(string $val)
  {
    $verify = StringValidation::NoHtmlTagValidation($val);
    if ($verify && $val != '') {
      $this->_FILTERS[] = array (
        'id'      => rand(1, 10),
        'param'   => 'alamat',
        'value'   => strtolower("%$val%"),
        'option'  => $this->_options['like'],
        'type'    => \PDO::PARAM_STR
      );
    }
    return $this;
  }

  /**
   * filter/seacrh dengan Nomor Rt
   * @param string $val Nomor Rt
   */
  public function filterByRt(string $val)
  {
    if (is_numeric($val)) {
      $this->_FILTERS[] = array (
        'id'      => rand(1, 10),
        'param'   => 'nomor_rt',
        'value'   => $val,
        'option'  => $this->_options['equal'],
        'type'    => \PDO::PARAM_STR
      );
    }
    return $this;
  }

  /**
   * filter/seacrh dengan Nomor Rt
   * @param string $val Nomor Rt
   */
  public function filterByRw(string $val)
  {
    if (is_numeric($val)) {
      $this->_FILTERS[] = array (
        'id'      => rand(1, 10),
        'param'   => 'nomor_rw',
        'value'   => $val,
        'option'  => $this->_options['equal'],
        'type'    => \PDO::PARAM_STR
      );
    }
    return $this;
  }

  /**
   * filter/seacrh dengan nama kk
   * @param string $val Nama kk
   */
  public function filterByNamaKK(string $val)
  {
    $verify = StringValidation::NoHtmlTagValidation($val);
    if ($verify && ! empty($val)) {
      $this->_FILTERS[] = array (
        'id'      => rand(1, 10),
        'param'   => 'nama_kk',
        'value'   => strtolower("%$val%"),
        'option'  => $this->_options['like'],
        'type'    => \PDO::PARAM_STR
      );
    }
    return $this;
  }

  /**
   * filter/seacrh dengan nomor rm kk
   * @param int $val Nomor RM kk
   */
  public function filterByNomorRmKK(string $val)
  {
    $verify = StringValidation::NumberValidation($val, 1, 6);
    if ($verify) {
      $len = strlen($val);
      $max = 6 - $len;
      for ($i=0; $i < $max; $i++) {
        $val = 0 . $val;
      }
      $this->_FILTERS[] = array (
        'id'      => rand(1, 10),
        'param'   => 'nomor_rm',
        'value'   => $val,
        'option'  => $this->_options['equal'],
        'type'    => \PDO::PARAM_STR
      );
    }
    return $this;
  }

  /**
   * filter tanggal berdasarkan selisih tahun
   * @param int $minVal tahun termuda
   * @param int $maxVal tahun tertua
   */
  public function filterRangeTanggalLahir($minVal, $maxVal)
  {
    $this->costumeWhere(
      "`tanggal_lahir` BETWEEN DATE(:maxVal) AND DATE(:minVal)",
      array([':maxVal', $maxVal], [':minVal', $minVal])
    );
    return $this;
  }

  /**
   * filter berdasarkan alamat-alamat
   *
   * fungsi ini bisa ditulis berulang
   * @param string $val filter alamat tanpa rt / rw
   */
  public function filtersAddAlamat(string $val)
  {
    $this->_addresses['addresses']['filters'][] = array (
      'id'      => rand(1, 10),
      'param'   => 'alamat',
      'value'   => strtolower($val),
      'option'  => $this->_options['equal'],
      'type'    => \PDO::PARAM_STR
    );

    $this->_GROUP_FILTER = $this->_addresses;
    return $this;
  }

  /**
   * filter data berdasarkan kk (kepala keluarga)
   * @param bool $val True jika ingin memfilter KK
   */
  public function filterStatusKK(bool $val = true)
  {
    $this->costumeWhere(
      "`nomor_rm` = `nomor_rm_kk`",
      array()
    );
    return $this;
  }

  public function filterDupliate($duplicate)
    {
      $where = $this->getWhere();
      $this->PDO->query(
        "SELECT
          y.*
        FROM
          data_rm y
        INNER JOIN
          (
            SELECT
              *,
              COUNT(*) AS CountOf
            FROM
                data_rm
              GROUP BY
                nama, $duplicate
              HAVING
                COUNT(*) > 1
              AND
                ($where)
          )
        dt ON y.nama = dt.nama AND y.$duplicate = dt.$duplicate
        ORDER BY
          y.nama, y.$duplicate"
      );
      $this->bindingFilters();

      return $this->PDO->resultset();
    }

  // settup
  /**
   * Memilih kolomn yang akan di tampilkan
   */
  public function selectColumn(array $columns_name)
  {
    $this->_COLUMNS = $columns_name;
    return $this;
  }


  /**
   * @param MyPDO $PDO DataBase class Dependency Injection
   */
  public function __construct(MyPDO $PDO = null)
  {
    $this->_TABELS[]  = 'data_rm';
    $this->PDO = $PDO ?? MyPDO::getInstance();
  }

  // costume function
  /**
   * Mendapatkan jumlah seluruh data yang tersedia mengguna filter yang ditentukan
   * @return int Jumlah data yang tersedia
   */
  public function maxData(): int
  {
    $whereStantment = $this->grupQueryFilters( $this->mergeFilters() );
    $whereStantment = $whereStantment == '' ? '' : "WHERE $whereStantment";
    $this->PDO->query(
      "SELECT
        COUNT(id) as total
      FROM
        `data_rm`
      $whereStantment
    ");
    $this->bindingFilters();
    // var_dump($whereStantment);
    // exit;

    return $this->PDO->single()['total'] ?? 0;
  }

  /**
   * Mengambil jumlah page tersedia berdasarkan jumlah data yg ditampilkan
   * @return int Jumlah page tersedia
   */
  public function maxPage(): int
  {
    $page = $this->_limit_end - $this->_limit_start;
    $return = ceil($this->maxData() / $page);
    return $return < 1 ? 1 : $return;
  }

  /**
   * Mengurutkan berdasarkan column yang ditentukan,
   * list column dapat dilihat menggunkan getColumnSupport()
   * @param string $column_name Nama column yang akan diurutkan
   */
  public function sortUsing(string $column_name)
  {
    $this->_orderColumn = $column_name;
    $this->order($this->_orderColumn, $this->_orderUsing);
    return $this;
  }

  /**
   * Mengurutkan data berdasarkan ASC atau DESC
   * @param string $order_using ASC || DESC
   */
  public function orderUsing(string $order_using)
  {
    $this->_orderUsing = $order_using == "ASC" ? MyModel::ORDER_ASC : MyModel::ORDER_DESC;
    $this->order($this->_orderColumn, $this->_orderUsing);
    return $this;
  }

  /**
   * Jumlah data yang ditampilkan dalam sekali panggil,
   * min: 10, maks: 100
   * @param string $val Jumlah data yang ditampilkan
   */
  public function limitView($val)
  {
    $verify = StringValidation::NumberValidation($val, 1, 3);
    if ($verify) {
      $val = $val < 10 ? 10 : $val;
      $val = $val > 100 ? 100 : $val;
      $this->_dataPerPage = $val;
      $this->_limit_end = $this->_limit_start + $val;
    }
    return $this;
  }

  /**
   * Jumlah data yang ditampilkan dalam sekali panggil,
   * tanpa minum atau maksimum
   * @param string $val Jumlah data yang ditampilkan
   */
  public function forceLimitView($val)
  {
    $verify = StringValidation::NumberValidation($val, 1, 3);
    if ($verify) {
      $this->_dataPerPage = $val;
      $this->_limit_end = $this->_limit_start + $val;
    }
    return $this;
  }

  /**
   * Meset posisi page (limit satart, end)
   * @param int $page_pos Page postion defult = 0
   */
  public function currentPage(int $page_pos)
  {
    $page_pos = $page_pos < 1 ? 1 : $page_pos;
    $page_pos = $page_pos > 100 ? 100 : $page_pos;
    $page_pos = floor($page_pos);

    $this->_limit_start = ($page_pos * $this->_dataPerPage) - $this->_dataPerPage;
    return $this;
  }

  /**
   * @return array List column pada table
   */
  public function getColumnSupport()
  {
    $result = MyQuery::conn("COLUMNS", MyPDO::conn("INFORMATION_SCHEMA"))
      ->select()
      ->equal("TABLE_SCHEMA", DB_NAME)
      ->equal("TABLE_NAME", 'data_rm')
      ->all();

    return array_column($result, 'COLUMN_NAME') ?? array();
  }
}
