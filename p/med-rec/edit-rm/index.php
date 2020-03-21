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
    #ambil id dari url jika tidak ada akes ditolak
    if( isset( $_GET['document_id'])){
        #ambil data rm menggunakn  id
        $id = $_GET['document_id'];
    
        # validasi tombol submit di click
        if( isset( $_POST['submit']) ){
            # validasi form jika ada yg kurang atau salah permintaaan ditolak
    
            # kita anggap semua field form sudah benar
            $new_rm = MedicalRecord::withId($id);
            $new_rm->setNomorRM( $_POST['nomor_rm'] );
            $new_rm->setDataDibuat( time() );
            $new_rm->setNama( $_POST['nama'] );
            $new_rm->setTanggalLahir( $_POST['tgl_lahir'] );
            $new_rm->setAlamat( $_POST['alamat'] );
            $new_rm->setNomorRt( $_POST['nomor_rt'] );
            $new_rm->setNomorRw( $_POST['nomor_rw'] );
            # opsonal
            $new_rm->setNamaKK( $_POST['nama_kk'] );
            $new_rm->setNomorRM_KK( $_POST['nomor_rm_kk'] );
    
            #simpan data
            $simpan = $new_rm->save();
            if( $simpan ){
                $msg = 'berhasil disimpan';
                $_POST = [];
            } else{
                $msg =  'gagal menyimpan';
            }    
        }else{           
            // memuat data dari data base
            $load_rm = MedicalRecord::withId($id);
            // persipan data untuk ditampilkan
            $nomorRM = $load_rm->getNomorRM();
            $nama = $load_rm->getNama();
            $tanggalLahir = $load_rm->getTangalLahir();
            $alamat = $load_rm->getAlamat();
            $nomorRt = $load_rm->getNomorRt();
            $nomorRw = $load_rm->getNomorRw();
            $namaKK = $load_rm->getNamaKK();
            $nomorRM_KK = $load_rm->getNomorRM_KK();
            if( $load_rm->cekAxis() == false){                        
                echo 'acces deny!!!';
                exit;
            }

        }
    }else{
        echo 'acces deny!!!';
        exit;
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
    <?php if( isset( $msg ) ) :?>
        <p style="color: red"><?= $msg ?></p>
        <a href="/">kembali ke menu utama</a>
    <?php else:?>
        <p>buat data rm  baru:</p>
        <form action="" method="post">
            <input type="text" name="nomor_rm" id="input-nomor-rm" placeholder="nomor rekam medis" value="<?= isset($load_rm) ? $nomorRM : '' ?>">
            <input type="text" name="nama" id="input-nama" placeholder="nama" value="<?= isset($load_rm) ? $nama : '' ?>">
            <input type="date" name="tgl_lahir" id="input-tgl-lahir" value="<?= isset($load_rm) ? $tanggalLahir : '' ?>">
            <input type="text" name="alamat" id="input-alamat" placeholder="alamat tanpa rt/rw" value="<?= isset($load_rm) ? $alamat : '' ?>">
            <input type="text" name="nomor_rt" id="input-nomor-rt" placeholder="nomor rt" max="2" value="<?= isset($load_rm) ? $nomorRt : '' ?>">
            <input type="text" name="nomor_rw" id="input-nomor-rw" placeholder="nomor rw" max="2" value="<?= isset($load_rm) ? $nomorRw : '' ?>">
            <p>data pelengkpa (opsonal)</p>
            <input type="text" name="nama_kk" id="input-nama-kk" placeholder="nama kepala keluarga" value="<?= isset($load_rm) ? $namaKK : '' ?>">
            <input type="text" name="nomor_rm_kk" id="input-nomor-rm-kk" placeholder="nomor rm kepla keluarga" value="<?= isset($load_rm) ? $nomorRM_KK : '' ?>">
        
            <button type="submit" name="submit">Buat Rm Baru</button>
        </form>           
    <?php endif; ?>
    </div>
</body>
</html>
