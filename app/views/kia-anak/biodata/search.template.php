<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">

    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
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

        input[type=text] {
          min-width: 100px;
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
        <?php include(BASEURL . '/resources/components/header/header.php'); ?>
    </header>

    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="/rekam-medis">KIA Anak</a></li>
                    <li>Cari Data</li>
                </ul>
            </div>
            <h1>Cari Data KIA Anak</h1>
            <div class="boxs">
                <div class="box left">
                    <form action="" method="get" class="search-box">
                        <input class="textbox outline black rounded small block" type="text" name="main-search" id="input-main-search" placeholder="cari nama" value="<?= $content->nama ?>" <?= $portal['DNT'] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <div class="grub-control horizontal right">
                            <button class="btn rounded light blue outline" type="submit" id="submit">Cari</button>
                            <div class="gap-space"><!-- helper --></div>
                            <button class="btn rounded light red outline" type="reset" id="reset-btn">Batal</button>
                        </div>
                        <div class="grub-control horizontal">
                            <input type="checkbox" name="strict-search" id="input-strict-search" <?= $content->strict == true ? "checked" : ""?>>
                            <label for="input-strict-search">Pencarian Mendalam</label>
                        </div>
                        <input class="textbox outline black rounded small block" type="text" name="alamat-search" id="input-alamat-seacrh" placeholder="cari alamat" value="<?= $content->alamat ?>" <?= $portal['DNT'] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <div class="grub-control horizontal">
                            <input class="textbox outline black rounded small block" type="text" name="no-rt-search" id="input-no-rt-search" placeholder="cari alamat rt" value="<?= $content->nomor_rt ?>">
                            <div class="gap-space"><!-- helper --></div>
                            <div class="gap-space"><!-- helper --></div>
                            <input class="textbox outline black rounded small block" type="text" name="no-rw-search" id="input-no-rw-search" placeholder="cari alamat rw" value="<?= $content->nomor_rw ?>">
                        </div>
                        <input class="textbox outline black rounded small block" type="text" name="nama-kk-search" id="input-nama-kk-search" placeholder="cari nama orang tua" value="<?= $content->nama_kk ?>" <?= $portal['DNT'] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>

                        <p>Grup Posyandu</p>
                        <select class="textbox outline black rounded small block" name="desa" id="input-desa">
                            <option <?= $content->desa == null ? 'selected' : null ?> disabled hidden>Pilih Desa</option>
                            <option <?= $content->desa == 'bandarjo' ? 'selected' : null ?> value="bandarjo">Bandarjo</option>
                            <option <?= $content->desa == 'branjang' ? 'selected' : null ?> value="branjang">Branjang</option>
                            <option <?= $content->desa == 'kalisidi' ? 'selected' : null ?> value="kalisidi">Kalisidi</option>
                            <option <?= $content->desa == 'keji' ? 'selected' : null ?> value="keji">Keji</option>
                            <option <?= $content->desa == 'lerep' ? 'selected' : null ?> value="lerep">Lerep</option>
                            <option <?= $content->desa == 'nyatnyono' ? 'selected' : null ?> value="nyatnyono">Nyatnyono</option>
                        </select>
                        <select class="textbox outline black rounded small block" name="tempat_pemeriksaan" id="input-posyandu">
                            <?php if( $content->posyandu_exist ): ?>
                                <option disabled hidden>Pilih Jenis Kelamin</option>
                                <?php foreach( $content->$groups_posyandu as $key => $val): ?>
                                    <option <?= $content->posyandu == $val['posyandu'] ? 'selected' : null ?>><?= ($val['posyandu']) ?></option>
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
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/footer/footer.html') ?>
    </footer>

    <!-- hidden -->
    <div id="modal">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/control/modal.html') ?>
    </div>
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
            window.location.href = "/login?url=<?= $_SERVER['REQUEST_URI'] ?>&logout=true"
        },
        () => {
            // close fuction : just logout
            window.location.href = "/logout?url=<?= $_SERVER['REQUEST_URI'] ?>"
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
        $json('/api/ver1.0/posyandu/grup-posyandu.json?desa=' + e.target.value)
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
