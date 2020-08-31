<?php
    use Simpus\Helper\HttpHeader;
    use Simpus\Auth\Auth;
    use Simpus\Simpus\KIAAnakRecords;
    // import modul
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';

    // Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 2);

    // header 
    HttpHeader::standartJsonHeader(true);

    // cek Aunt
    if( !$auth->TrushClient() ) HttpHeader::printJson([], 401);

    // code utama:

    // ambil parameter dari url
    $sort       = $_GET['sort'] ?? 'tanggal_dibuat';
    $order      = $_GET['order'] ?? 'ASC';
    $page       = $_GET['page'] ?? 1;
    $max_page   = 1;
    $limit      = 10;

    // ambil parameter dari url
    $main_search    = $_GET['main-search'] ?? '';
    $alamat_search  = $_GET['alamat-search'] ?? '';
    $no_rt_search   = $_GET['nomor-rt-search'] ?? '';
    $no_rw_search   = $_GET['nomor-rw-search'] ?? '';
    $nama_kk_search = $_GET['nama-kk-search'] ?? '';
    $strict_search  = $_GET['strict-search'] ?? false;
    $strict_search  = $strict_search == 'on' ? true : false;        // strict search
    // posyandu
    $alamat_posyandu = $_GET['desa'] ?? '';
    $nama_posyandu  = $_GET['tempat_pemeriksaan'] ?? 0;

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

    //  result
    HttpHeader::printJson([
        "maks_page" => (int) $max_page,
        "cure_page" => (int) $page,
        "data" => $show_data->result( $strict_search )
    ], 200);



