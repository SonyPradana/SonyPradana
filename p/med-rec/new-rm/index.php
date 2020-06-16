<?php
#import modul 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
?>
<?php
#Aunt cek
session_start();
$token = $_SESSION['token'] ?? '';
$auth = new Auth($token, 2);
if( !$auth->TrushClient() ){
    header("Location: /p/auth/login");   
    exit();
}
?>
<?php 
    $user = new User($auth->getUserName());
    # validasi tombol submit di click

    // detacting do not track header
    $DNT_Enable = false;
    if( isset( $_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] == 1){
        $DNT_Enable = true;
    }

    # property
    $nomor_rm    = $_POST['nomor_rm'] ?? '';
    $nama        = $_POST['nama'] ?? '';
    $tgl_lahir   = $_POST['tgl_lahir'] ?? '';
    $alamat      = $_POST['alamat'] ?? '';
    $nomor_rt    = $_POST['nomor_rt'] ?? '';
    $nomor_rw    = $_POST['nomor_rw'] ?? '';
    $nama_kk     = $_POST['nama_kk'] ?? '';
    $nomor_rm_kk = $_POST['nomor_rm_kk'] ?? '';

    # ambil nomor rm terakhir
    $data = new MedicalRecords();
    $data->limitView(1);
    $data->sortUsing('nomor_rm');
    $data->orderUsing("DESC");
    $last_nomor_rm = $data->resultAll()[0]['nomor_rm'];

    if( isset( $_POST['submit']) ){
        # validasi form jika ada yg kurang atau salah permintaaan ditolak
        $last_data = $_SESSION['last_data'] ?? [];

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
            $msg = [];
            $msg['message'] = 'Berhasil disimpan';
            $msg['type'] = 'success';
            $_SESSION['last_data'] = $_POST;
            $_POST = [];
            $nomor_rm = $_POST['nomor_rm'] ?? '';
            $nama = $tgl_lahir = $alamat = $nomor_rt = $nomor_rw = $nama_kk = $nomor_rm_kk = null;
        } else{
            $msg = [];
            $msg['message'] = 'Gagal disimpan';
            $msg['type'] = 'danger';
        }

        # merefrresh nomor rm terakhir saad form dikirim
        # ambil nomor rm terakhir
        $data = new MedicalRecords();
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
<?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <style>
        .boxs{
            width: 100%; height: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .box.right { padding: 8px 16px }
        .input-information p,
        .input-information p a,
        p.dusun{
            margin: 0;
            color: #7f6cff;
        }

        form { max-width: 500px }
        form > input:not(:first-child),
        form > button,
        .grub-control.horizontal{
            margin-top: 12px
        }
        form > input { width: 100% }
        .grub-control.horizontal > .textbox{
            width: 100px;
        }

        /* mobile */
        @media screen and (max-width: 600px) {            
            .boxs { grid-template-columns: 1fr }
            .box.right { padding: 5px }
        }
    </style>
    <script>
        let last_nomor_rm = <?= (int) $last_nomor_rm ?>;
    </script>
</head>
<body>
    <header>
        <?php $active_menu = 'buat data'?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.html') ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <div class="container">
        <main>
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
                        <form class="new-rm" action="" method="post">
                            <input class="textbox outline black rounded small block" type="number" name="nomor_rm" id="input-nomor-rm" placeholder="nomor rekam medis" value="<?= $nomor_rm ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*">
                            <div class="input-information"><p>nomor rm terahir : <a href="javascript:void(0)" id="tambah-nomor-rm" tabindex="10"><?= $last_nomor_rm ?></a></p></div>
                            <div class="input-information warning"></div>
                            <input class="textbox outline black rounded small block" type="text" name="nama" id="input-nama" placeholder="nama" value="<?= $nama ?>" maxlength="50" <?= $DNT_Enable ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                            <input class="textbox outline black rounded small block" type="date" name="tgl_lahir" id="input-tgl-lahir" value="<?= $tgl_lahir ?>">
                            <input class="textbox outline black rounded small block" type="text" name="alamat" id="input-alamat" placeholder="alamat tanpa rt/rw" value="<?= $alamat ?>" <?= $DNT_Enable ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                            <div class="grub-control horizontal">
                                <input class="textbox outline black rounded small" type="text" name="nomor_rt" id="input-nomor-rt" placeholder="rt" maxlength="2" value="<?= $nomor_rt ?>" inputmode="numeric" pattern="[0-9]*">
                                <div class="gap-space"><!-- helper --></div>
                                <input class="textbox outline black rounded small" type="text" name="nomor_rw" id="input-nomor-rw" placeholder="rw" maxlength="2" value="<?= $nomor_rw ?>" inputmode="numeric" pattern="[0-9]*">
                                <div class="gap-space"><!-- helper --></div>
                                <p class="dusun"></p>
                            </div>
                            <!-- <p style="margin: 10px 0 5px 0">data pelengkpa (opsonal)</p> -->
                            <div class="grub-control horizontal">
                                <input type="checkbox" name="tandai_sebagai_kk" id="input-mark-as-kk" tabindex="11">
                                <label for="input-mark-as-kk">Tandai sebagai kk</label>
                            </div>                            
                            <input class="textbox outline black rounded small block" type="text" name="nama_kk" id="input-nama-kk" placeholder="nama kepala keluarga" value="<?= $nama_kk ?>" <?= $DNT_Enable ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                            <input class="textbox outline black rounded small block" type="text" name="nomor_rm_kk" id="input-nomor-rm-kk" placeholder="nomor rm kepla keluarga" value="<?= $nomor_rm_kk ?>" maxlength="6" maxlength="6" inputmode="numeric" pattern="[0-9]*" >
                            <div class="input-information no-rm-kk"></div>
                            <div class="input-information kk-sama"></div>

                            <button class="btn rounded small blue outline" type="submit" name="submit">Buat Rm Baru</button>
                        </form>      
                </div>
            </div>
        </main>
    </div>
    <div class="gotop" onclick="gTop()"></div>
    <?php if( isset( $msg ) ) :?>
        <div class="snackbar <?= $msg['type'] ?>">
            <div class="icon">
                <!-- css image -->
            </div>
            <div class="message">
                <?= $msg['message'] ?>
            </div>
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
    window.onscroll = function(){
            stickyHeader('.container', '82px', '32px')
    }
    
    // keep alive
    keepalive(() => {
        window.location.href = "/p/auth/login/?url=<?= $_SERVER['REQUEST_URI'] ?>"
    });
</script>
</html>
