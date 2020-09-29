<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
    <style>
        .boxs-container{
            /* width: 100%; height: 100%; */
            display: grid;
            grid-template-columns: 1fr 24px 1fr;
        }
        .box.right { padding: 8px 16px }

        /* mobile */
        @media screen and (max-width: 740px) {            
            .boxs-container { grid-template-columns: 1fr }
            .box.right { padding: 5px }
        }
        /* tab */
        .tab{
            display: none;
        }

        /* form design */
        section{
            display: flex;
            flex-direction: column;
            margin: 8px 0px;
        }
        section label{
            margin-bottom: 4px;
        }

        /* btn goups */
        .btn-grup{
            margin-top: 16px;
            display: flex;
        }
        .gap-hori{
            max-width: 16px; min-width: 15px;
        }
    </style>
</head>
<body>
    <header>
        <?php include(BASEURL . '/lib/components/header/header.php'); ?>
    </header>
    <div id="modal">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    </div>

    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="/rekam-medis">KIA Anak</a></li>
                    <li>Edit Data</li>
                </ul>
            </div>
            <div class="boxs-container">
                <div class="box left">
                </div>
                <div class="gap"></div>
                <div class="box right">
                    <h1>Edit Data Posyandu</h1>
                    <form action="" method="post">
                        <section>
                            <label for="input-desa">Desa</label>
                            <select class="textbox outline black rounded small" name="desa" id="input-desa">
                                <!-- <option selected disabled hidden>Pilih Desa</option>  -->
                                <option <?= $content->desa_posyandu == 'bandarjo' ? 'selected' : '' ?> value="bandarjo">Bandarjo</option>
                                <option <?= $content->desa_posyandu == 'branjang' ? 'selected' : '' ?> value="branjang">Branjang</option>
                                <option <?= $content->desa_posyandu == 'kalisidi' ? 'selected' : '' ?> value="kalisidi">Kalisidi</option>
                                <option <?= $content->desa_posyandu == 'keji' ? 'selected' : '' ?> value="keji">Keji</option>
                                <option <?= $content->desa_posyandu == 'lerep' ? 'selected' : '' ?> value="lerep">Lerep</option>
                                <option <?= $content->desa_posyandu == 'nyatnyono' ? 'selected' : '' ?> value="nyatnyono">Nyatnyono</option>
                            </select>
                        </section>
                        <section>
                            <label for="input-posyandu">Nama posyandu</label>
                            <select class="textbox outline black rounded small" name="tempat_pemeriksaan" id="input-posyandu">
                                <!-- <option selected disabled hidden>Pilih Jenis Kelamin</option> -->
                                <?php foreach( $content->groups_posyandu as $group_posyandu): ?>
                                    <option <?= $content->desa_posyandu == $group_posyandu['posyandu'] ? 'selected' : '' ?> value="<?= $group_posyandu['id'] ?>"><?= $group_posyandu['posyandu'] ?></option>                                    
                                <?php endforeach; ?>
                            </select>
                        </section>
                        <!-- data -->
                        <section>
                            <label for="input-tanggal-pemeriksaan">Tanggal Pemeriksaan</label>
                            <input class="textbox outline black rounded small" type="date" name="tanggal_pemeriksaan" id="input-tanggal-pemeriksaan" value="<?= $content->tanggal_pemeriksaan ?>">
                        </section>
                        <section>
                            <label for="input-tinggi-badan">Tinggi Badan</label>
                            <input class="textbox outline black rounded small" type="number" name="tinggi_badan" id="input-tinggi-badan" placeholder="Dalam satuan cm" required value="<?= $content->tinggi_pemeriksaan ?>">
                        </section>
                        <section>
                            <label for="input-berat-badan">Berat Badan</label>
                            <input class="textbox outline black rounded small" type="number" name="berat_badan" id="input-berat-badan" placeholder="Dalam satuan gram" required value="<?= $content->berat_pemeriksaan ?>">
                        </section>
                         
                        <div class="btn-grup">
                            <button id="btn-sumbit" class="btn rounded small blue fill" type="submit" name="request">Edit Data</button>
                            <div class="gap-hori"></div>
                            <button id="btn-cancel" class="btn rounded small red outline" type="button" name="batal" >Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <?php if( $portal['message']['show'] ) :?>
        <div class="snackbar <?= $portal['message']['type'] ?>">
            <div class="icon">
                <!-- css image -->
            </div>
            <div class="message">
                <?= $portal['message']['content'] ?>
            </div>
        </div>
    <?php endif; ?> 
    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>
</body>
<script src="/lib/js/index.end.js"></script>
<script>
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

    // pilih nama desa
    $id('input-desa').addEventListener('change', function(event){
        // load nama posyandu
        $id('input-posyandu').innerHTML = '<option selected disabled hidden>Pilih Nama Posyandu</option>';
        $json('/api/ver1.0/posyandu/grup-posyandu.json?desa=' + event.target.value)
            .then( json => {
                json.data.forEach(element => {
                    let creat_option = document.createElement('option');
                    creat_option.setAttribute('value', element.id);
                    creat_option.innerText = element.posyandu;

                    $id('input-posyandu').appendChild(creat_option)  ;
                });

                if( select !== null){
                    $id('input-posyandu').value = select;
                }
            })
    })
</script>
</html>
