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

$user = new User($auth->getUserName());
?>
<?php
    function deny($msg = ''){
        header('HTTP/1.0 405 Method Not Allowed');
        echo '<h1>Acces deny!!!</h1>';
        echo '<p>' . $msg . '</p>';
        exit();
    }

    $success   = false;
    $hash_id = $_GET['document_id'] ?? false;
    if( $hash_id == false ){ 
        deny();
    }

    // ambil data lama
    $data_rm = new MedicalRecord();                             // data rm
    if($data_rm->refreshUsingIdHash($hash_id) == false) deny('document tidak ditemukan');

    $data_kia = new KIAAnakRecord();                            // data kia
    $data_kia->loadWithID( CCode::ConvertToCode( $hash_id ) );
    if( $data_kia->cekExist() == false ) deny('documnet tidak terdaftar');    

    // update data
    if( isset( $_POST['request'] ) ){
        $undo_change = array_merge(
            $data_rm->getData(),
            $data_kia->convertToArray()
        );
        // simpan data kia
        $data_kia->convertFromArray($_POST);
        $success = $data_kia->update();

        // simpdan data rm
        $data_rm->convertFromArray($_POST);
        $status           = (array) json_decode( $data_rm->getStatus() );
        $status["update"] = time();
        $data_rm->setStatus( json_encode($status) );              

        if( $success ){
            // switch data disimpan dimana                      TODO cek asal table, langsung dr database-nya menggunakn id
            if( $data_rm->getNomorRM() == ''){
                $success = $data_rm->save('staging_rm');
            }else{
                $success = $data_rm->save();
            }
        }
        if( !$success ){
            // mengembalikan data apabila data tidak berhasil disimpan 
            $data_kia->convertFromArray($undo_change);
            $data_kia->update();                                // [database-kia]
            $data_rm->convertFromArray($undo_change);           // [user input]
        }
    }

    // isi input dengan data
    $nama    = $data_rm->getNama();
    $tgl     = $data_rm->getTangalLahir();
    $alamat  = $data_rm->getAlamat();
    $no_rt   = $data_rm->getNomorRt();
    $no_rw   = $data_rm->getNomorRw();
    $nama_kk = $data_rm->getNamaKK();

    $jk      = $data_kia->getJenisKelamin();
    $bbl     = $data_kia->getBeratBayiLahir();
    $pbl     = $data_kia->getPanjangBayiLahir();
    $kia     = $data_kia->getKIA();
    $imt     = $data_kia->getIndeksMasaTubuh();
    $asi     = $data_kia->getAsiEks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data KIA Anak</title>
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
        .boxs-container{
            /* width: 100%; height: 100%; */
            display: grid;
            grid-template-columns: 1fr 24px 1fr;
        }
        .box.right { padding: 8px 16px }

        /* mobile */
        @media screen and (max-width: 740px) {            
            .boxs-container { grid-template-columns: 1fr }
            .box.right { padding: 5px }
        }
        /* tab */
        .tab{
            display: none;
        }

        /* form design */
        section{
            display: flex;
            flex-direction: column;
            margin: 8px 0px;
        }
        section label{
            margin-bottom: 4px;
        }
        .btn-kunjungan-box{
            display: flex;
            justify-content: center;
        }
        .btn-kunjungan-box button{margin: 0 8px}
        .grub-control.horizontal .textbox{
            width: 112px;
        }
        .navigation-box{
            margin-top: 24px;
            display: flex;
            justify-content: right;
        }
        /* costume */
        #input-cari-rm{ min-width: 230px;}

        /* tab */
        .tab {
            /* display: none; */
            outline: 1px dashed pink;
        }
    </style>
