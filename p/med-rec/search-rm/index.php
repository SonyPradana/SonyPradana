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
    
    # ambil parameter dari url
    $main_search     = $_GET['main-search'] ?? '';
    $nomor_rm_search = $_GET['nomor-rm-search'] ?? '';
    $alamat_search   = $_GET['alamat-search'] ?? '';
    $no_rt_search    = $_GET['no-rt-search'] ?? '';
    $no_rw_search    = $_GET['no-rw-search'] ?? '';
    $nama_kk_search  = $_GET['nama-kk-search'] ?? '';
    $no_rm_kk_search = $_GET['no-rm-kk-search'] ?? '';
    $strict_search   = isset( $_GET['strict-search'] ) ? true : false;    

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari data rm</title>
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
        .box.left form > input{
            width: 100%
        }
        .box.left form > input:not(:first-child),
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
            $active_menu = "Cari RM";
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
                    <li>Cari Data</li>
                </ul>
            </div>
            <h1>Cari Data Rekam Medis</h1>
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
                        <input class="textbox outline black rounded small block" type="text" name="nomor-rm-search" id="input-nomor-rm-seacrh" placeholder="cari nomor rm" value="<?= $nomor_rm_search ?>">
                        <input class="textbox outline black rounded small block" type="date" name="tgl-search" id="input-tgl-search" data-date-format="DD MMMM YYYY" value="<?= (isset($_GET['tgl-search'])) ? $_GET['tgl-search'] : '' ?>">
                        <input class="textbox outline black rounded small block" type="text" name="alamat-search" id="input-alamat-seacrh" placeholder="cari alamat" value="<?= $alamat_search ?>" <?= $DNT_Enable ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <div class="grub-control horizontal">
                            <input class="textbox outline black rounded small block" type="text" name="no-rt-search" id="input-no-rt-search" placeholder="cari alamat rt" value="<?= $no_rt_search ?>">
                            <div class="gap-space"><!-- helper --></div>
                            <div class="gap-space"><!-- helper --></div>
                            <input class="textbox outline black rounded small block" type="text" name="no-rw-search" id="input-no-rw-search" placeholder="cari alamat rw" value="<?= $no_rw_search ?>">
                        </div>
                        <input class="textbox outline black rounded small block" type="text" name="nama-kk-search" id="input-nama-kk-search" placeholder="cari nama kk" value="<?= $nama_kk_search ?>" <?= $DNT_Enable ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <input class="textbox outline black rounded small block" type="text" name="no-rm-kk-search" id="input-no-rm-kk" placeholder="cari nomor rm kk" value="<?= $no_rm_kk_search ?>">
                    </form>
                </div>
                <div class="box right">                        
                    <div class="box-right">   
                        <table class="data-rm">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('nomor_rm')">No RM</a></th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('nama')">Nama</a></th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('tanggal_lahir')">Tanggal Lahir</a></th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('alamat')">Alamat</a></th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('nomor_rw')">RT / RW</a></th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('nama_kk')">Nama KK</a></th>
                                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('nomor_rm_kk')">No. Rm KK</a></th>
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
<script type="text/javascript">
    // clear url
    let myform = document.querySelector('form.search-box');
    myform.addEventListener('submit', () => {
        elements = myform.elements
        for (let i = 0, element; element = elements[i++];) {
            if ((element.type === "text" || element.type === "date") && element.value === "")
                element.disabled = true
        }
    })

    //claer button    
    var btnBack = document.querySelector('#reset-btn');
    btnBack.onclick = function () {
        document.querySelector('#input-main-search').setAttribute('value', '');
        document.querySelector('#input-nomor-rm-seacrh').setAttribute('value', '');
        document.querySelector('#input-tgl-search').setAttribute('value', '');
        document.querySelector('#input-alamat-seacrh').setAttribute('value', '');
        document.querySelector('#input-no-rt-search').setAttribute('value', '');
        document.querySelector('#input-no-rw-search').setAttribute('value', '');
        document.querySelector('#input-nama-kk-search').setAttribute('value', '');
        document.querySelector('#input-no-rm-kk').setAttribute('value', '');
    };    
    
    window.addEventListener('load', () => {
        // get data from DOM or URL
        const queryString = window.location.search
        let searchParams = new URLSearchParams(queryString)

        _search_name = searchParams.get('main-search')
        _search_name_kk = searchParams.get('nama-kk-search')

        let query = '&' + searchParams.toString()
        if( query != '&'){
            getData(_sort, _order, _cure_page, query)
            _search_query = query
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
            window.location.href = "/login?url=<?= $_SERVER['REQUEST_URI'] ?>&logout=true"
        },
        () => {          
            // close fuction : just logout
            window.location.href = "/logout?url=<?= $_SERVER['REQUEST_URI'] ?>"
        }
    );
</script>
</html>
