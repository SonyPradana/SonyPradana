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

    # defultnya false agar tidak menampilkan table ketika kosong/pertama
    $get_data = false;

    # ambil data dari url 
    $main_search = isset( $_GET['main-search'] ) ? $_GET['main-search'] : '';
    $alamat_search = isset( $_GET['alamat-search'] ) ? $_GET['alamat-search'] : '';
    $no_rt_search = isset( $_GET['no-rt-search'] ) ? $_GET['no-rt-search'] : '';
    $no_rw_search = isset( $_GET['no-rw-search'] ) ? $_GET['no-rw-search'] : '';
    $nama_kk_search = isset( $_GET['nama-kk-search'] ) ? $_GET['nama-kk-search'] : '';
    $no_rm_kk_search = isset( $_GET['no-rm-kk-search'] ) ? $_GET['no-rm-kk-search'] : '';    
    # data lainnya
    $sort = isset($_GET['sortby']) ? $_GET['sortby'] : 'id';
    
    if( isset( $_GET['submit'])  ){
            # cari berdasarkan nama
            $show_data->filterByNama( $main_search );
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
            $show_data->limitView(25);
            #mulai mencari data
            $get_data = $show_data->result( );
            $result_fount = count($get_data);
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
    <title>crai data rm</title>
    <meta name="description" content="sisteminformasi kesehtan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">

    <link rel="stylesheet" href="/lib/css/style-main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">    
    <link rel="stylesheet" href="/lib/css/ui/v1/control.default.css">
         
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style>
        #input-main-search{
            display: inline
        }
        .boxs{
            display: grid;
            grid-template-columns: minmax(250px, 350px) minmax(30%, auto);
            grid-column-gap: 10px;
        }
        .boxs p{margin-top: 0}
        .boxs .box.right .box-table {
            width: 100%;
            overflow-x: auto;
        }
        .boxs .box.left .box-form.bottom{
            padding: 0 50px 0 0;
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
                    <p>Search :</p>
                    <form action="" method="get">
                        <input type="search" name="main-search" id="input-main-search" placeholder="cari data rm" autofocus value="<?= $main_search ?>">
                        <button type="submit" name="submit">Cari</button>
                        <button type="reset" id="reset-btn">Batal</button>
                        <div class="box-form bottom">
                            <input type="date" name="tgl-search" id="input-tgl-search" data-date-format="DD MMMM YYYY" value="<?= (isset($_GET['tgl-search'])) ? $_GET['tgl-search'] : '' ?>">
                            <input type="text" name="alamat-search" id="input-alamat-seacrh" placeholder="cari alamat" value="<?= $alamat_search ?>">
                            <input type="text" name="no-rt-search" id="input-no-rt-search" placeholder="cari almat rt" value="<?= $no_rt_search ?>">
                            <input type="text" name="no-rw-search" id="input-no-rw-search" placeholder="cari alamat rw" value="<?= $no_rw_search ?>">
                            <input type="text" name="nama-kk-search" id="input-nama-kk-search" placeholder="cari nama kk" value="<?= $nama_kk_search ?>">
                            <input type="text" name="no-rm-kk-search" id="input-no-rm-kk" placeholder="cari nomor rm kk" value="<?= $no_rm_kk_search ?>">
                        </div>
                    </form>
                </div>
                <div class="box right">                        
                
                    <p>Result: <?= isset($result_fount) ? $result_fount : ''?></p>
                    <div class="box-table">   
                        <table>
                            <tr>
                                <th>No.</th>
                                <th scope="col"><a class="sort-by" href="#">No RM</a></th>
                                <th scope="col"><a class="sort-by" href="#">Nama</a></th>
                                <th scope="col"><a class="sort-by" href="#">Tanggal Lahir</a></th>
                                <th scope="col"><a class="sort-by" href="#">Alamat</a></th>
                                <th scope="col"><a class="sort-by" href="#">RT/RW</a></th>
                                <th scope="col"><a class="sort-by" href="#">Nama KK</a></th>
                                <th scope="col"><a class="sort-by" href="#">No. Rm KK</a></th>
                                <th><a href="#">Action</a></th>                                                       
                            </tr> 
                        <?php if ( $get_data ): ?>
                            <?php $idnum = (int) 1; ?>
                            <?php foreach( $get_data as $data) :?>            
                            <tr>       
                                <th><?= $idnum ?></th>
                                <th><?= $data['nomor_rm']?></th>
                                <th><?= $data['nama']?></th>
                                <th><?= $data['tanggal_lahir']?></th>
                                <th><?= $data['alamat']?></th>
                                <th><?= $data['nomor_rt'] . ' / ' . $data['nomor_rw']?></th>
                                <th><?= $data['nama_kk']?></th>
                                <th><?= $data['nomor_rm_kk']?></th>
                                <th><a class="link" href="/p/med-rec/edit-rm/index.php?document_id=<?= $data['id']?>">edit</a></th>
                            </tr>   
                            <?php $idnum++; ?>
                            <?php endforeach ; ?>      
                            <?= '</table>' ?>                      
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
