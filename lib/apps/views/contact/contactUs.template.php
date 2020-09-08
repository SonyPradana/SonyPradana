<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <style>
        h1, h2{
            margin: 4px !important;
        }
        boxs h2{font-family: 18px !important}

        .container{                
            margin: 32px 56px;
            padding: 20px;
            min-height: 300px;
            background-color: white;
            display: grid;
            grid-template-columns: 1fr 400px;
        }
        form{
            min-width: 200px;
            max-width: 400px;

            display: flex;
            flex-direction: column;
        }
        form label,
        form button{
            margin-top: 8px; margin-bottom: 4px;
            min-width: 80px;
            max-width: 150px;
        }
        form .grub-control{
            margin-top: 8px;
        }

        input, label, select, textarea{font-size: 1rem}
        #input-ampcaptcha{ width: 100px }

        #input-submit{ margin-top: 12px }

        /* mobile */
        @media screen and (max-width: 479px) {
            .container{
                grid-template-columns: 1fr
            }
            main{
                max-width: 478px;
                min-width: 200px;
            }
            form{
                overflow-x: auto;
            }
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

</script>
</html>
