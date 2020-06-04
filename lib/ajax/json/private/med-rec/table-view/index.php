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
    $page = is_numeric($page) ? $page : 1;
    $max_page = 1;
    $limit = 10;
    
    // ambil prameter dari url
    $umur = isset( $_GET['umur'] ) ? $_GET['umur'] : '0-100';
    $desa = isset( $_GET['desa'] ) ? $_GET['desa'] : "bandarjo-branjang-kalisidi-keji-lerep-nyatnyono";
    $status_kk = isset( $_GET['status_kk'] ) ? $_GET['status_kk'] : 'off';

    // ambil semua data bila diminta
    if( isset( $_GET['all'] ) ){
        $data = new View_RM();

        // set-up data
        $data->sortUsing($sort);
        $data->orderUsing($order);
        $data->limitView(25);

        // setup data
        $max_page = $data->maxPage();
        $page = $page > $max_page ? $max_page : $page;
        $data->currentPage($page);

        $res = [
            "status" => "ok",
            "maks_page" => (int) $max_page,
            "cure_page" => (int) $page,
            "data" => $data->resultAll()
        ];

        echo json_encode($res);
        exit();
    }

    // parse range umur
    $min_max = explode("-", $umur);
    if( count( $min_max) < 2){
        // reject sintaks yang tidak valid
        header("HTTP/1.1 400 Bad Request");
        echo '{"status":"bad request"}';
        exit();
    }
    $min =  $min_max[0];
    $max =  $min_max[1];
    if(is_numeric($min) == false || is_numeric($max) == false){
        // reject sintaks yang tidak valid
        header("HTTP/1.1 400 Bad Request");
        echo '{"status":"bad request"}';
        exit();
    }
    $min  = date("Y-m-d", time() - ($min * 31536000) );
    $max  = date("Y-m-d", time() - ($max * 31536000) ); 

    // parse desa / alamat
    $valid_desa = ["bandarjo", "branjang", "kalisidi", "keji", "lerep", "nyatnyono"];
    $arr_desa = explode('-', $desa);
        
    // cari data
    $data = new View_RM();

    // set-up data
    $data->sortUsing($sort);
    $data->orderUsing($order);
    $data->limitView(25);

    // filter range waktu
    $data->filterRangeTanggalLahir($min, $max);
    // filter desa
    for( $i = 0; $i < count($arr_desa); $i++){
        if( in_array($arr_desa[$i], $valid_desa) ){
            $data->filtersAddAlamat($arr_desa[$i]);
        }
    }
    // filter status kk
    if( $status_kk == 'on'){
        $data->filterStatusKK();
    }

    // setup data
    $max_page = $data->maxPage();
    $page = $page > $max_page ? $max_page : $page;
    $data->currentPage($page);
    
    $res = [
        "status" => "ok",
        "staus_kk" => $status_kk,
        "maks_page" => (int) $max_page,
        "cure_page" => (int) $page,
        "data" => $data->results()
    ];
    
    echo json_encode($res);
?>
