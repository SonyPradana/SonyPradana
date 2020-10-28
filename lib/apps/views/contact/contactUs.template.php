<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <link rel="stylesheet" href="/lib/css/pages/v1.1/contactus.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
</head>
<body>    
    <header>
        <?php include(BASEURL . '/lib/components/header/header.php'); ?>
    </header>
    
    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Hubung Kami</li>
                </ul>
            </div>
            <div class="boxs">
                <h1>Hubungi Kami</h1>
                <h2>kritik dan saran untuk kami</h2>
                <form action="" method="post">
                    <label for="input-email">Email</label>
                    <input type="email" name="mail" id="input-email" class="textbox outline blue rounded small" placeholder="email">

                    <label for="input-regarding">Regarding</label>
                    <select name="regarding" id="input-regarding" class="textbox outline blue rounded small">
                        <option hidden selected>-- pilih satu</option>
                        <option value="saran">Saran</option>
                        <option value="keluhan">Keluhan pelanggan</option>
                        <option value="sponsor">Tertarik dengan Kami</option>
                        <option value="bug">Masalah pada website</option>
                        <option value="other">Yang lainnya</option>
                    </select>
                    
                    <label for="input-message">Pesan</label>
                    <textarea name="message" id="input-message" cols="30" rows="10" class="textbox outline blue rounded small" placeholder="saran dari Anda"></textarea>
                    <p class="info">(Kerahasian Anda adalah yang utama)</p>
                    
                    <div class="grub-control horizontal">
                        <label for="input-ampcaptcha"><?= $content->captcha_quest ?></label>
                        <input type="text" name="ampcaptcha" id="input-ampcaptcha" class="textbox outline blue rounded small" placeholder="wajib diisi">
                    </div>

                    <button type="submit" id="input-submit" name="done" class="btn blue outline rounded normal">Kirim Pesan</button>
                </form>
            </div>
        </main>
        <aside class="right-side">

        </aside>
    </div>
    
    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>
    
    <!-- hidden -->
    <div id="modal">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
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

</script>
</html>
