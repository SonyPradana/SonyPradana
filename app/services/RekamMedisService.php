<?php

use Model\Simpus\MedicalRecords;
use Simpus\Apps\Service;
use System\Database\MyPDO;

class RekamMedisService extends Service
{
  /** @var MyPDO */
  private $PDO;
  /**
   * @param MyPDO $PDO DataBase class Dependency Injection
   */
  public function __construct(MyPDO $PDO = null)
  {
    $this->error = new DefaultService();
    $this->PDO = $PDO ?? MyPDO::getInstance();
    // cek access
    $this->useAuth();
  }

  /**
   * Cek nomor rm kk bedasrkan paramater yang ada
   * @param array $params Rule pengecekan nomor kk
   * @return array hasil pencarian data
   */
  public function search_nomor_rm_kk(array $params): array
  {
    $nama_kk    = $params['n'] ?? $this->errorHandler(400);
    $alamat     = $params['a'] ?? $this->errorHandler(400);
    $rt         = $params['r'] ?? $this->errorHandler(400);
    $rw         = $params['w'] ?? $this->errorHandler(400);

    $data = new MedicalRecords( $this->PDO );
    $data
      ->filterByNamaKK($nama_kk)
      ->filterByAlamat($alamat)
      ->filterByRt($rt)
      ->filterByRw($rw)
      ->forceLimitView(1);

    $result = $data->result()[0]['nomor_rm_kk'] ?? null;

    return array(
      'code'        => 200,
      'status'      => $result != null ? 'ok' : 'no content',
      'nomor_rm_kk' => $result ?? '',
      'headers'     => ['HTTP/1.1 200 Oke']
    );
  }

  /**
   * Mencari profile rm dengan menggunakn nomor rm
   * @param array $params Nomor rm
   * @return array Profile data Rm
   */
  public function search_rm(array $params): array
  {
    $nomor_rm = $params[ 'nomor_rm' ] ?? null;

    $data = new MedicalRecords( $this->PDO );
    $data
    ->filterByNomorRm( $nomor_rm )
    ->forceLimitView(1);

    $result = $data->result()[0] ?? null;

    return array(
      'code'    => 200,
      'status'  => $result != null ? 'ok' : 'no content',
      'data'    => $result ?? [],
      'headers' => ['HTTP/1.1 200 Oke']
    );
  }

  /**
   * Cek nomor rm valid atau tidak
   * @param array $params nomor rm yang akan dicek
   * @return array jumlah data yang ditemukan
   */
  public function valid_nomor_rm(array $params): array
  {
    $data = new MedicalRecords( $this->PDO );
    $data
      ->filterByNomorRm( $params[ 'nr' ] ?? null )
      ->forceLimitView(1);

    $result = $data->maxData() ?? null;

    return array(
      'code'    => 200,
      'status'  => $result != null ? 'ok' : 'no content',
      'found'   => $result ?? 0,
      'headers' => ['HTTP/1.1 200 Oke']
    );
  }

  public function nomor_rm_terahir(array $request): array
  {
    $upper_limit = $request['limit'] ?? '014000';
    // force nomor rm 6 digit
    for ($i = 0; $i < 6 - strlen($upper_limit); $i++) {
      $upper_limit = 0 . $upper_limit;
    }
    // ambil nomor rm terakhir
    $data = new MedicalRecords($this->PDO);
    $data
      ->selectColumn(['nomor_rm'])
      ->forceLimitView(1)
      ->orderUsing("DESC")
      ->sortUsing('nomor_rm');

    // last nomor rm by index
    $last_nomor_rm  = $data->result();

    $data->costumeWhere("`nomor_rm` < :nrm", [[':nrm', $upper_limit]]);
    // lat nomotr rm by upper limit
    $upper_nomor_rm = $data->result();

    return array (
      'code'    => 200,
      'status'  => 'ok',
      'data'    => array (
        "last_nomor_rm"   => $last_nomor_rm[0]['nomor_rm'],
        "upper_nomor_rm"  => $upper_nomor_rm[0]['nomor_rm'],
      ),
      'headers' => ['HTTP/1.1 200 Oke']
    );
  }

