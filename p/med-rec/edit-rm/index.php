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
                $msg = 'berhasil disimpan';
                $_POST = [];$nomor_rm = isset( $_POST['nomor_rm'] ) ? $_POST['nomor_rm'] : '';
                $_POST = [];
            } else{
                $msg =  'gagal menyimpan';
            }    
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
            $cari_rm = new View_RM();
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
<?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.default.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
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
</head>
<body>
    <header>
        <?php $active_menu = 'home' ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.html') ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/alert.html') ?>
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
                        <div class="input-information">
                        <?php if( $status_double ) : ?>
                            <p>nomor rekam medis sama :
                                <a href="/p/med-rec/search-rm/?nomor-rm-search=<?= $nomorRM ?>"
                                    target="_blank">lihat</a>
                            </p>
                        <?php endif; ?>
                        </div>
                        <input type="text" name="nama" id="input-nama" placeholder="nama" value="<?= isset($load_rm) ? $nama : '' ?>" maxlength="50">
                        <input type="date" name="tgl_lahir" id="input-tgl-lahir" value="<?= isset($load_rm) ? $tanggalLahir : '' ?>">
                        <input type="text" name="alamat" id="input-alamat" placeholder="alamat tanpa rt/rw" value="<?= isset($load_rm) ? $alamat : '' ?>">
                        <div class="form-box">
                            <input type="text" name="nomor_rt" id="input-nomor-rt" placeholder="nomor rt" max="2" value="<?= isset($load_rm) ? $nomorRt : '' ?>" inputmode="numeric" pattern="[0-9]*">
                            <input type="text" name="nomor_rw" id="input-nomor-rw" placeholder="nomor rw" max="2" value="<?= isset($load_rm) ? $nomorRw : '' ?>" inputmode="numeric" pattern="[0-9]*">
                        </div>
                        <div class="form-box bottom">
                            <input type="checkbox" name="tandai_sebagai_kk" id="input-mark-as-kk" tabindex="11" <?= $status_kk == true ? "checked" : ""?>>
                            <label for="input-mark-as-kk">Tandai sebagai kk</label>
                        </div>  
                        <input type="text" name="nama_kk" id="input-nama-kk" placeholder="nama kepala keluarga" value="<?= isset($load_rm) ? $namaKK : '' ?>">
                        <input type="text" name="nomor_rm_kk" id="input-nomor-rm-kk" placeholder="nomor rm kepla keluarga" value="<?= isset($load_rm) ? $nomorRM_KK : '' ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*">
                        <div class="input-information no-rm-kk"></div>
                        <div class="input-information kk-sama"></div>
                    
                        <button type="submit" name="submit">Edit Data RM</button>
                        <button type="button" onclick="window.history.back()">Batal Perubahan</button>
                    </form>           
                <?php endif; ?>
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
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/footer.html') ?>
    </footer>
    <script>
        //DOM property
        var tambah_no_rm = document.querySelector('#tambah-nomor-rm')
        var tandai_sbg_kk = document.querySelector('#input-mark-as-kk');
        var cari_no_RmKk = document.querySelector('#input-nama-kk');
        var cari_no_Rm = document.querySelector('#input-nomor-rm');        
        
        // cari nomor rm kk jika ada
        cari_no_Rm.addEventListener('input', (event) => {
            if( cari_no_Rm.value == '')  return;
            xhr_cek_rm_terdaftar();
        });
        
        // cari nomor rm kk jika ada
        cari_no_RmKk.addEventListener('input', (event) => {
            xhr_cek_rm_kk();
        });

        // function
        function xhr_cek_rm_kk() {
            var sendAjax = new XMLHttpRequest();
            let nm = document.querySelector("#input-nama").value;
            let n = document.querySelector("#input-nama-kk").value;
            let a = document.querySelector("#input-alamat").value;
            let r = document.querySelector("#input-nomor-rt").value;
            let w = document.querySelector("#input-nomor-rw").value;

            sendAjax.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {            
                    //berhasil dipanggil
                    let info_rm_kk = document.querySelector('.input-information.no-rm-kk');
                    var para2 = document.createElement('p');
                    var alink2 = document.createElement('a');
                    info_rm_kk.textContent = '';
                    if( this.response != ''){
                        para2.innerHTML = 'nomor rm kk : ';
                        alink2.href = 'javascript:void(0)';
                        alink2.id = 'tambah-nomor-rm-kk';
                        alink2.tabIndex = 12;
                        alink2.innerHTML = this.response;
                        para2.appendChild(alink2);
                        info_rm_kk.appendChild(para2);
                        alink2.addEventListener('click', (event) => {
                            var input_no_rm_kk = document.querySelector("#input-nomor-rm-kk");
                            input_no_rm_kk.value = this.responseText;
                        })
                    }
                    // nama kk yang sama
                    let info_kk = document.querySelector('.input-information.kk-sama');
                    var para = document.createElement('p');
                    var alink = document.createElement('a');
                    info_kk.textContent = '';
                    if( this.response != '' && n == nm ){
                        para.innerHTML = 'nama kk indentik : '
                        alink.href ='/p/med-rec/search-rm/?strict-search=on&alamat-search='+ a +'&no-rt-search=' + r + '&no-rw-search=' + w + '&nama-kk-search=' + n;
                        alink.innerHTML = 'lihat'
                        alink.target = '_blank';
                        para.appendChild(alink);
                        info_kk.appendChild(para);
                    }
                }
            }
            sendAjax.open('GET', "/lib/ajax/inner-text/cari-nomor-rm-kk.php?n="+ n + "&a=" + a + "&r=" + r + "&w=" + w, true);
            sendAjax.send();
        }

        function xhr_cek_rm_terdaftar(params) {
            var sendAjax = new XMLHttpRequest();
            sendAjax.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {            
                    //berhasil dipanggil
                    let res = document.querySelector('.input-information');
                    var para = document.createElement("p");
                    var alink = document.createElement('a');
                    res.textContent = '';
                    para.innerHTML = 'nomor rekam medis sama : ';
                    alink.href = '/p/med-rec/search-rm/?nomor-rm-search=' + cari_no_Rm.value;
                    alink.innerHTML = 'lihat'
                    alink.target = '_blank';
                    para.appendChild(alink);
                    if( this.responseText > 0){
                        res.appendChild(para);
                    }
                }
            }
            let nr = cari_no_Rm.value;
            sendAjax.open('GET', "/lib/ajax/inner-text/cek-nomor-rm.php?nr="+ nr, true);
            sendAjax.send();
        }
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
    <script src="/lib/js/index.end.js"></script>
</body>
</html>
