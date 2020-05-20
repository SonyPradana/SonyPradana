<?php
    #import modul 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
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
    
    # ambil parameter dari url
    $sort = isset( $_GET['sort'] ) ? $_GET['sort'] : 'nomor_rm';
    $order = isset( $_GET['order'] ) ? $_GET['order'] : 'ASC';
    $page = isset( $_GET['page'] ) ? $_GET['page'] : 1;
    $page = is_numeric($page) ? $page : 1;
    $max_page = 1;
    $limit = 10;
    # parameter untuk search data
    $main_search = isset( $_GET['main-search'] ) ? $_GET['main-search'] : '';
    $nomor_rm_search = isset( $_GET['nomor-rm-search']) ? $_GET['nomor-rm-search'] : '';
    $alamat_search = isset( $_GET['alamat-search'] ) ? $_GET['alamat-search'] : '';
    $no_rt_search = isset( $_GET['no-rt-search'] ) ? $_GET['no-rt-search'] : '';
    $no_rw_search = isset( $_GET['no-rw-search'] ) ? $_GET['no-rw-search'] : '';
    $nama_kk_search = isset( $_GET['nama-kk-search'] ) ? $_GET['nama-kk-search'] : '';
    $no_rm_kk_search = isset( $_GET['no-rm-kk-search'] ) ? $_GET['no-rm-kk-search'] : '';
    $strict_search = isset( $_GET['strict-search'] ) ? true : false;    

    # defultnya false agar tidak menampilkan table ketika kosong/pertama
    $get_data = false;
    
    if( isset( $_GET['main-search']) || 
        isset( $_GET['nomor-rm-search']) ||
        isset( $_GET['alamat-search']) ||
        isset( $_GET['no-rt-search']) ||
        isset( $_GET['no-rw-search']) ||
        isset( $_GET['nama-kk-search']) ||
        isset( $_GET['no-rm-kk-search']) ){
            
            # cari data
            $show_data = new View_RM();

            # setup data
            $show_data->sortUsing($sort);
            $show_data->orderUsing($order);
            $show_data->limitView($limit);

            # query data
            $show_data->filterByNama( $main_search );
            $show_data->filterByNomorRm( $nomor_rm_search);
            $show_data->filterByAlamat($alamat_search );
            $show_data->filterByRt( $no_rt_search );
            $show_data->filterByRw( $no_rw_search );
            $show_data->filterByNamaKK( $nama_kk_search );
            $show_data->filterByNomorRmKK( $no_rm_kk_search );
                    
            # setup page
            $max_page = $show_data->maxPage();
            $page = $page > $max_page ? $max_page : $page;
            $show_data->currentPage($page);

            # excute query
            $get_data = $show_data->result( $strict_search );
    }
    $parram = "'$main_search', '$nomor_rm_search', '$strict_search', '', '$alamat_search', '$no_rt_search', '$no_rw_search', '$nama_kk_search', '$no_rm_kk_search'";
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
<?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">    
    <link rel="stylesheet" href="/lib/css/ui/v1/pagination.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">

    <script src="/lib/js/index.js"></script>         
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="/lib/js/ajax/html/GetData.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
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
        /* .boxs .box.left .box-input.button-grub{
            margin-top: 10px;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .boxs .box.left .box-input.button-grub .box-input-right{
            display: flex;
            justify-content: flex-end;
        }
        .boxs .box.left button{
            height: 32px;
            width: 56px;
        }
        .boxs .box.left button#submit{
            margin-right: 4px
        }
        .boxs .box.left .box-input.checkbox-grub{
            display: flex;
            justify-content: baseline;
            margin-top: 10px;
        }
        .boxs .box.left .box-input.text-grub{
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-column-gap: 16px;
        } */
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
        <?php $active_menu = 'cari data' ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.html') ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    
    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Cari Data</li>
                </ul>
            </div>
            <h1>Cari Data Rekam Medis</h1>
            <div class="boxs">
                <div class="box left">
                    <form action="" method="get" class="search-box">
                        <input class="textbox outline black rounded small block" type="text" name="main-search" id="input-main-search" placeholder="cari nama" value="<?= $main_search ?>">
                        <div class="grub-control horizontal right">
                                <button class="btn rounded small blue" type="submit" id="submit">Cari</button>
                                <div class="gap-space"><!-- helper --></div>
                                <button class="btn rounded small blue" type="reset" id="reset-btn">Batal</button>
                        </div>
                        <div class="grub-control horizontal">
                            <input type="checkbox" name="strict-search" id="input-strict-search" <?= $strict_search == true ? "checked" : ""?>>
                            <label for="input-strict-search">Pencarian Mendalam</label>
                        </div>
                        <input class="textbox outline black rounded small block" type="text" name="nomor-rm-search" id="input-nomor-rm-seacrh" placeholder="cari nomor rm" value="<?= $nomor_rm_search ?>">
                        <input class="textbox outline black rounded small block" type="date" name="tgl-search" id="input-tgl-search" data-date-format="DD MMMM YYYY" value="<?= (isset($_GET['tgl-search'])) ? $_GET['tgl-search'] : '' ?>">
                        <input class="textbox outline black rounded small block" type="text" name="alamat-search" id="input-alamat-seacrh" placeholder="cari alamat" value="<?= $alamat_search ?>">
                        <div class="grub-control horizontal">
                            <input class="textbox outline black rounded small block" type="text" name="no-rt-search" id="input-no-rt-search" placeholder="cari alamat rt" value="<?= $no_rt_search ?>">
                            <div class="gap-space"><!-- helper --></div>
                            <div class="gap-space"><!-- helper --></div>
                            <input class="textbox outline black rounded small block" type="text" name="no-rw-search" id="input-no-rw-search" placeholder="cari alamat rw" value="<?= $no_rw_search ?>">
                        </div>
                        <input class="textbox outline black rounded small block" type="text" name="nama-kk-search" id="input-nama-kk-search" placeholder="cari nama kk" value="<?= $nama_kk_search ?>">
                        <input class="textbox outline black rounded small block" type="text" name="no-rm-kk-search" id="input-no-rm-kk" placeholder="cari nomor rm kk" value="<?= $no_rm_kk_search ?>">
                    </form>
                </div>
                <div class="box right">                        
                    <div class="box-right">   
                        <script>
                            getTableSearch( '<?= $sort ?>', '<?= $order ?>', '<?= $page ?>' , <?= $parram ?>)
                        </script>
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
    (function($) {
        $('form').submit(function() {
            $('form input').each(function() { 
                if ($(this).val().length == 0) { 
                    $(this).attr('disabled', true); 
                }
            });
        });
    })(jQuery);

    //claer button    
    var btnBack = document.querySelector('#reset-btn');
    btnBack.onclick = function () {
            document.querySelector('#input-main-search').setAttribute('value', '');
            document.querySelector('#input-tgl-search').setAttribute('value', '');
            document.querySelector('#input-alamat-seacrh').setAttribute('value', '');
            document.querySelector('#input-no-rt-search').setAttribute('value', '');
            document.querySelector('#input-no-rw-search').setAttribute('value', '');
            document.querySelector('#input-nama-kk-search').setAttribute('value', '');
            document.querySelector('#input-no-rm-kk').setAttribute('value', '');
    };
    // var href = new URL('http://localhost/p/med-rec/search-rm/?main-search=agus');
    // href.searchParams.set('page', 1);
    // console.log(href.toString());
    
    // sticky header
    window.onscroll = function(){
            stickyHeader('.container', '82px', '32px')
    }
    
    // keep alive
    keepalive(() => {
        window.location.href = "/p/auth/login/"
    });
</script>
</html>
