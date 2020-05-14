<?php
#import modul 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
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

    # ambil nomor rm terakhir
    $data = new View_RM();
    $data->limitView(1);
    $data->sortUsing('nomor_rm');
    $data->orderUsing("DESC");
    $last_nomor_rm = $data->resultAll()[0]['nomor_rm'];

    if( isset( $_POST['submit']) ){
        # validasi form jika ada yg kurang atau salah permintaaan ditolak
        $last_data = isset( $_SESSION['last_data']) ? $_SESSION['last_data'] : [];

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
        if( $simpan && $last_data != $_POST){
            $msg = 'berhasil disimpan';
            $_SESSION['last_data'] = $_POST;
            $_POST = [];$nomor_rm = isset( $_POST['nomor_rm'] ) ? $_POST['nomor_rm'] : '';
            $nama = $tgl_lahir = $alamat = $nomor_rt = $nomor_rw = $nama_kk = $nomor_rm_kk = null;
        } else{
            $msg =  'gagal menyimpan';
        }

        # merefrresh nomor rm terakhir saad form dikirim
        # ambil nomor rm terakhir
        $data = new View_RM();
        $data->forceLimitView(1);
        $data->sortUsing('nomor_rm');
        $data->orderUsing("DESC");
        $last_nomor_rm = $data->resultAll()[0]['nomor_rm'];

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
    <meta name="description" content="Sistem Informasi Manajemen Puskesmas SIMPUS Lerep">
    <meta name="keywords" content="simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.default.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
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
        .input-information p,
        .input-information p a{
            margin: 0;
            color: #7f6cff;
        }
        button{
            margin-top: 15px;
            padding: 10px 6px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type=checkbox]{
            margin: 0 !important;
            width: 0 !important;
        }
        .form-box.bottom{
            margin-top: 10px !important;
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
    <script>
        let last_nomor_rm = <?= $last_nomor_rm ?>;
    </script>
</head>
<body>
    <header>
        <?php $active_menu = 'buat data'?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.html') ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/alert.html') ?>
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
                            <input type="number" name="nomor_rm" id="input-nomor-rm" placeholder="nomor rekam medis" value="<?= $nomor_rm ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*">
                            <div class="input-information"><p>nomor rm terahir : <a href="javascript:void(0)" id="tambah-nomor-rm" tabindex="10"><?= $last_nomor_rm ?></a></p></div>
                            <div class="input-information warning"></div>
                            <input type="text" name="nama" id="input-nama" placeholder="nama" value="<?= $nama ?>" maxlength="50">
                            <input type="date" name="tgl_lahir" id="input-tgl-lahir" value="<?= $tgl_lahir ?>">
                            <input type="text" name="alamat" id="input-alamat" placeholder="alamat tanpa rt/rw" value="<?= $alamat ?>">
                            <div class="form-box">
                                <input type="text" name="nomor_rt" id="input-nomor-rt" placeholder="rt" maxlength="2" value="<?= $nomor_rt ?>" inputmode="numeric" pattern="[0-9]*">
                                <input type="text" name="nomor_rw" id="input-nomor-rw" placeholder="rw" maxlength="2" value="<?= $nomor_rw ?>" inputmode="numeric" pattern="[0-9]*">
                            </div>
                            <!-- <p style="margin: 10px 0 5px 0">data pelengkpa (opsonal)</p> -->
                            <div class="form-box bottom">
                                <input type="checkbox" name="tandai_sebagai_kk" id="input-mark-as-kk" tabindex="11">
                                <label for="input-mark-as-kk">Tandai sebagai kk</label>
                            </div>                            
                            <input type="text" name="nama_kk" id="input-nama-kk" placeholder="nama kepala keluarga" value="<?= $nama_kk ?>">
                            <input type="text" name="nomor_rm_kk" id="input-nomor-rm-kk" placeholder="nomor rm kepla keluarga" value="<?= $nomor_rm_kk ?>" maxlength="6" maxlength="6" inputmode="numeric" pattern="[0-9]*" >
                            <div class="input-information no-rm-kk"></div>
                            <div class="input-information kk-sama"></div>

                            <button type="submit" name="submit">Buat Rm Baru</button>
                        </form>      
                </div>
            </div>
        </div>
    </main>
    <div class="gotop" onclick="gTop()"></div>
    <?php if( isset( $msg ) ) :?>
        <div class="snackbar">
            <?= $msg ?>
        </div>
    <?php endif; ?>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>
</body>
<script src="/lib/js/controller/form-rm/index.js"></script>
<script src="/lib/js/index.end.js"></script>
<script>
    
    // sticky header
    window.onscroll = function(){stickyHeader('82px')};
    var mycontent = document.querySelector('main');
    
    // keep alive    
    var dom_alert = document.querySelector('.modal.alert');
    
    keepalive(dom_alert);
    function redirect_login(){
        window.location.href = "/";
    }
</script>
</html>
