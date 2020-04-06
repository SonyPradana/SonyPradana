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

    #config
    $sort = 'nomor_rm';
    $order = 'ASC';
    $page = isset( $_GET['page'] ) ? $_GET['page'] : 1;
    $page = is_numeric($page) ? $page : 1;

    # ambil data
    $show_data = new View_RM();
    $show_data->sortUsing($sort);
    $show_data->orderUsing($order);
    $show_data->limitView(25);
    $max_page = $show_data->maxPage();
    $page = $page > $max_page ? $max_page : $page;
    $show_data->currentPage($page);
    $get_data = $show_data->resultAll();
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
<?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/style-main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/pagination.css">
    <style>
        button{
            margin: 7px 0;
        }
        .boxs{
            width: 100%;
        }
        .box-right{
            width: 100%;
            overflow-x: auto;
        }
    
        .box-left{
            height: 100%; 
            width: 0; 
            position: fixed;
            z-index: 1; 
            top: 0; 
            left: 0;
            background-color: #23303d;
            color: #fff;
            overflow-x: hidden; 
            padding-top: 60px; 
            transition: 0.2s; 
        }

        .container-filter{padding-left: 30px}
        a.closebtn {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 50px;
            color: #818181;
            display: block;
        }a:hover.closebtn {color: #f1f1f1;}

        .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
        }
        
        .input-groub{margin-bottom: 8px}
    </style>
    <script src="/lib/js/ajax/html/GetData.js"></script>
</head>
<body>
    <header>
        <?php $active_menu = 'lihat data' ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/header.html') ?>
    </header>
    <main>
        <div class="container">
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Lihat Data</li>
                </ul>
            </div>
            <h1>Lihat Data Rekam Medis</h1>
            <button id="btnFilter">Costume Filter</button>
        <?php if ( $get_data ): ?>
            <div class="boxs">
                <div class="box-left">
                    <a href="javascript:void(0)" class="closebtn">&times;</a>
                    <div class="container-filter">
                        <form action="" method="post" class="form-filter">
                            <div class="label-Umur"><p>Umur:</p></div>
                            <div class="form-groub">
                                <select id="input-umur" name="filter-umur">                            
                                    <option value="0-100">--</option>
                                    <option value="0-5">0-5</option>
                                    <option value="5-10">5-10</option>
                                    <option value="10-15">10-15</option>
                                    <option value="15-20">15-20</option>
                                    <option value="20-25">20-25</option>
                                    <option value="25-30">25-30</option>
                                    <option value="35-40">35-40</option>
                                    <option value="45-50">45-50</option>
                                    <option value="50-55">50-55</option>                                    
                                    <option value="55-60">55-60</option>
                                    <option value="60-100">60-100</option>
                                </select>
                            </div>
                            <div class="label-alamat"><p>Alamat:</p></div>
                            <div class="form-groub filter-alamat">
                                <div class="input-groub">
                                    <input type="checkbox" name="filter-alamat-bandarjo" id="input-alamat-bandarjo">
                                    <label for="input-alamat-bandarjo">Bandarjo</label>
                                </div>
                                <div class="input-groub">
                                    <input type="checkbox" name="filter-alamat-branjang" id="input-alamat-branjang">
                                    <label for="input-alamat-branjang">Branjang</label>
                                </div>
                                <div class="input-groub">
                                    <input type="checkbox" name="filter-alamat-kalisidi" id="input-alamat-kalisidi">
                                    <label for="input-alamat-kalisidi">Kalisidi</label>
                                </div>
                                <div class="input-groub">
                                    <input type="checkbox" name="filter-alamat-keji" id="input-alamat-keji">
                                    <label for="input-alamat-keji">Keji</label>
                                </div>
                                <div class="input-groub">
                                    <input type="checkbox" name="filter-alamat-lerep" id="input-alamat-lerep">
                                    <label for="input-alamat-lerep">Lerep</label>
                                </div>
                                <div class="input-groub">
                                    <input type="checkbox" name="filter-alamat-nyatnyono" id="input-alamat-nyatnyono">
                                    <label for="input-alamat-nyatnyono">Nyatnyono</label>
                                </div>
                            </div>
                            <div class="label-alamat"><p>Status:</p></div>
                            <div class="input-groub">
                                <input type="checkbox" name="filter-kk" id="input-kk">
                                <label for="input-kk">Kepala Keluarga</label>
                            </div>
                        </form>
                            <div class="input-groub">
                                <button name="submit" class="submit">Terapkan</button>
                                <button name="reset">Reset</button>
                            </div>
                    </div>

                </div>
                <div class="box-right">
                    <!-- table -->
                    <table>                
                        <tr>
                            <th>No.</th>
                            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nomor_rm', <?= $order == 'ASC' && $sort == 'nomor_rm' ? "'DESC'" : "'ASC'" ?>, <?= $page ?>, [])">No RM</a></th>
                            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nama', <?= $order == 'ASC' && $sort == 'nama' ? "'DESC'" : "'ASC'" ?>, <?= $page ?>, [])">Nama</a></th>
                            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('tanggal_lahir', <?= $order == 'ASC' && $sort == 'tanggal_lahir' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, [])">Tanggal Lahir</a></th>
                            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('alamat', <?= $order == 'ASC' && $sort == 'alamat' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, [])">Alamat</a></th>
                            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nomor_rw',<?= $order == 'ASC' && $sort == 'nomor_rt' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, [])">RT / RW</a></th>
                            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nama_kk',<?= $order == 'ASC' && $sort == 'nama_kk' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, [])">Nama KK</a></th>
                            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nomor_rm_kk', <?= $order == 'ASC' && $sort == 'nomor_rm_kk' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, [])">No. Rm KK</a></th>
                            <th><a href="javascript:void(0)">Action</a></th>
                        </tr>                         
                    <?php $idnum = (int) ($page * 25) - 24; ?>
                    <?php foreach( $get_data as $data) :?>            
                        <tr>       
                            <th><?= $idnum ?></th>
                            <th><?= $data['nomor_rm']?></th>
                            <th><?= ucwords( $data['nama'] )?></th>
                            <th><?= date("d-m-Y", strtotime( $data['tanggal_lahir']))  ?></th>
                            <th><?= ucwords( $data['alamat'] )?></th>
                            <th><?= $data['nomor_rt'] . ' / ' . $data['nomor_rw']?></th>
                            <th <?= $data['nama_kk'] == $data['nama'] ? 'class="mark"' : ""?>><?= ucwords( $data['nama_kk'] )?></th>
                            <th><?= $data['nomor_rm_kk']?></th>
                            <th><a class="link" href="/p/med-rec/edit-rm/index.php?document_id=<?= $data['id']?>">edit</a><?= $data['nama_kk'] == $data['nama'] ? '<a class="link" href="/p/med-rec/search-rm/?submit=&no-rm-kk-search=' . $data['nomor_rm_kk']. '">view</a>' : ""?> </th>
                        </tr>                       
                        <?php $idnum++; ?>
                    <?php endforeach ; ?>
                    </table>
                    <div class="box-pagination">
                        <div class="pagination">
                            <?php if( $page > 0  ):?>
                                <a href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $page -1 ?>, [])">&laquo;</a>
                            <?php endif;?>                            
                            <?php if( $max_page > 5 ):?>
                                <!-- satu depan -->
                                <a <?= 1 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', 1, [])">1</a>
                                <!-- tiga tengah -->                                
                                <?php if( $page  > 2 && $page < ($max_page - 1) ):?>
                                    <a href="javascript:void(0)" class="sperator">...</a>
                                    <a href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $page - 1 ?>, [])"><?= $page - 1 ?></a>
                                    <a class="active" href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $page ?>, [])"><?= $page ?></a>
                                    <a href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $page + 1 ?>, [])"><?= $page + 1 ?></a>
                                    <a href="javascript:void(0)" class="sperator">...</a>
                                <?php elseif( $page < 4 ):?> 
                                    <a <?= 2 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', 2, [])">2</a>
                                    <a <?= 3 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', 3, [])">3</a>
                                    <a href="javascript:void(0)" class="sperator">...</a>
                                <?php elseif( $page > ($max_page - 2) ):?>  
                                    <a href="javascript:void(0)" class="sperator">...</a>
                                    <a <?= $max_page - 2 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $max_page - 2 ?>, [])"><?= $max_page - 2 ?></a>
                                    <a <?= $max_page - 1 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $max_page - 1 ?>, [])"><?= $max_page -1 ?></a>
                                <?php endif;?>  
                                <!-- satu belakang -->
                                <a href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $max_page ?>, [])"><?= $max_page ?></a>
                            <?php elseif( $max_page < 6 ):?>
                                <?php for ($i=1; $i <= $max_page; $i++) :?>
                                    <a <?= $i == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $i ?>, [])"><?= $i ?></a>
                                <?php endfor;?>
                            <?php endif;?>  
                            <?php if( $page < $max_page ):?>  
                                <a href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $page +1 ?>, [])">&raquo;</a>
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <p>gagal memuat data</p>
        <?php endif; ?>
        </div>
    </main>
    <footer>
        <div class="line"></div>
        <p class="big-footer">SIMPUS LEREP</p>
        <p class="note-footer">creat by <a href="https://twitter.com/AnggerMPd">amp</a></p>
        <div class="box"></div>
    </footer>
