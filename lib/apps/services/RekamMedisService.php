<?php

use Simpus\Apps\Middleware;
use Model\Simpus\MedicalRecords;
use Simpus\Helper\HttpHeader;
use System\Database\MyPDO;

class RekamMedisService extends Middleware
{
    /** @var MyPDO */
    private $PDO;
    /**
     * @param MyPDO $PDO DataBase class Dependency Injection
     */
    public function __construct(MyPDO $PDO = null)
    {
        $this->PDO = $PDO ?? new MyPDO();
        // cek access
        if ($this->getMiddleware()['auth']['login'] == false) {
            HttpHeader::printJson(['status' => 'unauthorized'], 500, [
                "headers" => [
                    'HTTP/1.0 401 Unauthorized',
                    'Content-Type: application/json'
                ]
            ]);
        }
    }

    private function errorhandler(){
        HttpHeader::printJson(['status' => 'bad request'], 500, [
            "headers" => [
                'HTTP/1.1 400 Bad Request',
                'Content-Type: application/json'
            ]
        ]);
    }

    /**
     * Cek nomor rm kk bedasrkan paramater yang ada
     * @param array $params Rule pengecekan nomor kk
     * @return array hasil pencarian data
     */
    public function search_nomor_rm_kk(array $params): array
    {
        $nama_kk    = $params['n'] ?? $this->errorhandler();
        $alamat     = $params['a'] ?? $this->errorhandler();
        $rt         = $params['r'] ?? $this->errorhandler();
        $rw         = $params['w'] ?? $this->errorhandler();

        $data = new MedicalRecords( $this->PDO );
        $data->filterByNamaKK($nama_kk);
        $data->filterByAlamat($alamat);
        $data->filterByRt($rt);
        $data->filterByRw($rw);
        $data->forceLimitView(1);
        $result = $data->result()[0]['nomor_rm_kk'] ?? null;

        return [
            'status'        => $result != null ? 'ok' : 'no content',
            'nomor_rm_kk'   => $result ?? '',
            'headers'       => ['HTTP/1.1 200 Oke']
        ];
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
        $data->filterByNomorRm( $nomor_rm );
        $data->forceLimitView(1);
        $result = $data->result()[0] ?? null;

        return [
            'status'    => $result != null ? 'ok' : 'no content',
            'data'      => $result ?? [],
            'headers'   => ['HTTP/1.1 200 Oke']
        ];
    }

    /**
     * Cek nomor rm valid atau tidak
     * @param array $params nomor rm yang akan dicek
     * @return array jumlah data yang ditemukan
     */
    public function valid_nomor_rm(array $params): array
    {
        $data = new MedicalRecords( $this->PDO );
        $data->filterByNomorRm( $params[ 'nr' ] ?? null );
        $data->forceLimitView(1);
        $result = $data->maxData() ?? null;

        return [
            'status'    => $result != null ? 'ok' : 'no content',
            'found'     => $result ?? 0,
            'headers'   => ['HTTP/1.1 200 Oke']
        ];
    }

    public function nomor_rm_terahir(array $pramas): array
    {
      $upper_limit = $pramas['limit'] ?? 14000;
      // ambil nomor rm terakhir
      $data = new MedicalRecords($this->PDO);
      $data->limitView(1)
        ->orderUsing("DESC")
        ->sortUsing('nomor_rm');

      $last_nomor_rm  = $data->resultAll("WHERE `nomor_rm` < $upper_limit");
      $upper_nomor_rm = $data->resultAll();

      return array (
        'status' => 'ok',
        'data' => array (
          "last_nomor_rm"   => $last_nomor_rm[0]['nomor_rm'],
          "upper_nomor_rm"  => $upper_nomor_rm[0]['nomor_rm'],
        ),
        'headers'       => ['HTTP/1.1 200 Oke']
      );
    }

