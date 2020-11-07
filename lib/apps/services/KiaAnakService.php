<?php

use Simpus\Apps\Middleware;
use Simpus\Simpus\KIAAnakRecords;
use Simpus\Helper\HttpHeader;

class KiaAnakService extends Middleware
{
    public function __construct()
    {
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

    public function search(array $params)
    {
        // validation
        $validation = new GUMP('id');
        $validation->validation_rules(array(
            'main-search' => 'alpha_space|max_len,50',
            'alamat-search' => 'alpha_space|max_len,20',
            'no-rt-search' => 'numeric|max_len,2',
            'no-rw-search' => 'numeric|max_len,2',
            'nama-kk-search' => 'alpha_space|max_len,50',
            'desa' => 'alpha|max_len,20',
            'tempat_pemeriksaan' => 'numeric|3'
        ));
        $validation->run($_GET);
        if ($validation->errors()) {
            return array (
                'status' => 'error',
                'error' => $validation->get_errors_array(),
                'headers'   => ['HTTP/1.1 200 Oke']
            );
        }

        // ambil parameter dari url
        $sort       = $params['sort'] ?? 'tanggal_dibuat';
        $order      = $params['order'] ?? 'ASC';
        $page       = $params['page'] ?? 1;
        $max_page   = 1;
        $limit      = 10;

        // ambil parameter dari url
        $main_search    = $params['main-search'] ?? '';
        $alamat_search  = $params['alamat-search'] ?? '';
        $no_rt_search   = $params['nomor-rt-search'] ?? '';
        $no_rw_search   = $params['nomor-rw-search'] ?? '';
        $nama_kk_search = $params['nama-kk-search'] ?? '';
        $strict_search  = $params['strict-search'] ?? false;
        $strict_search  = $strict_search == 'on' ? true : false;        // strict search
        // posyandu
        $alamat_posyandu = $params['desa'] ?? '';
        $nama_posyandu   = $params['tempat_pemeriksaan'] ?? 0;

        // cari data
        $show_data = new KIAAnakRecords();

        // setup data
        $show_data->sortUsing( $sort )
                ->orderUsing( $order )
                ->limitView( $limit );
        // query data
        $show_data->filterByNama( $main_search )
                ->filterByAlamat($alamat_search )
                ->filterByRt( (int) $no_rt_search )
                ->filterByRw( (int) $no_rw_search )
                ->filterByNamaKK( $nama_kk_search )
                ->filterByAlamatPosyandu( $alamat_posyandu );

        // setup page
        $max_page = $show_data->getMaxPage( $strict_search );
        $page = $page > $max_page ? $max_page : $page;
        $show_data->setCurrentPage( $page );
        
        // return
        return [
            'status'    => 'ok',
            "maks_page" => (int) $max_page,
            "cure_page" => (int) $page,
            "data"      => $show_data->result( $strict_search ),
            'headers'   => ['HTTP/1.1 200 Oke']
        ] ;
    }

}
