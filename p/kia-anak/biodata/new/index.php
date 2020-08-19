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
    $success = false;
    // simpan data
    if( isset( $_POST['request'] ) ){
        // default
        $id_hash = time();
        $success_stagingRM = false;
        $success_dataKIA = false;

        if( $_POST['target']  == 'staging' ){
            // request data rm baru
            $stagingRM = new MedicalRecord();
            $stagingRM->setNama( $_POST['nama_rm'] ?? 'bayi nyonya Y' );
            $stagingRM->setTanggalLahir( $_POST['tanggal_lahir'] ?? date("M/d/Y", time()) );
            $stagingRM->setAlamat( $_POST['alamat'] ?? '');
            $stagingRM->setNomorRt( $_POST['nomor_rt'] ?? 0 );
            $stagingRM->setNomorRw( $_POST['nomor_rw'] ?? 0 );
            $stagingRM->setNamaKK( $_POST['nama_kk'] ?? 'tuan x');
            $stagingRM->setStatus('{"grub":["posyandu", "kia-anak"]}');
            $stagingRM->setDataDibuat( $id_hash );
            // simpan data ke staging data rm
            $success_stagingRM = $stagingRM->insertNewOne('', 'staging_rm');

            // var_dump($stagingRM->getData());
        }elseif( $_POST['target'] == 'data_rm' && isset( $_POST['timestamp'] )){
            echo 'work!!!';
            $success_stagingRM = true;
            $id_hash = $_POST['timestamp'];
        }

        // cek data rm ada atau tidak
        if( $success_stagingRM ){
            // request data posyandu baru
            $dataKIA = new KIAAnakRecord();
            $dataKIA->convertFromArray( $_POST );
            // table relation
            $table_relation = new Relation(CCode::ConvertToCode( $id_hash ), $id_hash);
            // simpan data biodata kia
            $success_dataKIA = $dataKIA->creat( CCode::ConvertToCode( $id_hash ) );

            if( $success_dataKIA && $table_relation->creat() ){
                $success = true;
                $msg = [];
                $msg['type'] = 'info';
                $msg['message'] = 'berhasil disimpan';
            }
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
    <title>Buat Biodat Anak Baru</title>
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
            $menu_link = MENU_KIA_ANAK;
            $active_menu = "Buat Data KIA";
        ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.php') ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="/rekam-medis">KIA Anak</a></li>
                    <li>Buat Data Baru</li>
                </ul>
            </div>
            <div class="boxs-container">
                <div class="box left">
                </div>
                <div class="gap"></div>
                <div class="box right">
                    <h1>Registrasi Posyandu </h1>
                    <div class="form-step">
                        <form class="form-rm" method="POST">
                            <div class="tab pertama">
                                <h2>Status Kunjungan</h2>
                                <div class="status-kunjungan">
                                    <div class="cari-rm">
                                        <section>
                                            <label for="input-cari-rm">Cari Data</label>
                                            <div class="control-groub">
                                                <input class="textbox outline black rounded small" type="text" name="cari-rm" id="input-cari-rm" placeholder="No Rekam Medis">
                                                <button id="btn-cari-rm" class="btn rounded light blue fill" type="button">Pilih</button>
                                            </div>
                                        </section>
                                        <div class="select-rm">
                                            <div class="card profile rm two-line">
                                                <div id="titleProfile" class="title">Bayi Nyonya Y</div>
                                                <div id="descriptionProfile" class="description">Bandarjo x/x</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="navigation-box btn-kunjungan-box">
                                        <button id="btn-baru-datarm" class="btn rounded small green fill" type="button">Data Baru</button>
                                        <button id="btn-next-datarm" class="btn rounded small blue fill" type="button">Next</button>
                                    </div>
                                </div>
                            </div>
                            <div class="tab kedua">
                                <h2>Identitas Balita</h2>
                                <!-- nama rm -->
                                <section>
                                    <label for="input-nama-rm">Nama RM</label>
                                    <input class="textbox outline black rounded small" type="text" id="input-nama-rm" name="nama_rm" placeholder="Nama Balita">
                                </section>
                                <!-- tanggal lahir -->
                                <section>
                                    <label for="input-tanggal-lahir">Tangal Lahir</label>
                                    <input class="textbox outline black rounded small" type="date" id="input-tanggal-lahir" name="tanggal_lahir">
                                </section>
                                <!-- alamat -->
                                <section>
                                    <label for="input-alamat">Alamat</label>
                                    <input class="textbox outline black rounded small" type="text" id="input-alamat" name="alamat" placeholder="Almat Desa">
                                </section>
                                <div class="grub-control horizontal">
                                    <!-- nomor rt -->
                                    <section>
                                        <label for="input-nomor-rt">RT</label>
                                        <input class="textbox outline black rounded small" type="number" id="input-nomor-rt" name="nomor_rt" placeholder="Nomor Rt">
                                    </section>
                                    <div class="gap-space">
                                        <!-- gab -->
                                    </div>
                                    <!-- nomor rw -->
                                    <section>
                                        <label for="input-nomor-rw">RW</label>
                                        <input class="textbox outline black rounded small" type="number" id="input-nomor-rw" name="nomor_rw" placeholder="Nomor Rw">
                                    </section>
                                </div>
                                <!-- nama rm kk-->
                                <section>
                                    <label for="input-nama-kk">Nama Orang Tua</label>
                                    <input class="textbox outline black rounded small" type="text" id="input-nama-kk" name="nama_kk" placeholder="Nama Ibu / Ayah">
                                </section>
                                <div class="navigation-box btn-kunjungan-box">
                                    <button id="btn-back-cari" class="btn rounded small blue fill" type="button" id="btn-verifikasi">Kembali</button>
                                    <button id="btn-next-biodata" class="btn rounded small blue fill" type="button" id="btn-verifikasi">Next</button>
                                </div>
                            </div>
                            <div class="tab ketiga">
                                <h2>Biodata Balita</h2>
    
                                <!-- hoden input -->
                                <section>
                                    <input id="input-timestamp" type="hidden" name="timestamp">
                                    <input id="input-target" type="hidden" name="target">
                                </section>
                                <!-- jenis kelamin -->
                                <section>
                                    <label for="input-jenis-kelamin">Jenis Kelamin</label>
                                    <select class="textbox outline black rounded small" id="input-jenis-kelamin" name="jenis_kelamin">
                                        <option selected disabled hidden>Pilih Jenis Kelamin</option> 
                                        <option value="0">Perempuan</option>
                                        <option value="1">Laki-laki</option>
                                    </select>
                                </section>
                                <!-- bbl -->
                                <section>
                                    <label for="input-bbl">Berat Bayi Lahir</label>
                                    <input class="textbox outline black rounded small" type="number" id="input-bbl" name="bbl">
                                </section>
                                <!-- pbl -->
                                <section>
                                    <label for="input-pbl">Panjang Bayi Lahir</label>
                                    <input class="textbox outline black rounded small" type="number" id="input-pbl" name="pbl">
                                </section>
                                <!-- kia -->
                                <section>
                                    <label for="input-kia">KIA</label>
                                    <input class="textbox outline black rounded small" type="number" id="input-kia" name="kia">
                                </section>
                                <!-- imd -->
                                <section>
                                    <label for="input-imd">Indeks Masa </label>
                                    <input class="textbox outline black rounded small" type="number" id="input-imd" name="imd">
                                </section>
                                <!-- asi eks -->
                                <div>
                                    <input class="textbox outline black rounded small" type="checkbox" id="input-asi" name="asi_ekslusif" checked="">
                                    <label for="input-asi">Asi Ekslusif</label>
                                </div>
                                <div class="navigation-box btn-kunjungan-box">
                                    <button id="btn-back-datarm" class="btn rounded small blue fill" type="button" id="btn-verifikasi">Kembali</button>
                                    <button id="btn-request" class="btn rounded small green fill" type="submit" name="request">Simpan</button>
                                </div>
                            </div>
                            <div class="tab keempat">
                                <h2>Selesai</h2>
                                <p>Data Berhasil Disimpan</p>
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
    var dataRm = new Array();
    // property tab
    showTab(<?= $success ? 3 : 0 ?>);
    var valids = [false, false, false] // cari, data-rm, biodata
    
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
    $event('btn-cari-rm').click( () => {
        const val_input = $query('#input-cari-rm').value
        getDataRM(val_input)
    })
    $event('btn-baru-datarm').click( () => {
        valids[0] = true; // validasi data rm
        hideTab(0);
        showTab(1);
        // set taget
        $id('input-target').value = "staging"
        readonlyFormBiodata(false)
    })
    $event('btn-next-datarm').click( () => {
        if( valids[0] ){
            hideTab(0)
            showTab(1)
            // set taget
            $id('input-target').value = "data_rm"
            $id('input-timestamp').value = dataRm['data_dibuat']
            // isi form
            setFormbiodata()
            readonlyFormBiodata(true)
        }
    })
    // tab 2
    $event('btn-back-cari').click( () => {
        hideTab(1)
        showTab(0)
    })
    $event('btn-next-biodata').click( () => {
        if( validatedBiodata ){
            hideTab(1)
            showTab(2)
        }
    })
    // tab: 3 (button)    
    $event('btn-back-datarm').click( () => {
        hideTab(2)
        showTab(1)
    })

    // tab: 1 (function)
    function getDataRM(nomor_rm){
        $json(`/lib/ajax/json/private/med-rec/cari-rm/?nomor_rm=${nomor_rm}`)
            .then( json => {
                dataRm = json['data']
                // profile card
                titleProfile.innerText = dataRm.nama
                descriptionProfile.innerText = `${dataRm.alamat} ${dataRm.nomor_rt}/${dataRm.nomor_rw}`

                // validasi data rm
                if( json['status'] == 'ok' ){
                    valids[0] = true
                }
        })
    }
    // tab: 2 (funtion) 
    function readonlyFormBiodata(readonly = false){
        // dom documnet
        $id('input-nama-rm').disabled       = readonly
        $id('input-tanggal-lahir').disabled = readonly
        $id('input-alamat').disabled        = readonly
        $id('input-nomor-rt').disabled      = readonly
        $id('input-nomor-rw').disabled      = readonly
        $id('input-nama-kk').disabled       = readonly
    }
    function setFormbiodata(){
        $id('input-nama-rm').value          = dataRm['nama']
        $id('input-tanggal-lahir').value    = dataRm['tanggal_lahir']
        $id('input-alamat').value           = dataRm['alamat']
        $id('input-nomor-rt').value         = dataRm['nomor_rt']
        $id('input-nomor-rw').value         = dataRm['nomor_rw']
        $id('input-nama-kk').value          = dataRm['nama_kk']
    }
    function validatedBiodata(){
        valids[1] = true
    }

    
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
