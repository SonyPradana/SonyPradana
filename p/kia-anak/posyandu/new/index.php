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
    if( isset($_POST['request']) && isset( $_POST['id_hash'] )){
        if( $_POST['id_hash'] == '') return false;

        $posyandu = new PosyanduRecord($_POST['id_hash']);
        $posyandu->convertFromArray($_POST);
        if( $posyandu->creat() ){
            $msg = [];
            $msg['type'] = 'info';
            $msg['message'] = 'berhasil ditambah';
        }else{
            $msg = [];
            $msg['type'] = 'danger';
            $msg['message'] = 'gagal menyimpan';

        }
        // isi kembali data, agar tidak mereload halaman
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
    <title>Buat Tambah Posyandu Baru</title>
    <meta name="description" content="Sistem Informasi Manajemen Puskesmas SIMPUS Lerep">
    <meta name="keywords" content="simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">
    <link rel="stylesheet" href="/lib/css/ui/v1.1/modal.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <script src="/lib/js/bundles/dialog.modal.js"></script>
    <style>
        .boxs-container{
            /* width: 100%; height: 100%; */
            display: grid;
            grid-template-columns: 1fr 24px 1fr;
        }
        .box.left,
        .box.right{
            padding: 8px 16px
        }

        /* mobile */
        @media screen and (max-width: 740px) {            
            .boxs-container { grid-template-columns: 1fr }
            .box.right { padding: 5px }
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

        .btn-grup{
            margin-top: 16px;
            display: flex;
        }
        .gap-hori{
            max-width: 16px; min-width: 15px;
        }
    </style>
</head>
<body>
    <header>
        <?php 
            $active_menu = "Buat Data Posyandu";
            $menu_link = MENU_POSYANDU;
            include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.php') 
        ?>
    </header>
    <div class="modals">
        <!-- modal menu -->
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
        <!-- modal dialog -->
        <?php $views['modals']['type'] = 'search-by-name-adress' ?>
        <?php require_once BASEURL . '/lib/components/modals/modal.template.php' ?>
    </div>
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
                    <h1>Tambah Data Posyandu</h1>
                    <button class="btn rounded light blue fill" type=" type="button" id="cari-anak">Cari data anak</button>
                    <div class="box-profile-rm">
                        <p id="res-details"></p>
                    </div>
                    <div class="table-posynadu">
                        <table>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Tanggal</th>
                                    <th>Tinngi Badan</th>
                                    <th>Berat Badan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- table data -->
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="gap"></div>
                <div class="box right">
                    <h2>Form Pemeriksaan</h2>
                    <form action="" method="post">
                        <input type="hidden" name="id_hash" id="input-hash">
                        <section>
                            <label for="input-desa">Desa</label>
                            <select class="textbox outline black rounded small" name="desa" id="input-desa">
                                <option selected disabled hidden>Pilih Desa</option> 
                                <option value="bandarjo">Bandarjo</option>
                                <option value="branjang">Branjang</option>
                                <option value="kalisidi">Kalisidi</option>
                                <option value="keji">Keji</option>
                                <option value="lerep">Lerep</option>
                                <option value="nyatnyono">Nyatnyono</option>
                            </select>
                        </section>
                        <section>
                            <label for="input-posyandu">Nama posyandu</label>
                            <select class="textbox outline black rounded small" name="tempat_pemeriksaan" id="input-posyandu">
                                <option selected disabled hidden>Pilih Jenis Kelamin</option>
                            </select>
                        </section>
                        <!-- data -->
                        <section>
                            <label for="input-tanggal-pemeriksaan">Tanggal Pemeriksaan</label>
                            <input class="textbox outline black rounded small" type="date" name="tanggal_pemeriksaan" id="input-tanggal-pemeriksaan">
                        </section>
                        <section>
                            <label for="input-tinggi-badan">Tinggi Badan</label>
                            <input class="textbox outline black rounded small" type="number" name="tinggi_badan" id="input-tinggi-badan" placeholder="Dalam satuan cm" required>
                        </section>
                        <section>
                            <label for="input-berat-badan">Berat Badan</label>
                            <input class="textbox outline black rounded small" type="number" name="berat_badan" id="input-berat-badan" placeholder="Dalam satuan gram" required>
                        </section>
                         
                        <div class="btn-grup">
                            <button id="btn-sumbit" class="btn rounded small blue fill" type="submit" name="request">Tambah Data</button>
                            <div class="gap-hori"></div>
                            <button id="btn-cancel" class="btn rounded small red outline" type="button" name="batal" >Batal</button>
                        </div>
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
<script src="/lib/js/index.end.js"></script>
<script>    
    // sticky header
    window.onscroll = function(){
            stickyHeader('.container', '82px', '32px');
    }
    
    // keep alive
    keepalive(
        () => {
            // ok function : redirect logout and then redirect to login page to accses this page
            window.location.href = "/login?url=<?= $_SERVER['REQUEST_URI'] ?>&logout=true";
        },
        () => {          
            // close fuction : just logout
            window.location.href = "/logout?url=<?= $_SERVER['REQUEST_URI'] ?>";
        }
    );

    // modal search    
    const pref = {
        type    : 'search-by-name-adress',
        parrent : $event('modal-dialog'), 
        trigger : 'cari-anak',
        content : {
            input_nama   : $id('input-modal-nama'),
            input_alamat : $id('input-modal-alamat'),
            table_result : $id('modal-table-body')
        }
    }
    // callback 
    pref.result = function(e){
        $id('res-details').innerText  = `Nama: ${e.nama}, ${e.tanggal_lahir}`;
        $id('input-hash').value       = e.tanggal_dibuat;
        if(e.desa != 'null'){
            $id('input-desa').value   = e.desa;
            getGrupPosyandu(e.desa, e.id_posyandu);             // TODO    defaultnya bukan desa tp alamat admin
        }
        
        getHistoryPosyandau(e.tanggal_dibuat, e.id_posyandu);
    }
    modal_dialog( pref );

    $id('input-desa').addEventListener('change', event => {
        getGrupPosyandu(event.target.value);
    });
    
    function getGrupPosyandu(desa, select = null){
        // render grub desa
        $id('input-posyandu').innerHTML = '<option selected disabled hidden>Pilih Nama Posyandu</option>';
        $json(`/lib/ajax/json/public/grup-posyandu/?desa=${desa}`)
            .then( json => {
                json.data.forEach(element => {
                    let creat_option = document.createElement('option');
                    creat_option.setAttribute('value', element.id);
                    creat_option.innerText = element.posyandu;

                    $id('input-posyandu').appendChild(creat_option)  ;
                });

                if( select !== null){
                    $id('input-posyandu').value = select;
                }
            })
    }
    function getHistoryPosyandau(time_stamp, grups_posyandu){
        const url = `/lib/ajax/json/private/posyandu/search/?idhash=${time_stamp}&idposyandu=${grups_posyandu}`;
        $json( url )
            .then( json => {
                $query('.table-posynadu table tbody').innerHTML = '<!-- table data posyandu -->';
                let num = 1;
                json.data.forEach(element => {
                    let dom_tr = document.createElement('tr');

                    dom_tr.appendChild( $creat('td', num));
                    dom_tr.appendChild( $creat('td', element.tanggal_pemeriksaan));
                    dom_tr.appendChild( $creat('td', element.tinggi_badan));
                    dom_tr.appendChild( $creat('td', element.berat_badan));

                    $query('.table-posynadu table tbody').appendChild(dom_tr);
                    num++;
                });
            });
    }
</script>
</html>
