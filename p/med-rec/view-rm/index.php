<?php
    #import modul 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/auth/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/simpus/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/db/db_crud/DbConfig.php';
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
    # ambil data
    $show_data = new View_RM();
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
    <style>
        input{
            display: block;
            margin: 7px 0
        }
        #input-main-search{
            display: inline
        }
        table {
            margin-top: 5px;
            padding: 5px;
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>
<body>
    <header>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/header.html') ?>
    </header>
    <div class="main">
        <p>liat data rekam medis</p>
    <?php if ( $get_data ): ?>
        <table>
            <tr>
                <th>No.</th>
                <th>No RM</th>
                <th>Nama</th>
                <th>Tanggal Lahir</th>
                <th>Almat</th>
                <th>RT / RW</th>
                <th>Nama KK</th>
                <th>No. Rm KK</th>
                <th>Action</th>
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
                <th><a href="/p/med-rec/edit-rm/index.php?document_id=<?= $data['id']?>">edit</a></th>
            </tr>                       
            <?php $idnum++; ?>
        <?php endforeach ; ?>
        </table>
    <?php else : ?>
        <p>gagal memuat data</p>
    <?php endif; ?>
    </div>
</body>
</html>
