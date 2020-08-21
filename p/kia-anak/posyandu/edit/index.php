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

$user        = new User($auth->getUserName());
$document_id = $_GET['document_id'] ?? header_exit();
$params      = explode('-', $document_id);                                // [0]: code_hash, [1]: id 
//  
$code_hash  = $params[0];                                                // validasi document id type, harus angka
$id         = $params[1] ?? header_exit();
// 
$posyandu   = new PosyanduRecord($code_hash);
$isValided  = $posyandu->IsValided();
$read       = $posyandu->read( $id );

if( isset( $_POST['request']) && $isValided && $read){
    // setter dari user input
    $posyandu->setTempatPemeriksaan( $_POST['tempat_pemeriksaan'] ?? $posyandu->getTempatPemeriksaan() )
             ->setTenagaPemeriksaan( $auth->getUserName() )
             ->setTanggalPemeriksaan( $_POST['tanggal_pemeriksaan'] ?? $posyandu->getTanggalPemeriksaan() )
             ->setTinggiBadan( $_POST['tinggi_badan'] ?? $posyandu->getTinggiBadan() )
             ->setBeratBadan( $_POST['berat_badan'] ?? $posyandu->getBeratBadan() );
    $update = $posyandu->update( $id );
    // message
    if( $update ){
        $msg = [];
        $msg['message'] = 'Berhasil disimpan';
        $msg['type'] = 'success';        
    }
}elseif( $isValided == false || $read == false){
    header_exit();
}
//  isi form dari data base, 
$desa_posyandu          = GroupsPosyandu::getPosyanduDesa( $posyandu->getTempatPemeriksaan() );
$nama_posyandu          = GroupsPosyandu::getPosyanduName( $posyandu->getTempatPemeriksaan() );
$groups_posyandu        = GroupsPosyandu::getPosyandu( $desa_posyandu );
$tanggal_pemeriksaan    = $posyandu->getTanggalPemeriksaan();
$tinggi_pemeriksaan     = $posyandu->getTinggiBadan();
$berat_pemeriksaan      = $posyandu->getBeratBadan();

// function helper
function header_exit(){
    header('HTTP/1.0 405 Method Not Allowed');
    echo '<h1>Acces deny!!!</h1>';
    exit();
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

        /* btn goups */
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
            $active_menu = 'Edit Data Posyandu';
            $menu_link = MENU_POSYANDU;
            include(BASEURL . '/lib/components/header/header.php')
        ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="/rekam-medis">KIA Anak</a></li>
                    <li>Edit Data</li>
                </ul>
            </div>
            <div class="boxs-container">
                <div class="box left">
                </div>
                <div class="gap"></div>
                <div class="box right">
                    <h1>Edit Data Posyandu</h1>
                    <form action="" method="post">
                        <section>
                            <label for="input-desa">Desa</label>
                            <select class="textbox outline black rounded small" name="desa" id="input-desa">
                                <!-- <option selected disabled hidden>Pilih Desa</option>  -->
                                <option <?= $desa_posyandu == 'bandarjo' ? 'selected' : '' ?> value="bandarjo">Bandarjo</option>
                                <option <?= $desa_posyandu == 'branjang' ? 'selected' : '' ?> value="branjang">Branjang</option>
                                <option <?= $desa_posyandu == 'kalisidi' ? 'selected' : '' ?> value="kalisidi">Kalisidi</option>
                                <option <?= $desa_posyandu == 'keji' ? 'selected' : '' ?> value="keji">Keji</option>
                                <option <?= $desa_posyandu == 'lerep' ? 'selected' : '' ?> value="lerep">Lerep</option>
                                <option <?= $desa_posyandu == 'nyatnyono' ? 'selected' : '' ?> value="nyatnyono">Nyatnyono</option>
                            </select>
                        </section>
                        <section>
                            <label for="input-posyandu">Nama posyandu</label>
                            <select class="textbox outline black rounded small" name="tempat_pemeriksaan" id="input-posyandu">
                                <!-- <option selected disabled hidden>Pilih Jenis Kelamin</option> -->
                                <?php foreach( $groups_posyandu as $group_posyandu): ?>
                                    <option <?= $desa_posyandu == $group_posyandu['posyandu'] ? 'selected' : '' ?> value="<?= $group_posyandu['id'] ?>"><?= $group_posyandu['posyandu'] ?></option>                                    
                                <?php endforeach; ?>
                            </select>
                        </section>
                        <!-- data -->
                        <section>
                            <label for="input-tanggal-pemeriksaan">Tanggal Pemeriksaan</label>
                            <input class="textbox outline black rounded small" type="date" name="tanggal_pemeriksaan" id="input-tanggal-pemeriksaan" value="<?= $tanggal_pemeriksaan ?>">
                        </section>
                        <section>
                            <label for="input-tinggi-badan">Tinggi Badan</label>
                            <input class="textbox outline black rounded small" type="number" name="tinggi_badan" id="input-tinggi-badan" placeholder="Dalam satuan cm" required value="<?= $tinggi_pemeriksaan ?>">
                        </section>
                        <section>
                            <label for="input-berat-badan">Berat Badan</label>
                            <input class="textbox outline black rounded small" type="number" name="berat_badan" id="input-berat-badan" placeholder="Dalam satuan gram" required value="<?= $berat_pemeriksaan ?>">
                        </section>
                         
                        <div class="btn-grup">
                            <button id="btn-sumbit" class="btn rounded small blue fill" type="submit" name="request">Edit Data</button>
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

    // pilih nama desa
    $id('input-desa').addEventListener('change', function(event){
        // load nama posyandu
        $id('input-posyandu').innerHTML = '<option selected disabled hidden>Pilih Nama Posyandu</option>';
        $json('/lib/ajax/json/public/grup-posyandu/?desa=' + event.target.value)
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
    })
</script>
</html>
