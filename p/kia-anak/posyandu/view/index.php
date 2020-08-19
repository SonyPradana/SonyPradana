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
    $pdo = new MyPDO();

    // TODO list:
    //  [X] 1. filter / order / sorting,' data posyandu' (sesui kriteria)
    //  [X] 2. generate id_hash dr 'data posyandu' (remove duplicate)
    //  [X]     2.2 ambil juga jumlah row terbanya id_hash (count id_hash tertinggi)  ---> render column 
    //  [X] 3. ambil 'data rm'  namanya saja, dan 'data kia' alamatnya saja           ---> alamat dipending
    //  [ ] 4. tampilkan data hasil merge 'data rm', 'data kia', 'data posyandu'




    // data posyandu raw
    $posyandu = new PosyanduRecords( $pdo );
    // filter data
    $posyandu->setStrictSearch(true)
             ->filtterByAlamat(10);
    // hasil filter
    $raw = $posyandu->result();
    
    // generate id_hash
    $count = $posyandu->CountID();
    // ambil count by hash_id
    $kolom_terbanyak   = max( array_column($count, 'jumlah_kunjungan') );


    // cari data rm dan alamat
    $medrec = new MedicalRecord($pdo);    
    
    for ($i=0; $i < count($count); $i++) {      
        $filrer_by = $count[$i]['id_hash'];
        $count[$i]['data'] = array_filter($raw, function($e){
            global $filrer_by;
            return $e['id_hash'] == $filrer_by;
        });
        sort( $count[$i]['data']);
        $relation = Relation::where('id_hash', $count[$i]['id_hash'], $pdo)[0];
        $medrec->refreshUsingIdHash($relation['time_stamp']);
        $count[$i]['nama'] = $medrec->getNama();
        $count[$i]['alamat'] = $medrec->getAlamatLengkap();
    }
        
    // hasil merger dalam array 
    // $all_data = $count;
    
    // HttpHeader::standartJsonHeader();
    // HttpHeader::printJson(["data" => $contoh],  200);    
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liat data rm</title>
    <meta name="description" content="sisteminformasi kesehtan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/pagination.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <script src="/lib/js/controller/table-rm/index.js"></script>
    <style>
        body{
            transition: margin-left .3s;
        }

        .btn-boxs{
            display: flex;
            justify-content: start;
        }
        .btn-box.left{
            display: flex;
        }

        button{ margin: 8px 0;}
        p{ margin: 16px 0 }
        .boxs{
            width: 100%;
        }
        .box-right{
            width: 100%;
            overflow-x: auto;
        }
        .box-right p.info{ display: none;}
    
        table { width: 100% }  
        .gab{
            min-width: 20px;
        }
    </style>
</head>
<body>
    <header>
        <?php 
            $menu_link = [["Lihat Data KIA", "/p/kia-anak/biodata/view/"], ["Cari Data KIA", "/p/kia-anak/biodata/search/"], ["Buat Data KIA", "/p/kia-anak/biodata/new/"] ];
            $active_menu = $menu_link[0];
        ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.php') ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="/p/med-rec/">KIA Anak</a></li>
                    <li>Lihat Data</li>
                </ul>
            </div>
            <h1>Lihat Data KIA Anak</h1>
            <div class="btn-boxs">
                <div class="btn-box-right">
                    <button class="btn fill blue rounded small block" id="btnFilter">Filter</button>
                </div>
            </div>
            <div class="boxs">
                <div class="box-left">
                </div>
                <div class="box-right">
                    <!-- table -->
                    <table class="data-rm">       
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama</a></th>
                                <th>Alamat</th>
                                <?php for($i=0; $i < $kolom_terbanyak; $i++): ?>
                                <th>pemeriksaan ke <?= $i+1 ?></th>
                                <?php endfor; ?>
                                <th>Action</th>                                                     
                            </tr>                       
                        </thead>
                        <tbody>
                            <?php $num = (int) 1 ?>
                            <?php foreach( $count as $data) : ?>
                            <tr>       
                                <th><?= $num ?></th>
                                <th><?= ucwords( $data['nama'] ) ?></th>
                                <th><?= ucwords( $data['alamat'] ) ?></th>                                
                                <?php foreach($data['data'] as $pertemuan): ?>
                                <th><?= $pertemuan['tinggi_badan'] ?></th>
                                <?php endforeach; ?>
                                <?php $minus =  $kolom_terbanyak - count($data['data']); ?>
                                <?php for ($i=0; $i < $minus; $i++): ?>
                                    <th>--</th>
                                <?php endfor; ?>
                                <th><a href="javascript:void(0)" onclick="$work('<?= $data['id_hash'] ?>')">Lihat</a></th>
                                <?php $num++ ?>
                            </tr>
                            <?php endforeach ; ?>
                        </tbody>
                    </table>
                    <p class="info">Data tidak ditemukan</p>
                    <div class="box-pagination">
                        <div class="pagination">
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>
</body>
<script src="/lib/js/index.end.js"></script>
<script>
    // onload
    window.addEventListener('load', () => {
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

    // row click tampilkan data
</script>
</html>
