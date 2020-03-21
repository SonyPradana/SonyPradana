<?php
    #import modul 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/auth/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/library/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/simpus/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/db/db_crud/DbConfig.php';
?>
<?php
    #Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $new_auth = new Auth($token, 2);
    if( !$new_auth->TrushClient() ){
        header("Location: /p/auth/login");   
        exit();
    }
?>
<?php
    # ambil data
    $show_data = new View_RM();

    # defultnya false agar tidak menampilkan table ketika kosong/pertama
    $get_data = false;

    # get data dari link  
    $main_search = isset( $_GET['main-search'] ) ? $_GET['main-search'] : '';
    $alamat_search = isset( $_GET['alamat-search'] ) ? $_GET['alamat-search'] : '';
    $no_rt_search = isset( $_GET['no-rt-search'] ) ? $_GET['no-rt-search'] : '';
    $no_rw_search = isset( $_GET['no-rw-search'] ) ? $_GET['no-rw-search'] : '';
    $nama_kk_search = isset( $_GET['nama-kk-search'] ) ? $_GET['nama-kk-search'] : '';
    $no_rm_kk_search = isset( $_GET['no-rm-kk-search'] ) ? $_GET['no-rm-kk-search'] : '';
    
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
        
            #mulai mencari data
            $get_data = $show_data->result( );
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>crai data rm</title>
         
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <style>
        input{
            display: block;
            margin: 7px 0
        }
        #input-main-search{
            display: inline
        }
        table {
            margin-top: 5px;
            padding: 5px;
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>
<body>
    <p>Cari Data Rekam Medis</p>
    <form action="" method="get">
        <input type="search" name="main-search" id="input-main-search" placeholder="cari data rm" autofocus value="<?= $main_search ?>">
        <button type="submit" name="submit">Cari</button>
        <button type="reset" id="reset-btn">Batal</button>
        <input type="date" name="tgl-search" id="input-tgl-search" data-date-format="DD MMMM YYYY" value="<?= (isset($_GET['tgl-search'])) ? $_GET['tgl-search'] : '' ?>">
        <input type="text" name="alamat-search" id="input-alamat-seacrh" placeholder="cari alamat" value="<?= $alamat_search ?>">
        <input type="text" name="no-rt-search" id="input-no-rt-search" placeholder="cari almat rt" value="<?= $no_rt_search ?>">
        <input type="text" name="no-rw-search" id="input-no-rw-search" placeholder="cari alamat rw" value="<?= $no_rw_search ?>">
        <input type="text" name="nama-kk-search" id="input-nama-kk-search" placeholder="cari nama kk" value="<?= $nama_kk_search ?>">
        <input type="text" name="no-rm-kk-search" id="input-no-rm-kk" placeholder="cari nomor rm kk" value="<?= $no_rm_kk_search ?>">
    </form>
    <div>
    <?php if ( $get_data ): ?>
        <p>Result: </p>
        <table border="1">
            <tr>
                <th>No.</th>
                <th>No RM</th>
                <th>Nama</th>
                <th>Tanggal Lahir</th>
                <th>Alamat</th>
                <th>RT / RW</th>
                <th>Nama KK</th>
                <th>No. Rm KK</th>
                <th>Action</th>
            </tr>                         
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
                <th><a href="/p/med-rec/edit-rm/index.php?document_id=<?= $data['id']?>">edit</a></th>
            </tr>                       
            <?php $idnum++; ?>
        <?php endforeach ; ?>
        </table>
    <?php else : ?>
        <p>gagal memuat data</p>
    <?php endif; ?>
    </div>
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
