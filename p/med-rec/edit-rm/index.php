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
    header("Location: /login?url=" . $_SERVER['REQUEST_URI']); 
    exit();
}
?>
<?php 
    $user = new User($auth->getUserName());
   
    // detacting do not track header
    $DNT_Enable = false;
    if( isset( $_SERVER['HTTP_DNT']) && $_SERVER['HTTP_DNT'] == 1){
        $DNT_Enable = true;
    }

    # ambil id dari url jika tidak ada akes ditolak
    if( isset( $_GET['document_id'])){
        # ambil data rm menggunakn  id
        $id = $_GET['document_id'];
        # default property
        $status_kk = false;
        $status_double = false;    
        # validasi tombol submit di click
        if( isset( $_POST['submit']) ){ # untuk menggirim data
            # validasi form jika ada yg kurang atau salah permintaaan ditolak
            $last_data = isset( $_SESSION['last_data']) ? $_SESSION['last_data'] : [];
    
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
            if( $simpan && $last_data != $_POST){
                $msg = [];
                $msg['message'] = 'Berhasil disimpan';
                $msg['type'] = 'success';
                $_POST = [];$nomor_rm = isset( $_POST['nomor_rm'] ) ? $_POST['nomor_rm'] : '';
                $_POST = [];
            } else{
                $msg = [];
                $msg['message'] = 'Gagal disimpan';
                $msg['type'] = 'danger';
            }    
            
            // user log
            $log = new Log( $auth->getUserName() );
            $log->set_event_type('med-rec');
            $log->save( $new_rm->getLastQuery() );

        }else{ # untuk menangkap data        
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
            # cek status kk
            if( $nama === $namaKK){
                $status_kk = true;
            }
            # cari rm yang sama
            $cari_rm = new MedicalRecords();
            $cari_rm->filterByNomorRm($nomorRM);
            $cari_rm->forceLimitView(2);
            if( $cari_rm->maxData() > 1){
                $status_double = true;
            }
            # cek data rm terdaftar atau tidak
            if( $load_rm->cekAxis() == false){                        
                echo 'acces deny!!!';
                header('HTTP/1.1 403 Forbidden');
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
    <title>Edit Rekam Medis Baru</title>
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
</head>
<body>
    <header>
        <?php 
            $active_menu = null;
            $menu_link = [["Lihat RM", "/p/med-rec/view-rm/"], ["Cari RM", "/p/med-rec/search-rm/"], ["Buat RM", "/p/med-rec/new-rm/"] ];
            include(BASEURL . '/lib/components/header/header.php')
        ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="/p/med-rec/">Rekam Medis</a></li>
                    <li>Edit Data Rekam Medis</li>
                </ul>
            </div>
            <div class="boxs">
                <div class="box left"></div>
                <div class="box right">
                <?php if( isset( $msg ) ) :?>
                    <p style="color: red"><?= $msg['message'] ?></p>
                    <a href="/">kembali ke menu utama</a>
                <?php else:?>
                    <h1>Edit data Rekam Medis</h1>
                    <form action="" method="post">
                        <input class="textbox outline black rounded small block" type="text" name="nomor_rm" id="input-nomor-rm" placeholder="nomor rekam medis" value="<?= isset($load_rm) ? $nomorRM : '' ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*">                        
                        <div class="input-information warning">
                        <?php if( $status_double ) : ?>
                            <p>nomor rekam medis sama :
                                <a href="/p/med-rec/search-rm/?nomor-rm-search=<?= $nomorRM ?>"
                                    target="_blank">lihat</a>
                            </p>
                        <?php endif; ?>
                        </div>
                        <input class="textbox outline black rounded small block" type="text" name="nama" id="input-nama" placeholder="nama" value="<?= isset($load_rm) ? $nama : '' ?>" maxlength="50" <?= $DNT_Enable ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <input class="textbox outline black rounded small block" type="date" name="tgl_lahir" id="input-tgl-lahir" value="<?= isset($load_rm) ? $tanggalLahir : '' ?>">
                        <input class="textbox outline black rounded small block" type="text" name="alamat" id="input-alamat" placeholder="alamat tanpa rt/rw" value="<?= isset($load_rm) ? $alamat : '' ?>" <?= $DNT_Enable ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <div class="grub-control horizontal">
                            <input class="textbox outline black rounded small" type="text" name="nomor_rt" id="input-nomor-rt" placeholder="nomor rt" max="2" value="<?= isset($load_rm) ? $nomorRt : '' ?>" inputmode="numeric" pattern="[0-9]*">
                            <div class="gap-space"><!-- helper --></div>
                            <input class="textbox outline black rounded small" type="text" name="nomor_rw" id="input-nomor-rw" placeholder="nomor rw" max="2" value="<?= isset($load_rm) ? $nomorRw : '' ?>" inputmode="numeric" pattern="[0-9]*">
                            <div class="gap-space"><!-- helper --></div>
                            <p class="dusun"></p>
                        </div>
                        <div class="grub-control horizontal">
                            <input type="checkbox" name="tandai_sebagai_kk" id="input-mark-as-kk" tabindex="11" <?= $status_kk == true ? "checked" : ""?>>
                            <label for="input-mark-as-kk">Tandai sebagai kk</label>
                        </div>  
                        <input class="textbox outline black rounded small block" type="text" name="nama_kk" id="input-nama-kk" placeholder="nama kepala keluarga" value="<?= isset($load_rm) ? $namaKK : '' ?>" <?= $DNT_Enable ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <input class="textbox outline black rounded small block" type="text" name="nomor_rm_kk" id="input-nomor-rm-kk" placeholder="nomor rm kepla keluarga" value="<?= isset($load_rm) ? $nomorRM_KK : '' ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*">
                        <div class="input-information no-rm-kk"></div>
                        <div class="input-information kk-sama"></div>
                    
                        <div class="grub-control horizontal">
                            <button class="btn rounded small blue outline" type="submit" name="submit">Edit Data RM</button>
                            <div class="gap-space"><!-- helper --></div>
                            <button class="btn rounded small red text" type="button" onclick="window.history.back()">Batal Perubahan</button>
                        </div>
                    </form>           
                <?php endif; ?>
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
        // onload
        window.addEventListener('load', () => {
            cekDesa()
        })
        
        // sticky header
        window.onscroll = function(){
                stickyHeader('.container', '82px', '32px')
        }
        
        // keep alive
        keepalive(
            () => {
                // ok function : redirect logout and then redirect to login page to accses this page
                window.location.href = "/login?url=<?= $_SERVER['REQUEST_URI'] ?>&logout=true"
            },
            () => {          
                // close fuction : just logout
                window.location.href = "/logout?url=<?= $_SERVER['REQUEST_URI'] ?>"
            }
        );
</script>
</html>
