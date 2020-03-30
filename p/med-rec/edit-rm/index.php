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
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Rekam Medis Baru</title>
    <meta name="description" content="sisteminformasi kesehtan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/style-main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.default.css">
    <style>
        .boxs{
            width: 100%;
            height: 100%;
            display: grid;
            grid-template-columns: 1fr 2fr;
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
        <?php $active_menu = 'home' ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/header.html') ?>
    </header>
    <main>
        <div class="container">
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Edit Data Rekam Medis</li>
                </ul>
            </div>
            <div class="boxs">
                <div class="box left"></div>
                <div class="box right">
                <?php if( isset( $msg ) ) :?>
                    <p style="color: red"><?= $msg ?></p>
                    <a href="/">kembali ke menu utama</a>
                <?php else:?>
                    <h1>Edit data Rekam Medis</h1>
                    <form action="" method="post">
                        <input type="text" name="nomor_rm" id="input-nomor-rm" placeholder="nomor rekam medis" value="<?= isset($load_rm) ? $nomorRM : '' ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*">
                        <input type="text" name="nama" id="input-nama" placeholder="nama" value="<?= isset($load_rm) ? $nama : '' ?>" maxlength="50">
                        <input type="date" name="tgl_lahir" id="input-tgl-lahir" value="<?= isset($load_rm) ? $tanggalLahir : '' ?>">
                        <input type="text" name="alamat" id="input-alamat" placeholder="alamat tanpa rt/rw" value="<?= isset($load_rm) ? $alamat : '' ?>">
                        <div class="form-box">
                            <input type="text" name="nomor_rt" id="input-nomor-rt" placeholder="nomor rt" max="2" value="<?= isset($load_rm) ? $nomorRt : '' ?>" inputmode="numeric" pattern="[0-9]*">
                            <input type="text" name="nomor_rw" id="input-nomor-rw" placeholder="nomor rw" max="2" value="<?= isset($load_rm) ? $nomorRw : '' ?>" inputmode="numeric" pattern="[0-9]*">
                        </div>
                        <p>data pelengkpa (opsonal)</p>
                        <input type="text" name="nama_kk" id="input-nama-kk" placeholder="nama kepala keluarga" value="<?= isset($load_rm) ? $namaKK : '' ?>">
                        <input type="text" name="nomor_rm_kk" id="input-nomor-rm-kk" placeholder="nomor rm kepla keluarga" value="<?= isset($load_rm) ? $nomorRM_KK : '' ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*">
                    
                        <button type="submit" name="submit">Buat Rm Baru</button>
                    </form>           
                <?php endif; ?>
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