  /**
   * Search data rm by using query search
   * @param array $param search querys
   * @return array list data rm yang sesuai query pencarian
   */
  public function search(array $request): array
  {
    // TODO: singgle searach nomor_rm and (nik and bpjs)
    // vallidation
    $validation = new GUMP('id');
    $validation->validation_rules(array (
      'main-search' => 'alpha_space|max_len,50',
      'nomor-rm-search' => 'numeric|max_len,6',
      'alamat-search' => 'alpha_space|max_len,20',
      'no-rt-search' => 'numeric|max_len,2',
      'no-rw-search' => 'numeric|max_len,2',
      'nama-kk-search' => 'alpha_space|max_len,50',
      'nomor-rm-kk-search' => 'numeric|max_len,6',
      // personal data
      'nik' => 'numeric|min_len,16|max_len,16',
      'nomor_jaminan' => 'numeric|min_len,8|max_len,13',
    ));
    $validation->run($request);
    if ($validation->errors()) {
      return array (
        'code'    => 200,
        'status'  => 'error',
        'error'   => $validation->get_errors_array(),
        'headers' => ['HTTP/1.1 200 Oke']
      );
    }

    // ambil configurasi
    $sort       = $request['sort'] ?? 'nomor_rm';
    $order      = $request['order'] ?? 'DESC';
    $page       = $request['page'] ?? 1;
    $max_page   = 1;
    $limit      = 10;

    // ambil search parameter
    $main_search        = $request['main-search'] ?? '';
    $nomor_rm_search    = $request['nomor-rm-search'] ?? '';
    $alamat_search      = $request['alamat-search'] ?? '';
    $no_rt_search       = $request['no-rt-search'] ?? '';
    $no_rw_search       = $request['no-rw-search'] ?? '';
    $nama_kk_search     = $request['nama-kk-search'] ?? '';
    $no_rm_kk_search    = $request['no-rm-kk-search'] ?? '';
    $strict_search      = isset( $request['strict-search'] ) ? true : false;
    // search in personal data
    $nik                = $request['nik'] ?? '';
    $nomor_jaminan      = $request['nomor-jaminan'] ?? '';

    // core
    $data = new MedicalRecords( $this->PDO );

    // setup data
    $data
      ->sortUsing($sort)
      ->orderUsing($order)
      ->limitView($limit)
    // query data
      ->filterByNama($main_search)
      ->filterByNomorRm($nomor_rm_search)
      ->filterByAlamat($alamat_search)
      ->filterByRt($no_rt_search)
      ->filterByRw($no_rw_search)
      ->filterByNamaKK($nama_kk_search)
      ->filterByNomorRmKK($no_rm_kk_search)
      ->setStrictSearch($strict_search)
    // personal data
      ->filtersByNik($nik)
      ->filtersByJaminan($nomor_jaminan);

    // setup page
    $max_page = $data->maxPage();
    $page = $page > $max_page ? $max_page : $page;
    $data->currentPage($page);

    // excute query
    $result = $data->result();

    return array(
      'code'      => '200',
      'status'    => $result != null ? 'ok' : 'no content',
      "maks_page" => (int) $max_page,
      "cure_page" => (int) $page,
      'data'      => $result ?? 0,
      'headers'   => ['HTTP/1.1 200 Oke']
    );
  }

  /**
   * View data rm with some filter
   * @param array $param filters rule
   * @return array list data rm
   */
  public function filter(array $param): array
  {
    // ambil configurasi
    $sort       = $param['sort'] ?? 'nomor_rm';
    $order      = $param['order'] ?? 'ASC';
    $page       = $param['page'] ?? 1;
    $max_page   = 1;
    $limit      = 25;

    // ambil search parameter
    $umur       = $param['umur'] ?? '0-100';
    $desa       = $param['desa'] ?? "bandarjo-branjang-kalisidi-keji-lerep-nyatnyono";
    $status_kk  = $param['status_kk'] ?? null;
    $duplicate  = $param['duplicate'] ?? null;

    if (isset($param['all'])) {
      $data = new MedicalRecords( $this->PDO );

      // set-up data
      $data
        ->sortUsing($sort)
        ->orderUsing($order)
        ->limitView($limit);

      // setup data
      $max_page   = $data->maxPage();
      $page       = $page > $max_page ? $max_page : $page;
      $data->currentPage($page);
      $result     = $data->result();

      return array(
        'code'      => 200,
        'status'    => $result != null ? 'ok' : 'no content',
        "maks_page" => (int) $max_page,
        "cure_page" => (int) $page,
        'data'      => $result ?? 0,
        'headers'   => ['HTTP/1.1 200 Oke']
      );
    }

    // parse umur
    $min_max    = $this->parseRangeTime( $umur );
    if ($min_max == false) {
      return $this->error(400);
    }

    $data = new MedicalRecords( $this->PDO );
    // configurasi
    $data
      ->sortUsing($sort)
      ->orderUsing($order)
      ->limitView($limit);

    // filter by tanggal
    if ($umur != '0-100') {
      $data->filterRangeTanggalLahir($min_max[0], $min_max[1]);
    }


    // filter by alamat
    $valid_desa = ["bandarjo", "branjang", "kalisidi", "keji", "lerep", "nyatnyono"];
    foreach (explode('-', $desa) as $desa_search) {
      if (in_array($desa_search, $valid_desa)) {
        $data->filtersAddAlamat( $desa_search );
        unset($valid_desa[$desa_search]);
      }
    }

    // filter status kk
    if ($status_kk == 'on') $data->filterStatusKK();

    // setup data
    $max_page = $data->maxPage();
    $page = $page > $max_page ? $max_page : $page;
    $data->currentPage($page);
    $result = $data->result();

    // filter by duplicate content
    if ($duplicate != null) {
      $support_filter = in_array($duplicate, $data->getColumnSupport()) ? $duplicate : 'alamat';

      $result = $data->filterDupliate($support_filter);

      $max_page   = 1;
      $page       = 1;
    }

    return array(
      'code'      => 200,
      'status'    => $result != null ? 'ok' : 'no content',
      "maks_page" => (int) $max_page,
      "cure_page" => (int) $page,
      'data'      => $result ?? 0,
      'headers'   => ['HTTP/1.1 200 Oke']
    );
  }

   private function parseRangeTime(string $time)
  {
    $min_max = explode('-', $time);

    if (count($min_max) > 1) {
      return array_map(function($time){
        return date("Y-m-d", time() - ((int) $time * 31536000) );
      }, $min_max);
    }
    return false;
  }
}
