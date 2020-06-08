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

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/pagination.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <script src="/lib/js/controller/table-rm/index.js"></script>
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
        .box-right p.info{ display: none;}
    
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

        table { width: 100% }
    </style>
</head>
<body>
    <header>
        <?php $active_menu = 'lihat data' ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.html') ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Lihat Data</li>
                </ul>
            </div>
            <h1>Lihat Data Rekam Medis</h1>
            <button class="btn outline blue rounded small block" id="btnFilter">Costume Filter</button>
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
                            <div class="label-alamat"><p>Duplikat Data:</p></div>
                            <div class="form-groub filter-alamat">
                                <div class="input-groub">
                                    <input type="radio" name="duplicate" id="input-duplicate-tgl" value="tanggal_lahir">
                                    <label for="input-duplicate-tgl">Duplikat Tanggal Lahir</label>
                                </div>
                                <div class="input-groub">
                                    <input type="radio" name="duplicate" id="input-duplicate-alamat" value="alamat">
                                    <label for="input-duplicate-alamat">Duplikat Alamat</label>
                                </div>
                                <div class="input-groub">
                                    <input type="radio" name="duplicate" id="input-duplicate-kk" value="nama_kk">
                                    <label for="input-duplicate-kk">Duplikat Nama KK</label>
                                </div>
                            </div>
                        </form>
                            <div class="input-groub">
                                <button class="btn outline blue rounded small" name="submit" id="submit">Terapkan</button>
                                <button class="btn outline blue rounded small" name="reset" id="reset">Reset</button>
                            </div>
                    </div>

                </div>
                <div class="box-right">
                    <!-- table -->
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
                        <?php $idnum = (int) ($page * 25) - 24; ?>
                        <?php foreach( $get_data as $data) :?>            
                            <tr>       
                                <th><?= $idnum ?></th>
                                <th><?= $data['nomor_rm']?></th>
                                <th><?= ucwords( $data['nama'] )?></th>
                                <?php $new_date = date("d-m-Y", strtotime( $data['tanggal_lahir'])) ?>
                                <th><?= $new_date == '01-01-1970' ? '00-00-0000' : $new_date ?></th>
                                <th><?= ucwords( $data['alamat'] )?></th>
                                <th><?= $data['nomor_rt'] . ' / ' . $data['nomor_rw']?></th>
                                <th <?= $data['nama_kk'] == $data['nama'] ? 'class="mark"' : ""?>><?= ucwords( $data['nama_kk'] )?></th>
                                <th><?= $data['nomor_rm_kk']?></th>
                                <th><a class="link" href="/p/med-rec/edit-rm/index.php?document_id=<?= $data['id']?>">edit</a><?= $data['nama_kk'] == $data['nama'] ? '<a class="link" href="/p/med-rec/search-rm/?submit=&no-rm-kk-search=' . $data['nomor_rm_kk']. '">view</a>' : ""?> </th>
                            </tr>                       
                            <?php $idnum++; ?>
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
        <?php else : ?>
            <p>gagal memuat data</p>
        <?php endif; ?>
        </main>
    </div>
    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>
</body>
<script src="/lib/js/index.end.js"></script>
<script>
    let boxLeft = document.querySelector(".box-left");
    //menampilkan/menyembunyikan panel
    let btnFilter =  document.querySelector("#btnFilter");
    btnFilter.addEventListener("click", event => {
        boxLeft.style.width = "250px";
    });
    let btnClose =  document.querySelector(".closebtn");
    btnClose.addEventListener("click", event => {
        boxLeft.style.width = "0px";
    });

    //filter data menggunakan xhr
    let btnTerapkan  = document.querySelector('#submit');
    btnTerapkan.addEventListener("click", event=> {
        //get data
        let formGET = document.querySelector('.form-filter');
        let formData = new FormData( formGET );
        let rangeUmur = formData.get('filter-umur');
        let cbandarjo = formData.get('filter-alamat-bandarjo');
        let cbranjang = formData.get('filter-alamat-branjang');
        let cKalisidi = formData.get('filter-alamat-kalisidi');
        let cKeji = formData.get('filter-alamat-keji');
        let cLerep = formData.get('filter-alamat-lerep');
        let cNyatnyono = formData.get('filter-alamat-nyatnyono');
        let cStatusKK = formData.get('filter-kk');

        let q = ''
        if( cbandarjo == 'on'){ q += 'bandarjo-' }
        if( cbranjang == 'on'){ q += 'branjang-' }
        if( cKalisidi == 'on'){ q += 'kalisidi-' }
        if( cKeji == 'on'){ q += 'keji-' }
        if( cLerep == 'on'){ q += 'lerep-' }
        if( cNyatnyono == 'on'){ q += 'nyatnyono' }
        
        // duplicate data
        let dupl = document.querySelector('input[name="duplicate"]:checked')

        if( rangeUmur == '0-100' && 
        cbandarjo == null && cbranjang == null && cKalisidi == null &&
        cKeji == null && cLerep == null && cNyatnyono == null &&
        cStatusKK == null && dupl == null){
            _search_query = '&all'
        }else{                
            let query_desa = q == '' ? '' : `&desa=${q}`
            let query = `&umur=${rangeUmur}${query_desa}&status_kk=${cStatusKK}`
            query = dupl == null ? query : `${query}&duplicate=${dupl.value}`
            _search_query = query
        }

        getData(_sort, _order, _cure_page, _search_query)
    });

    let btnReset = document.querySelector('#reset');
    btnReset.addEventListener("click", event=> {
        document.querySelector(".form-filter").reset(); 
    })

    // onload
    window.addEventListener('load', () => {
        table_type = "view"
        _maks_page = <?= $max_page ?>;
        _search_query = '&all'
        render_pagination()
    })

    // sticky header
    window.onscroll = function(){
            stickyHeader('.container', '82px', '32px')
    }

    // keep alive
    keepalive(() => {
        window.location.href = "/p/auth/login/"
    })
</script>
</html>