</body>
<script>
    //menampilkan/menyembunyikan panel
    var btnFilter =  document.querySelector("#btnFilter");
    btnFilter.addEventListener("click", event => {
        var boxLeft = document.querySelector(".box-left");
        boxLeft.style.width = "250px";
    });
    var btnFilter =  document.querySelector(".closebtn");
    btnFilter.addEventListener("click", event => {
        var boxLeft = document.querySelector(".box-left");
        boxLeft.style.width = "0px";
    });

    //filter data menggunakan xhr
    var btnTerapkan  = document.querySelector('.submit');
    //event handler
    btnTerapkan.addEventListener("click", event=> {
        //get data
        var formGET = document.querySelector('.form-filter');
        var formData = new FormData( formGET );
        var rangeUmur = formData.get('filter-umur');
        var cbandarjo = formData.get('filter-alamat-bandarjo');
        var cbranjang = formData.get('filter-alamat-branjang');
        var cKalisidi = formData.get('filter-alamat-kalisidi');
        var cKeji = formData.get('filter-alamat-keji');
        var cLerep = formData.get('filter-alamat-lerep');
        var cNyatnyono = formData.get('filter-alamat-nyatnyono');
        var cStatusKK = formData.get('filter-kk');

        var filters = [rangeUmur, cbandarjo, cbranjang, cKalisidi, cKeji, cLerep, cNyatnyono, cStatusKK];
        GDcostumeFilter('alamat', 'asc', <?= $page ?>, filters);
    });
</script>
</html>
