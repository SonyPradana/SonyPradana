<?php
#import modul 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/auth/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/library/init.php';
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
    $user = new User($auth->getUserName());
    # validasi tombol submit di click

    # property
    $nomor_rm = isset( $_POST['nomor_rm'] ) ? $_POST['nomor_rm'] : '';
    $nama = isset( $_POST['nama'] ) ? $_POST['nama'] : '';
    $tgl_lahir = isset( $_POST['tgl_lahir'] ) ? $_POST['tgl_lahir'] : '';
    $alamat = isset( $_POST['alamat'] ) ? $_POST['alamat'] : '';
    $nomor_rt = isset( $_POST['nomor_rt'] ) ? $_POST['nomor_rt'] : '';
    $nomor_rw = isset( $_POST['nomor_rw'] ) ? $_POST['nomor_rw'] : '';
    $nama_kk = isset( $_POST['nama_kk'] ) ? $_POST['nama_kk'] : '';
    $nomor_rm_kk = isset( $_POST['nomor_rm_kk'] ) ? $_POST['nomor_rm_kk'] : '';


    if( isset( $_POST['submit']) ){
        # validasi form jika ada yg kurang atau salah permintaaan ditolak

        # kita anggap semua field form sudah benar
        $new_rm = new MedicalRecord();
        $new_rm->setNomorRM( $nomor_rm );
        $new_rm->setDataDibuat( time() );
        $new_rm->setNama( $nama );
        $new_rm->setTanggalLahir( $tgl_lahir );
        $new_rm->setAlamat( $alamat );
        $new_rm->setNomorRt( $nomor_rt );
        $new_rm->setNomorRw( $nomor_rw );
        # opsonal
        $new_rm->setNamaKK( $nama_kk );
        $new_rm->setNomorRM_KK( $nomor_rm_kk );

        #simpan data
        $simpan = $new_rm->insertNewOne();
        if( $simpan ){
            $msg = 'berhasil disimpan';
            $_POST = [];
        } else{
            $msg =  'gagal menyimpan';
        }

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Rekam Medis Baru</title>
    <meta name="description" content="sisteminformasi kesehtan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">

    <link rel="stylesheet" href="/lib/css/style-main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.default.css">
    <style>
        .boxs{
            width: 100%;
            height: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .box.right{
            padding: 10px 100px 20px 20px;
        }
        /* mobile */
        @media screen and (max-width: 600px) {            
            .boxs{
                grid-template-columns: 1fr;
            }
            .box.right{
                padding: 5px
            }
        }
    </style>
</head>
<body>
    <header>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/header.html') ?>
    </header>
    <main>
        <div class="container">
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Buat Data Baru</li>
                </ul>
            </div>
            <div class="boxs">
                <div class="box left"></div>
                <div class="box right">
                    <h1>Data Rekam Medis Baru</h1>
                    <?php if( isset( $msg ) ) :?>
                        <p style="color: red"><?= $msg ?></p>         
                    <?php endif; ?>
                        <form action="" method="post">
                            <input type="text" name="nomor_rm" id="input-nomor-rm" placeholder="nomor rekam medis" value="<?= $nomor_rm ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*" >
                            <input type="text" name="nama" id="input-nama" placeholder="nama" value="<?= $nama ?>" maxlength="50">
                            <input type="date" name="tgl_lahir" id="input-tgl-lahir" value="<?= $tgl_lahir ?>">
                            <input type="text" name="alamat" id="input-alamat" placeholder="alamat tanpa rt/rw" value="<?= $alamat ?>">
                            <div class="form-box">
                                <input type="text" name="nomor_rt" id="input-nomor-rt" placeholder="rt" maxlength="2" value="<?= $nomor_rt ?>" inputmode="numeric" pattern="[0-9]*">
                                <input type="text" name="nomor_rw" id="input-nomor-rw" placeholder="rw" maxlength="2" value="<?= $nomor_rw ?>" inputmode="numeric" pattern="[0-9]*">
                            </div>
                            <p>data pelengkpa (opsonal)</p>
                            <input type="text" name="nama_kk" id="input-nama-kk" placeholder="nama kepala keluarga" value="<?= $nama_kk ?>">
                            <input type="text" name="nomor_rm_kk" id="input-nomor-rm-kk" placeholder="nomor rm kepla keluarga" value="<?= $nomor_rm_kk ?>" maxlength="6" maxlength="6" inputmode="numeric" pattern="[0-9]*" >

                            <button type="submit" name="submit">Buat Rm Baru</button>
                        </form>      
                </div>
            </div>
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
