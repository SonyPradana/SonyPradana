<?php
    // import modul
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/ajax/json/public/covid-kab-semarang/controller/DataKecamatan.php';

    // default header
    header_remove("Expires");
    header_remove("Pragma");
    header_remove("X-Powered-By");
    header_remove("Connection");
    header_remove("Server");
    header("Cache-Control:	private");
    header("Content-Type: application/json;charset=utf-8");

    $result = [];
    // get param dari url
    // jika ditentukan kecamatan-nya, result hanya kecamatan terpilih
    // jika tidak diditentukan load semua satu kabupaten
    $id = isset( $_GET['kecamatan'] ) ? $_GET['kecamatan'] : null;
    
    // fungsi untuk mengambil data
    $data = new DataKecamatan();
    // list wilayah se kecamatan di kab semarang (array: key & value)
    $dafar = $data->Dafatar_Kecamatan;

    if( $id == null){
        // akumulasi data (se kabupaten)
        $kasus_positif = 0;
        $kasus_sembuh = 0;
        $kasus_meninggal = 0;
        $res = [];
        // me loop semua kecamatan terdaftar
        foreach( $dafar as $key => $value){
            $res[] = $data->getData($key);
            $kasus_positif += $data->positifDirawat();
            $kasus_sembuh += $data->positifSembuh();
            $kasus_meninggal += $data->positifMeninggal();
        }
        // menyun hasil dari data yang telah di konvert
        $result =[
            "kabupaten" => "semarang",
            "kasus_posi" => $kasus_positif,
            "kasus_semb" => $kasus_sembuh,
            "kasus_meni" => $kasus_meninggal,
            "data" => $res
        ];
    }else{ # mengambil hanya data yg di inginkan berdasarkan id
        // redirect jika id tidak terdaftar
        if( !array_key_exists($id, $dafar)){ 
            header("HTTP/1.1 400 Bad Request");
            echo json_encode( $result );
            exit();
        }
        // data berhasil ditemukan
        $result = $data->getData($id);
    }
    // mengembalikan dalam bentuk JSON, dengan satus kode
    header("HTTP/1.1 200 ok");
    echo json_encode( $result );
