<!DOCTYPE html>
<html lang="en">
<head>
<meta content="id" name="language">
    <?php include(APP_FULLPATH['component'] . 'meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
    <script src="/lib/js/controller/table-rm/index.js"></script>
    <style>
        body{
            transition: margin-left .3s;
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
            transition: .3s;
        }

        .container-filter{padding-left: 30px}
        a.closebtn {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 48px;
            color: #818181;
            display: block;
        }a:hover.closebtn {color: #f1f1f1;}

        .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
        }

        .form-filter,
        .input-groub{
            max-width: 250px;
        }

        .label{ min-width: 200px;}

        .input-groub{
            margin-bottom: 8px;
            min-width: 200px;
        }
        .button-groub{
            margin-top: 8px;
            min-width: 200px;
        }


        table { width: 100% }
    </style>
</head>
<body>
    <header>
        <?php include(APP_FULLPATH['component'] . 'header/header.php'); ?>
    </header>

    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="/rekam-medis">Rekam Medis</a></li>
                    <li>Lihat Data</li>
                </ul>
            </div>
            <h1>Lihat Data Rekam Medis</h1>
            <button class="btn outline blue rounded small block" id="btnFilter">Costume Filter</button>
        <?php if( $content->data_rm ): ?>
            <div class="boxs">
                <div class="box-left">
                    <a href="javascript:void(0)" class="closebtn">&times;</a>
                    <div class="container-filter">
                        <form action="" method="post" class="form-filter">
                            <div class="label label-Umur"><p>Umur:</p></div>
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
                            <div class="label label-alamat"><p>Alamat:</p></div>
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
                            <div class="label label-alamat"><p>Status:</p></div>
                            <div class="input-groub">
                                <input type="checkbox" name="filter-kk" id="input-kk">
                                <label for="input-kk">Kepala Keluarga</label>
                            </div>
                            <div class="label label-duplikat"><p>Duplikat Data:</p></div>
                            <div class="form-groub filter-alamat">
                                <div class="input-groub">
                                    <input type="radio" name="duplicate" id="input-duplicate-tgl" value="tanggal_lahir">
                                    <label for="input-duplicate-tgl">Tanggal Lahir</label>
                                </div>
                                <div class="input-groub">
                                    <input type="radio" name="duplicate" id="input-duplicate-alamat" value="alamat">
                                    <label for="input-duplicate-alamat">Alamat</label>
                                </div>
                                <div class="input-groub">
                                    <input type="radio" name="duplicate" id="input-duplicate-kk" value="nama_kk">
                                    <label for="input-duplicate-kk">Nama KK</label>
                                </div>
                            </div>
                        </form>
                            <div class="button-groub grub-control horizontal">
                                <button class="btn outline blue rounded small" name="submit" id="submit">Terapkan</button>
                                <div class="gap-space"><!-- helper --></div>
                                <button class="btn outline green rounded small" name="reset" id="reset">Reset</button>
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
                        <?php $idnum = (int) ($content->page * 25) - 24; ?>
                        <?php foreach( $content->data_rm as $data) :?>
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
                                <th><a class="link" href="/rekam-medis/edit?document_id=<?= $data['id']?>">edit</a><?= $data['nama_kk'] == $data['nama'] ? '<a class="link" href="/rekam-medis/search?submit=&no-rm-kk-search=' . $data['nomor_rm_kk']. '">view</a>' : ""?> </th>
                            </tr>
                            <?php $idnum++; ?>
                        <?php endforeach ; ?>
                        </tbody>
                    </table>
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
        <?php include(APP_FULLPATH['component'] . 'footer/footer.html') ?>
    </footer>

    <!-- hidden -->
    <div id="modal">
        <?php include(APP_FULLPATH['component'] . 'control/modal.html') ?>
    </div>
</body>
<script src="/lib/js/index.end.js"></script>
<script>
    //menampilkan/menyembunyikan panel
    let show_panel = false
    let btnFilter =  document.querySelector("#btnFilter");
    btnFilter.addEventListener("click", event => {
        show_panel = show_panel ? false : true
        togle_panel(show_panel)
    });
    let btnClose =  document.querySelector(".closebtn");
    btnClose.addEventListener("click", event => {
        show_panel = false
        togle_panel(show_panel)
    });
    function togle_panel(val){
        if( val ){
            if( mobile_view.matches ){
                document.querySelector(".box-left").style.width = "100%"
                document.querySelector('body').style.marginLeft = "100%"
            }else{
                document.querySelector(".box-left").style.width = "250px"
                document.querySelector('body').style.marginLeft = "250px"
            }
        }else{
            document.querySelector(".box-left").style.width = "0px"
            document.querySelector('body').style.marginLeft = "0px"
        }
    }
    // medai query
    let mobile_view = window.matchMedia("screen and (max-width: 479px)")

    //filter data menggunakan ajax
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
        _maks_page = <?= $content->max_page ?>;
        _search_query = '&all'
        render_pagination()
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
