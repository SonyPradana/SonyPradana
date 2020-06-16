<?php
    // import modul
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';

    // Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 2);

    // header 
    header_remove("Expires");
    header_remove("Pragma");
    header_remove("X-Powered-By");
    header_remove("Connection");
    header_remove("Server");
    header("Cache-Control:	private");
    header("Content-Type: application/json; charset=utf-8");

    // cek Aunt
    if( !$auth->TrushClient() ){
        // reject jika tidak punya token
        header("HTTP/1.1 401 Unauthorized");
        echo '{"status":"unauthorized"}';
        exit();
    }

    // code utama:

    // ambil parameter dari url
    $sort = isset( $_GET['sort'] ) ? $_GET['sort'] : 'nomor_rm';
    $order = isset( $_GET['order'] ) ? $_GET['order'] : 'ASC';
    $page = isset( $_GET['page'] ) ? $_GET['page'] : 1;
    $max_page = 1;
    $limit = 10;

    // ambil parameter dari url
    $main_search = isset( $_GET['main-search'] ) ? $_GET['main-search'] : '';
    $nomor_rm_search = isset( $_GET['nomor-rm-search']) ? $_GET['nomor-rm-search'] : '';
    $alamat_search = isset( $_GET['alamat-search'] ) ? $_GET['alamat-search'] : '';
    $no_rt_search = isset( $_GET['no-rt-search'] ) ? $_GET['no-rt-search'] : '';
    $no_rw_search = isset( $_GET['no-rw-search'] ) ? $_GET['no-rw-search'] : '';
    $nama_kk_search = isset( $_GET['nama-kk-search'] ) ? $_GET['nama-kk-search'] : '';
    $no_rm_kk_search = isset( $_GET['no-rm-kk-search'] ) ? $_GET['no-rm-kk-search'] : '';
    $strict_search = isset( $_GET['strict-search'] ) ? true : false;

    // cari data
    $show_data = new MedicalRecords();

    // setup data
    $show_data->sortUsing($sort);
    $show_data->orderUsing($order);
    $show_data->limitView($limit);

    // query data
    $show_data->filterByNama( $main_search );
    $show_data->filterByNomorRm( $nomor_rm_search);
    $show_data->filterByAlamat($alamat_search );
    $show_data->filterByRt( $no_rt_search );
    $show_data->filterByRw( $no_rw_search );
    $show_data->filterByNamaKK( $nama_kk_search );
    $show_data->filterByNomorRmKK( $no_rm_kk_search );

    // setup page
    $max_page = $show_data->maxPage();
    $page = $page > $max_page ? $max_page : $page;
    $show_data->currentPage($page);

    // excute query
    $data =  $show_data->result( $strict_search );

    // gabung dengan meta datanya
    $res = [
        "status" => "ok",
        "maks_page" => (int) $max_page,
        "cure_page" => (int) $page,
        "data" => $data
    ];
    
    echo json_encode($res);
    
?>
