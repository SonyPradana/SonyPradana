<?php
    #import modul 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/auth/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/library/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/simpus/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/config/DbConfig.php';
?>
<?php
    #Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 2);
    if( !$auth->TrushClient() ){
        header("Location: /p/auth/login");   
        exit();
    }
?>
<?php
    $user = new User($auth->getUserName());

    #ambil dari url
    $sort = isset($_GET['sortby']) ? $_GET['sortby'] : 'id';
    #ambi dr session
    $order = isset($_GET['orderby']) ? $_GET['orderby'] : 'ASC';

    # ambil data
    $show_data = new View_RM();
    $show_data->sortUsing($sort);
    $show_data->orderUsing($order);
    $show_data->limitView(25);
    $get_data = $show_data->resultAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liat data rm</title>
    <meta name="description" content="sisteminformasi kesehtan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">

    <link rel="stylesheet" href="/lib/css/style-main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <style>
        button{
            margin: 7px 0;
        }
        .boxs {
            width: 100%;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <header>
        <?php $active_menu = 'lihat data' ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/header.html') ?>
    </header>
    <main>
        <div class="container">
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Lihat Data</li>
                </ul>
            </div>
            <h1>Lihat Data Rekam Medis</h1>
            <button>Costume Filter</button>
        <?php if ( $get_data ): ?>
            <div class="boxs">
                <table>                
                    <tr>
                        <th>No.</th>
                        <th scope="col"><a class="sort-by" href="/p/med-rec/view-rm/?sortby=nomor_rm<?= $order == "ASC" && $sort == 'nomor_rm' ? "&orderby=DESC" : "&orderby=ASC"?>">No RM</a></th>
                        <th scope="col"><a class="sort-by" href="/p/med-rec/view-rm/?sortby=nama<?= $order == "ASC" && $sort == 'nama' ? "&orderby=DESC" : "&orderby=ASC"?>">Nama</a></th>
                        <th scope="col"><a class="sort-by" href="/p/med-rec/view-rm/?sortby=tanggal_lahir<?= $order == "ASC" && $sort == 'tanggal_lahir' ? "&orderby=DESC" : "&orderby=ASC"?>">Tanggal Lahir</a></th>
                        <th scope="col"><a class="sort-by" href="/p/med-rec/view-rm/?sortby=alamat<?= $order == "ASC" && $sort == 'alamat' ? "&orderby=DESC" : "&orderby=ASC"?>">alamat</a></th>
                        <th scope="col"><a class="sort-by" href="/p/med-rec/view-rm/?sortby=nomor_rw<?= $order == "ASC" && $sort == 'nomor_rw' ? "&orderby=DESC" : "&orderby=ASC"?>">RT / RW</a></th>
                        <th scope="col"><a class="sort-by" href="/p/med-rec/view-rm/?sortby=nama_kk<?= $order == "ASC" && $sort == 'nama_kk' ? "&orderby=DESC" : "&orderby=ASC"?>">Nama KK</a></th>
                        <th scope="col"><a class="sort-by" href="/p/med-rec/view-rm/?sortby=nomor_rm_kk<?= $order == "ASC" && $sort == 'nomor_rm_kk' ? "&orderby=DESC" : "&orderby=ASC"?>">No. Rm KK</a></th>
                        <th><a href="#">Action</a></th>
                    </tr>                         
                <?php $idnum = (int) 1; ?>
                <?php foreach( $get_data as $data) :?>            
                    <tr>       
                        <th><?= $idnum ?></th>
                        <th><?= $data['nomor_rm']?></th>
                        <th><?= $data['nama']?></th>
                        <th><?= $data['tanggal_lahir']?></th>
                        <th><?= $data['alamat']?></th>
                        <th><?= $data['nomor_rt'] . ' / ' . $data['nomor_rw']?></th>
                        <th><?= $data['nama_kk']?></th>
                        <th><?= $data['nomor_rm_kk']?></th>
                        <th><a class="link" href="/p/med-rec/edit-rm/index.php?document_id=<?= $data['id']?>">edit</a></th>
                    </tr>                       
                    <?php $idnum++; ?>
                <?php endforeach ; ?>
                </table>
            </div>
        <?php else : ?>
            <p>gagal memuat data</p>
        <?php endif; ?>
        </div>
    </main>
    <footer>
        <div class="line"></div>
        <p class="big-footer">SIMPUS LEREP</p>
        <p class="note-footer">creat by <a href="https://twitter.com/AnggerMPd">amp</a></p>
        <div class="box"></div>
    </footer>
</body>
</html>
