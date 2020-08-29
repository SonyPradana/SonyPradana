<!DOCTYPE html>
<html lang="en">
<head> 
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <style>
        
        .boxs{
            width: 100%; height: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        .box.right { padding: 8px 16px }
        .input-information p,
        .input-information p a,
        p.dusun{
            margin: 0;
            color: #7f6cff;
        }

        form { max-width: 500px }
        form > input:not(:first-child),
        form > button,
        .grub-control.horizontal{
            margin-top: 12px
        }
        form > input { width: 100% }
        .grub-control.horizontal > .textbox{
            width: 100px;
        }

        /* mobile */
        @media screen and (max-width: 600px) {            
            .boxs { grid-template-columns: 1fr }
            .box.right { padding: 5px }
        }
    </style>
</head>
<body>
    <header>
        <?php include(BASEURL . '/lib/components/header/header.php'); ?>
    </header>
    <div id="modal">
        <?php include(BASEURL . '/lib/components/control/modal.html') ?>
    </div>

    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="/rekam-medis">Rekam Medis</a></li>
                    <li>Edit Data Rekam Medis</li>
                </ul>
            </div>
            <div class="boxs">
                <div class="box left"></div>
                <div class="box right">
                <?php if( $portal['message']['show'] ) :?>
                    <p style="color: red"><?= $portal['message']['content'] ?></p>
                    <a href="<?= $portal['redirect_to'] ?>">kembali ke menu utama</a>
                <?php else:?>
                    <h1>Edit data Rekam Medis</h1>
                    <form action="" method="post">
                        <input class="textbox outline black rounded small block" type="text" name="nomor_rm" id="input-nomor-rm" placeholder="nomor rekam medis" value="<?= $content->nomor_rm ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*">                        
                        <div class="input-information warning">
                        <?php if( $content->status_double ) : ?>
                            <p>nomor rekam medis sama :
                                <a href="/rekam-medis/search?nomor-rm-search=<?= $content->nomor_rm ?>"
                                    target="_blank">lihat</a>
                            </p>
                        <?php endif; ?>
                        </div>
                        <input class="textbox outline black rounded small block" type="text" name="nama" id="input-nama" placeholder="nama" value="<?= $content->nama ?>" maxlength="50" <?= $portal["DNT"] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <input class="textbox outline black rounded small block" type="date" name="tgl_lahir" id="input-tgl-lahir" value="<?= $content->tanggal_lahir ?>">
                        <input class="textbox outline black rounded small block" type="text" name="alamat" id="input-alamat" placeholder="alamat tanpa rt/rw" value="<?= $content->alamat?>" <?= $portal["DNT"] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <div class="grub-control horizontal">
                            <input class="textbox outline black rounded small" type="text" name="nomor_rt" id="input-nomor-rt" placeholder="nomor rt" max="2" value="<?= $content->nomor_rt ?>" inputmode="numeric" pattern="[0-9]*">
                            <div class="gap-space"><!-- helper --></div>
                            <input class="textbox outline black rounded small" type="text" name="nomor_rw" id="input-nomor-rw" placeholder="nomor rw" max="2" value="<?= $content->nomor_rw ?>" inputmode="numeric" pattern="[0-9]*">
                            <div class="gap-space"><!-- helper --></div>
                            <p class="dusun"></p>
                        </div>
                        <div class="grub-control horizontal">
                            <input type="checkbox" name="tandai_sebagai_kk" id="input-mark-as-kk" tabindex="11" <?= $content->status_kk == true ? "checked" : ""?>>
                            <label for="input-mark-as-kk">Tandai sebagai kk</label>
                        </div>  
                        <input class="textbox outline black rounded small block" type="text" name="nama_kk" id="input-nama-kk" placeholder="nama kepala keluarga" value="<?= $content->nama_kk ?>" <?= $portal["DNT"] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
                        <input class="textbox outline black rounded small block" type="text" name="nomor_rm_kk" id="input-nomor-rm-kk" placeholder="nomor rm kepla keluarga" value="<?= $content->nomor_rm_kk ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*">
                        <div class="input-information no-rm-kk"></div>
                        <div class="input-information kk-sama"></div>
                    
                        <div class="grub-control horizontal">
                            <button class="btn rounded small blue outline" type="submit" name="submit">Edit Data RM</button>
                            <div class="gap-space"><!-- helper --></div>
                            <button class="btn rounded small red text" type="button" onclick="window.history.back()">Batal Perubahan</button>
                        </div>
                    </form>           
                <?php endif; ?>
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
<script src="/lib/js/controller/form-rm/index.js"></script>
<script src="/lib/js/index.end.js"></script>
<script>
        // onload
        window.addEventListener('load', () => {
            cekDesa()
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
