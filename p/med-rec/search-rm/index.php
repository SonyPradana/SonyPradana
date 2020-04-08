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
    # ambil data
    $show_data = new View_RM();

    #config
    $sort = 'nomor_rm';
    $order = 'ASC';
    $page = isset( $_GET['page'] ) ? $_GET['page'] : 1;
    $page = is_numeric($page) ? $page : 1;

    # defultnya false agar tidak menampilkan table ketika kosong/pertama
    $get_data = false;

    # ambil data dari url 
    $main_search = isset( $_GET['main-search'] ) ? $_GET['main-search'] : '';
    $nomor_rm_search = isset( $_GET['nomor-rm-search']) ? $_GET['nomor-rm-search'] : '';
    $alamat_search = isset( $_GET['alamat-search'] ) ? $_GET['alamat-search'] : '';
    $no_rt_search = isset( $_GET['no-rt-search'] ) ? $_GET['no-rt-search'] : '';
    $no_rw_search = isset( $_GET['no-rw-search'] ) ? $_GET['no-rw-search'] : '';
    $nama_kk_search = isset( $_GET['nama-kk-search'] ) ? $_GET['nama-kk-search'] : '';
    $no_rm_kk_search = isset( $_GET['no-rm-kk-search'] ) ? $_GET['no-rm-kk-search'] : '';    
    # data lainnya
    $strict_search = isset( $_GET['strict-search'] ) ? true : false;
    
    if( isset( $_GET['main-search']) || 
        isset( $_GET['nomor-rm-search']) ||
        isset( $_GET['alamat-search']) ||
        isset( $_GET['no-rt-search']) ||
        isset( $_GET['no-rw-search']) ||
        isset( $_GET['nama-kk-search']) ||
        isset( $_GET['no-rm-kk-search']) ){
            # cari berdasarkan nama
            $show_data->filterByNama( $main_search );
            # cari berdasarkan nomor rm 
            $show_data->filterByNomorRm( $nomor_rm_search);
            # cari berdasarkan alamat
            $show_data->filterByAlamat($alamat_search );
            # cari berdasarkan rt dan rw
            $show_data->filterByRt( $no_rt_search );
            $show_data->filterByRw( $no_rw_search );
            # cari bedasarkan nama kk
            $show_data->filterByNamaKK( $nama_kk_search );
            # cari berdasarkan nomor rm kk
            $show_data->filterByNomorRmKK( $no_rm_kk_search );
        
            $show_data->sortUsing($sort);
            $show_data->orderUsing($order);
            $show_data->limitView(10);
            $max_page = $show_data->maxPage();
            $page = $page > $max_page ? $max_page : $page;
            $show_data->currentPage($page);
            #mulai mencari data
            $get_data = $show_data->result( $strict_search );
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
    <title>Cari data rm</title>
    <meta name="description" content="sisteminformasi kesehtan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/style-main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">    
    <link rel="stylesheet" href="/lib/css/ui/v1/pagination.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.default.css">
         
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style>
        .boxs{
            display: grid;
            grid-template-columns: minmax(250px, 300px) minmax(30%, auto);
            grid-column-gap: 10px;
        }
        .boxs .box.left{margin-right: 24px;}
        .boxs .box.left .box-input.button-grub{
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
        }
        .boxs .box.right{margin-top: 10px}
        .boxs .box.right .box-right {
            width: 100%;
            overflow-x: auto;
        }
        /* mobile */
        @media screen and (max-width: 600px) {                
            .boxs{
                display: block
            }
        }
    </style>
</head>
<body>
    <header>
        <?php $active_menu = 'cari data' ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/header.html') ?>
    </header>
    <main>
        <div class="container">
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Cari Data</li>
                </ul>
            </div>
            <h1>Cari Data Rekam Medis</h1>
            <div class="boxs">
                <div class="box left">
                    <form action="" method="get">
                        <input type="text" name="main-search" id="input-main-search" placeholder="cari nama" value="<?= $main_search ?>">
                        <div class="box-input button-grub">
                            <div class="box-input-left">

                            </div>
                            <div class="box-input-right">
                                <button type="submit" id="submit">Cari</button>
                                <button type="reset" id="reset-btn">Batal</button>                            
                            </div>
                        </div>
                        <div class="box-input checkbox-grub">
                            <input type="checkbox" name="strict-search" id="input-strict-search" <?= $strict_search == true ? "checked" : ""?>>
                            <label for="input-strict-search">Pencarian Mendalam</label>
                        </div>
                        <input type="text" name="nomor-rm-search" id="input-nomor-rm-seacrh" placeholder="cari nomor rm" value="<?= $nomor_rm_search ?>">
                        <input type="date" name="tgl-search" id="input-tgl-search" data-date-format="DD MMMM YYYY" value="<?= (isset($_GET['tgl-search'])) ? $_GET['tgl-search'] : '' ?>">
                        <input type="text" name="alamat-search" id="input-alamat-seacrh" placeholder="cari alamat" value="<?= $alamat_search ?>">
                        <div class="box-input text-grub">
                            <input type="text" name="no-rt-search" id="input-no-rt-search" placeholder="cari almat rt" value="<?= $no_rt_search ?>">
                            <input type="text" name="no-rw-search" id="input-no-rw-search" placeholder="cari alamat rw" value="<?= $no_rw_search ?>">
                        </div>
                        <input type="text" name="nama-kk-search" id="input-nama-kk-search" placeholder="cari nama kk" value="<?= $nama_kk_search ?>">
                        <input type="text" name="no-rm-kk-search" id="input-no-rm-kk" placeholder="cari nomor rm kk" value="<?= $no_rm_kk_search ?>">
                    </form>
                </div>
                <div class="box right">                        
                    <div class="box-right">   
                        <table>
                            <tr>
                                <th>No.</th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >No RM</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >Nama</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >Tanggal Lahir</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >Alamat</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >RT / RW</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >Nama KK</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >No. Rm KK</a></th>
                                <th><a href="javascript:void(0)">Action</a></th>                                                     
                            </tr> 
                        <?php if ( $get_data ): ?>
                            <?php $idnum = (int) 1; ?>
                            <?php foreach( $get_data as $data) :?>            
                            <tr>       
                                <th><?= $idnum ?></th>
                                <th><?= $data['nomor_rm']?></th>
                                <th><?= StringManipulation::addHtmlTag($data['nama'], $main_search, '<span style="color:blue">', '</span>')?></th>
                                <th><?= date("d-m-Y", strtotime( $data['tanggal_lahir']))  ?></th>
                                <th><?= $data['alamat']?></th>
                                <th><?= $data['nomor_rt'] . ' / ' . $data['nomor_rw']?></th>
                                <th><?= StringManipulation::addHtmlTag($data['nama_kk'], $nama_kk_search, '<span style="color:blue">', '</span>')?></th>
                                <th><?= $data['nomor_rm_kk']?></th>
                                <th><a class="link" href="/p/med-rec/edit-rm/index.php?document_id=<?= $data['id']?>">edit</a></th>
                            </tr>   
                            <?php $idnum++; ?>
                            <?php endforeach ; ?>      
                            <?= '</table>' ?>     
                            <div class="box-pagination">
                                <div class="pagination">
                                    <!-- paganation -->
                                </div>
                            </div>                 
                        <?php else : ?>
                        </table>    
                            <p>data tidak ditemukan</p>
                        <?php endif; ?>
                    </div> 
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div class="line"></div>
        <p class="big-footer">SIMPUS LEREP</p>
        <p class="note-footer">creat by <a href="https://twitter.com/AnggerMPd">amp</a></p>
        <div class="box"></div>
    </footer>
</body>
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

</script>
</html>