    /**
     * Search data rm by using query search
     * @param array $param search querys
     * @return array list data rm yang sesuai query pencarian
     */
    public function search(array $param): array
    {
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
        ));
        $validation->run($param);
        if ($validation->errors()) {
            return array (
                'status' => 'error',
                'error' => $validation->get_errors_array(),
                'headers'   => ['HTTP/1.1 200 Oke']
            );
        }

        // ambil configurasi
        $sort       = $param['sort'] ?? 'nomor_rm';
        $order      = $param['order'] ?? 'ASC';
        $page       = $param['page'] ?? 1;
        $max_page   = 1;
        $limit      = 10;

        // ambil search parameter
        $main_search        = $param['main-search'] ?? '';
        $nomor_rm_search    = $param['nomor-rm-search'] ?? '';
        $alamat_search      = $param['alamat-search'] ?? '';
        $no_rt_search       = $param['no-rt-search'] ?? '';
        $no_rw_search       = $param['no-rw-search'] ?? '';
        $nama_kk_search     = $param['nama-kk-search'] ?? '';
        $no_rm_kk_search    = $param['no-rm-kk-search'] ?? '';
        $strict_search      = isset( $param['strict-search'] ) ? true : false;

        // core
        $data = new MedicalRecords( $this->PDO );

        // setup data
        $data->sortUsing($sort);
        $data->orderUsing($order);
        $data->limitView($limit);

        // query data
        $data->filterByNama( $main_search );
        $data->filterByNomorRm( $nomor_rm_search);
        $data->filterByAlamat($alamat_search );
        $data->filterByRt( $no_rt_search );
        $data->filterByRw( $no_rw_search );
        $data->filterByNamaKK( $nama_kk_search );
        $data->filterByNomorRmKK( $no_rm_kk_search );

        // setup page
        $max_page = $data->maxPage();
        $page = $page > $max_page ? $max_page : $page;
        $data->currentPage($page);

        // excute query
        $result =  $data->result( $strict_search );

        return [
            'status'    => $result != null ? 'ok' : 'no content',
            "maks_page" => (int) $max_page,
            "cure_page" => (int) $page,
            'data'      => $result ?? 0,
            'headers'   => ['HTTP/1.1 200 Oke']
        ];


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

        if( isset($param['all']) ){
            $data = new MedicalRecords( $this->PDO );

            // set-up data
            $data->sortUsing($sort);
            $data->orderUsing($order);
            $data->limitView($limit);

            // setup data
            $max_page   = $data->maxPage();
            $page       = $page > $max_page ? $max_page : $page;
            $data->currentPage($page);
            $result     = $data->resultAll();

            return [
                'status'    => $result != null ? 'ok' : 'no content',
                "maks_page" => (int) $max_page,
                "cure_page" => (int) $page,
                'data'      => $result ?? 0,
                'headers'   => ['HTTP/1.1 200 Oke']
            ];
        }

        // parse umur
        $min_max    = $this->parseRangeTime( $umur );
        if( $min_max == false ) {
            return ['status'  => 'Bad Request', 'message' => 'time format not support', 'headers' => ['HTTP/1.1 400 Bad Request'] ];
        }

        $data = new MedicalRecords( $this->PDO );
        // configurasi
        $data->sortUsing($sort);
        $data->orderUsing($order);
        $data->limitView($limit);

        // filter by tanggal
        if( $umur != '0-100' ) $data->filterRangeTanggalLahir($min_max[0], $min_max[1]);

        // filter by alamat
        $valid_desa = ["bandarjo", "branjang", "kalisidi", "keji", "lerep", "nyatnyono"];
        foreach( explode('-', $desa) as $desa_search ){
            if( in_array($desa_search, $valid_desa) ){
                $data->filtersAddAlamat( $desa_search );
                unset($valid_desa[$desa_search]);
            }
        }

        // filter status kk
        if( $status_kk == 'on' ) $data->filterStatusKK();

        // setup data
        $max_page = $data->maxPage();
        $page = $page > $max_page ? $max_page : $page;
        $data->currentPage($page);
        $result = $data->results();

        // filter by duplicate content
        if( $duplicate != null ){
            $support_filter = in_array($duplicate, $data->getColumnSupport()) ? $duplicate : 'alamat';
            $result = $this->getDuplicate($support_filter, $data->getQueryStatment(true, false));

            $max_page   = 1;
            $page       = 1;
        }

        return [
            'status'    => $result != null ? 'ok' : 'no content',
            "maks_page" => (int) $max_page,
            "cure_page" => (int) $page,
            'data'      => $result ?? 0,
            'headers'   => ['HTTP/1.1 200 Oke']
        ];
    }

    // private function (helper)

    private function getDuplicate($duplicate, $where_statment)
    {
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
                  ($where_statment)
            )
          dt ON y.nama = dt.nama AND y.$duplicate = dt.$duplicate
          ORDER BY
            y.nama, y.$duplicate"
        );
        return $this->PDO->resultset();
    }

    private function parseRangeTime(string $time)
    {
        $min_max = explode('-', $time);

        if( count($min_max) > 1){
            return array_map(function($time){
                return date("Y-m-d", time() - ((int) $time * 31536000) );
            }, $min_max);
        }
        return false;
    }
}
