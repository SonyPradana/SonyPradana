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
        header("Location: /p/auth/login/?url=" . $_SERVER['REQUEST_URI']); 
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
    
    # ambil parameter dari url
    $main_search     = $_GET['main-search'] ?? '';
    $alamat_search   = $_GET['alamat-search'] ?? '';
    $no_rt_search    = $_GET['no-rt-search'] ?? '';
    $no_rw_search    = $_GET['no-rw-search'] ?? '';
    $nama_kk_search  = $_GET['nama-kk-search'] ?? '';
    $strict_search   = isset( $_GET['strict-search'] ) ? true : false;
    $desa            = $_GET['desa'] ?? null;
    $id_posyandu     = $_GET['tempat_pemeriksaan'] ?? null;

    if( $desa != null){
        $groups_posyandu = GroupsPosyandu::getPosyandu($desa);
    }

    if( $id_posyandu != null && is_numeric($id_posyandu)){
        $posyandu = GroupsPosyandu::getPosyanduName($id_posyandu);        
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
    <title>Cari data KIA anak</title>
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
    <script src="/lib/js/controller/table-kia-anak/index.js"></script>
    <style>
        .boxs{
            display: grid;
            grid-template-columns: minmax(250px, 300px) minmax(30%, auto);
            grid-column-gap: 10px;
        }
        .boxs .box.left { margin-right: 24px }
        .box.left form.search-box{
            position: -webkit-sticky;
            position: sticky;
            top: 80px;
        }
        .box.left form > input,
        .box.left form > select{
            width: 100%
        }
        .box.left form > input:not(:first-child),
        .box.left form > select,
        .box.left form > .grub-control.horizontal{
            margin-top: 10px
        }
        .box-right p.info{ display: none}
        .boxs .box.right .box-right {
            width: 100%;
            overflow-x: auto;
        } 
        /* mobile */
        @media screen and (max-width: 600px) {                
            .box.left form.search-box {
                position: unset;
                top: unset;
            }
            .boxs{
                display: block
            }
        }
    </style>
</head>
<body>
    <header>
        <?php 
            $menu_link = [["Lihat Data KIA", "/p/kia-anak/biodata/view/"], ["Cari Data KIA", "/p/kia-anak/biodata/search/"], ["Buat Data KIA", "/p/kia-anak/biodata/new/"] ];
            $active_menu = $menu_link[1];
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
                    <li>Cari Data</li>
                </ul>
            </div>
            <h1>Cari Data KIA Anak</h1>
            <div class="boxs">
                <div class="box left">
                    <form action="" method="get" class="search-box">
                        <input class="textbox outline black rounded small block" type="text" name="main-search" id="input-main-search" placeholder="cari nama" value="<?= $main_search ?>" <?= $DNT_Enable ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <div class="grub-control horizontal right">
                            <button class="btn rounded light blue outline" type="submit" id="submit">Cari</button>
                            <div class="gap-space"><!-- helper --></div>
                            <button class="btn rounded light red outline" type="reset" id="reset-btn">Batal</button>
                        </div>
                        <div class="grub-control horizontal">
                            <input type="checkbox" name="strict-search" id="input-strict-search" <?= $strict_search == true ? "checked" : ""?>>
                            <label for="input-strict-search">Pencarian Mendalam</label>
                        </div>
                        <input class="textbox outline black rounded small block" type="text" name="alamat-search" id="input-alamat-seacrh" placeholder="cari alamat" value="<?= $alamat_search ?>" <?= $DNT_Enable ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <div class="grub-control horizontal">
                            <input class="textbox outline black rounded small block" type="text" name="no-rt-search" id="input-no-rt-search" placeholder="cari alamat rt" value="<?= $no_rt_search ?>">
                            <div class="gap-space"><!-- helper --></div>
                            <div class="gap-space"><!-- helper --></div>
                            <input class="textbox outline black rounded small block" type="text" name="no-rw-search" id="input-no-rw-search" placeholder="cari alamat rw" value="<?= $no_rw_search ?>">
                        </div>
                        <input class="textbox outline black rounded small block" type="text" name="nama-kk-search" id="input-nama-kk-search" placeholder="cari nama orang tua" value="<?= $nama_kk_search ?>" <?= $DNT_Enable ? 'autocomplete="off"' : 'autocomplete="on"' ?>>

                        <p>Grup Posyandu</p>
                        <select class="textbox outline black rounded small block" name="desa" id="input-desa">
                            <option <?= $desa == null ? 'selected' : null ?> disabled hidden>Pilih Desa</option> 
                            <option <?= $desa == 'bandarjo' ? 'selected' : null ?> value="bandarjo">Bandarjo</option>
                            <option <?= $desa == 'branjang' ? 'selected' : null ?> value="branjang">Branjang</option>
                            <option <?= $desa == 'kalisidi' ? 'selected' : null ?> value="kalisidi">Kalisidi</option>
                            <option <?= $desa == 'keji' ? 'selected' : null ?> value="keji">Keji</option>
                            <option <?= $desa == 'lerep' ? 'selected' : null ?> value="lerep">Lerep</option>
                            <option <?= $desa == 'nyatnyono' ? 'selected' : null ?> value="nyatnyono">Nyatnyono</option>
                        </select>
                        <select class="textbox outline black rounded small block" name="tempat_pemeriksaan" id="input-posyandu">
                            <?php if( isset($posyandu) && isset($groups_posyandu) ): ?>
                                <option disabled hidden>Pilih Jenis Kelamin</option>
                                <?php foreach( $groups_posyandu as $key => $val): ?>
                                    <option <?= $posyandu == $val['posyandu'] ? 'selected' : null ?>><?= ($val['posyandu']) ?></option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option selected disabled hidden>Pilih Jenis Kelamin</option>
                            <?php endif; ?>

                        </select>
                    </form>
                </div>
                <div class="box right">                        
                    <div class="box-right">   
                        <table class="data-rm">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="tableKiaAnak.sortTable('nama')">Nama</a></th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="tableKiaAnak.sortTable('tanggal_lahir')">Tanggal Lahir</a></th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="tableKiaAnak.sortTable('alamat')">Alamat</a></th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="tableKiaAnak.sortTable('nomor_rw')">RT / RW</a></th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="tableKiaAnak.sortTable('nama_kk')">Nama KK</a></th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="tableKiaAnak.sortTable('nama_kk')">Grup Posyandu</a></th>
                                    <th>Action</th>                                                     
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <p class="info">Data tidak ditemukan</p>
                        <div class="box-pagination">
                            <div class="pagination">
                                <!-- pagination -->
                            </div>
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

    // render table
    $load(function(){
        // get data from DOM or URL
        const queryString = window.location.search
        let searchParams = new URLSearchParams(queryString)

        tableKiaAnak.search_name    = searchParams.get('main-search')
        tableKiaAnak.search_nameKK  = searchParams.get('nama-kk-search')

        let query = '&' + searchParams.toString()
        if( query != '&'){
            tableKiaAnak.getData(tableKiaAnak.sort, tableKiaAnak.order, tableKiaAnak.curentPage, query)
            tableKiaAnak.search_query = query
        }        
    })

    // sticky header
    window.onscroll = function(){
            stickyHeader('.container', '82px', '32px')
    }
    
    // keep alive
    keepalive(
        () => {
            // ok function : redirect logout and then redirect to login page to accses this page
            window.location.href = "/p/auth/login/?url=<?= $_SERVER['REQUEST_URI'] ?>&logout=true"
        },
        () => {          
            // close fuction : just logout
            window.location.href = "/p/auth/logout/?url=<?= $_SERVER['REQUEST_URI'] ?>"
        }
    );

    // prety url
    $query('form.search-box').addEventListener('submit', function(){
        let elements = $query('form.search-box').elements;
        for (let i = 0, element; element = elements[i++];) {
            // select-one
            if( element.type === "select=one" || element.type === "text"){
                if( element.value === ""){
                    element.disabled = true;
                }
            }

        }
    });

    //claer button
    $id('reset-btn').addEventListener('click', function(){
        $id('input-main-search').setAttribute('value', '');
        $id('input-alamat-seacrh').setAttribute('value', '');
        $id('input-no-rt-search').setAttribute('value', '');
        $id('input-no-rw-search').setAttribute('value', '');
        $id('input-nama-kk-search').setAttribute('value', '');
    });

    // get group posyandu
    $id('input-desa').addEventListener('change', function(e){        
        $id('input-posyandu').innerHTML = '<option selected disabled hidden>Pilih Nama Posyandu</option>';
        $json('/lib/ajax/json/public/grup-posyandu/?desa=' + e.target.value)
            .then( function(json){
                if(json.status == 'ok' && json.data.length > 0){                    
                    // render
                    json.data.forEach( function(el){
                        let creat_option = document.createElement('option');
                        creat_option.setAttribute('value', el.id);
                        creat_option.innerText = el.posyandu;

                        $id('input-posyandu').appendChild(creat_option)  ;
                    })
                }
            })
    })
</script>
</html>
