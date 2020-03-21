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
$new_auth = new Auth($token, 2);
if( !$new_auth->TrushClient() ){
    header("Location: /p/auth/login");   
    exit();
}
?>
<?php 
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Rekam Medis Baru</title>
    <style>
        div.main{   
            display: block;
                margin: 50px auto;
                width: 500px;
        }
        input{
            display: block;
            margin: 7px 0;
            font-size: 25px;
        }
    </style>
</head>
<body>
    <div class="main">
        <p>buat data rm  baru:</p>
    <?php if( isset( $msg ) ) :?>
        <p style="color: red"><?= $msg ?></p>         
    <?php endif; ?>
        <form action="" method="post">
            <input type="text" name="nomor_rm" id="input-nomor-rm" placeholder="nomor rekam medis" value="<?= $nomor_rm ?>">
            <input type="text" name="nama" id="input-nama" placeholder="nama" value="<?= $nama ?>">
            <input type="date" name="tgl_lahir" id="input-tgl-lahir" value="<?= $tgl_lahir ?>">
            <input type="text" name="alamat" id="input-alamat" placeholder="alamat tanpa rt/rw" value="<?= $alamat ?>">
            <input type="text" name="nomor_rt" id="input-nomor-rt" placeholder="nomor rt" max="2" value="<?= $nomor_rt ?>">
            <input type="text" name="nomor_rw" id="input-nomor-rw" placeholder="nomor rw" max="2" value="<?= $nomor_rw ?>">
            <p>data pelengkpa (opsonal)</p>
            <input type="text" name="nama_kk" id="input-nama-kk" placeholder="nama kepala keluarga" value="<?= $nama_kk ?>">
            <input type="text" name="nomor_rm_kk" id="input-nomor-rm-kk" placeholder="nomor rm kepla keluarga" value="<?= $nomor_rm_kk ?>">

            <button type="submit" name="submit">Buat Rm Baru</button>
        </form>      
    </div>
</body>
</html>