</head>
<body>
    <header>
        <?php 
            $active_menu = "none";
            $menu_link = [["Lihat Data KIA", "/p/kia-anak/biodata/view/"], ["Cari Data KIA", "/p/kia-anak/biodata/search/"], ["Buat Data KIA", "/p/kia-anak/biodata/new/"] ];
            include(BASEURL . '/lib/components/header/header.php')
        ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="/p/med-rec/">KIA Anak</a></li>
                    <li>Edit Data</li>
                </ul>
            </div>
            <div class="boxs-container">
                <div class="box left">
                </div>
                <div class="gap"></div>
                <div class="box right">
                    <h1>Registrasi Posyandu </h1>
                    <div class="form-step">
                        <form class="form-rm" method="POST" action="">
                            <div class="tab pertama">
                                <h2>Identitas Balita</h2>
                                <!-- nama rm -->
                                <section>
                                    <label for="input-nama-rm">Nama RM</label>
                                    <input class="textbox outline black rounded small" type="text" id="input-nama-rm" name="nama" placeholder="Nama Balita" value="<?= $nama ?>">
                                </section>
                                <!-- tanggal lahir -->
                                <section>
                                    <label for="input-tanggal-lahir">Tangal Lahir</label>
                                    <input class="textbox outline black rounded small" type="date" id="input-tanggal-lahir" name="tanggal_lahir" value="<?= $tgl ?>">
                                </section>
                                <!-- alamat -->
                                <section>
                                    <label for="input-alamat">Alamat</label>
                                    <input class="textbox outline black rounded small" type="text" id="input-alamat" name="alamat" placeholder="Almat Desa" value="<?= $alamat ?>">
                                </section>
                                <div class="grub-control horizontal">
                                    <!-- nomor rt -->
                                    <section>
                                        <label for="input-nomor-rt">RT</label>
                                        <input class="textbox outline black rounded small" type="number" id="input-nomor-rt" name="nomor_rt" placeholder="Nomor Rt" value="<?= $no_rt ?>">
                                    </section>
                                    <div class="gap-space">
                                        <!-- gab -->
                                    </div>
                                    <!-- nomor rw -->
                                    <section>
                                        <label for="input-nomor-rw">RW</label>
                                        <input class="textbox outline black rounded small" type="number" id="input-nomor-rw" name="nomor_rw" placeholder="Nomor Rw" value="<?= $no_rw ?>">
                                    </section>
                                </div>
                                <!-- nama rm kk-->
                                <section>
                                    <label for="input-nama-kk">Nama Orang Tua</label>
                                    <input class="textbox outline black rounded small" type="text" id="input-nama-kk" name="nama_kk" placeholder="Nama Ibu / Ayah"  value="<?= $nama_kk ?>">
                                </section>
                                <div class="navigation-box btn-kunjungan-box">
                                    <button id="btn-next-biodata" class="btn rounded small blue fill" type="button" id="btn-verifikasi">Next</button>
                                </div>
                            </div>
                            <div class="tab kedua">
                                <h2>Biodata Balita</h2>
                                <!-- jenis kelamin -->
                                <section>
                                    <label for="input-jenis-kelamin">Jenis Kelamin</label>
                                    <select class="textbox outline black rounded small" id="input-jenis-kelamin" name="jenis_kelamin">
                                        <option value="none" selected disabled hidden>Jenis Kelamin</option> 
                                        <option value="0" <?= $jk == 0 ? 'selected' : null ?>>Perempuan</option>
                                        <option value="1" <?= $jk == 1 ? 'selected' : null ?>>Laki-laki</option>
                                    </select>
                                </section>
                                <!-- bbl -->
                                <section>
                                    <label for="input-bbl">Berat Bayi Lahir</label>
                                    <input class="textbox outline black rounded small" type="number" id="input-bbl" name="bbl"  value="<?= $bbl ?>">
                                </section>
                                <!-- pbl -->
                                <section>
                                    <label for="input-pbl">Panjang Bayi Lahir</label>
                                    <input class="textbox outline black rounded small" type="number" id="input-pbl" name="pbl"  value="<?= $pbl ?>">
                                </section>
                                <!-- kia -->
                                <section>
                                    <label for="input-kia">KIA</label>
                                    <input class="textbox outline black rounded small" type="number" id="input-kia" name="kia" value="<?= $kia ?>">
                                </section>
                                <!-- imd -->
                                <section>
                                    <label for="input-imd">Indeks Masa </label>
                                    <input class="textbox outline black rounded small" type="number" id="input-imd" name="imd" value="<?= $imt ?>">
                                </section>
                                <!-- asi eks -->
                                <div>
                                    <input class="textbox outline black rounded small" type="checkbox" id="input-asi" name="asi_eks" checked="<?= $asi == 1 ? 'checked' : null ?>">
                                    <label for="input-asi">Asi Ekslusif</label>
                                </div>
                                <div class="navigation-box btn-kunjungan-box">
                                    <button id="btn-back-datarm" class="btn rounded small blue fill" type="button" id="btn-verifikasi">Kembali</button>
                                    <button id="btn-request" class="btn rounded small green fill" type="submit" name="request">Edit</button>
                                </div>
                            </div>
                            <div class="tab ketiga">
                                <h2>Selesai</h2>
                                <p>Data Berhasil diedit</p>
                            </div>
                        </form>
                    </div>                        
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
<script src="/lib/js/index.end.js"></script>
<script>
    // property data rm
    var dataRm = new Array();
    var dom_inputTarget = $id('input-target');
    var dom_inputTime   = $id('input-timestamp');
    // dom form 1 - tab 1
    var dom_nama    = $id('input-nama-rm');
    var dom_tgl     = $id('input-tanggal-lahir');
    var dom_alamat  = $id('input-alamat');
    var dom_rt      = $id('input-nomor-rt');
    var dom_rw      = $id('input-nomor-rw');
    var dom_nama_kk = $id('input-nama-kk');
    // property tab
    showTab(<?= $success ? 2 : 0 ?>);
    var valids = [false, false] // cari, data-rm, biodata
    
    function showTab(n){
        // This function will display the specified tab of the form...
        var x = document.getElementsByClassName("tab");
        x[n].style.display = "block";
    }
    function hideTab(n){
        var x = document.getElementsByClassName("tab");
        x[n].style.display = "none";
    }
    
    // form dan button
    // tab: 1 (button)
    $id('btn-next-biodata').addEventListener('click', function(){
        hideTab(0)
        showTab(1)
    })
    // tab: 2 (button)    
    $id('btn-back-datarm').addEventListener('click', function(){
        hideTab(1)
        showTab(0)
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
