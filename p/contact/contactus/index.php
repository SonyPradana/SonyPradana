<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
?>
<?php 
    #Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 2);
    $user = new User($auth->getUserName());
?>
<?php
    $cek_captcha = isset( $_SESSION['MathCaptcha_ContactUs'] ) ? $_SESSION['MathCaptcha_ContactUs'] : 'captcha';

    if( isset( $_POST['done'] ) 
    && isset( $_POST['mail'] )
    && isset( $_POST['message'] )
    && isset( $_POST['ampcaptcha'] ) ){        
        $sender = $_POST['mail'];
        $message = $_POST['message'];
        $regarding = $_POST['regarding'];
        $captcha = $_POST['ampcaptcha'];

        if( $cek_captcha == $_POST['ampcaptcha'] && is_numeric( $captcha )){
            $pesan = new ContactUs($sender, $message, $regarding);
            if( $pesan->kirimPesan() ){
                $msg = 'Terimakis atas perhatian Anda :)';
            }
        }else{
            $msg = 'Captcha yang Anda masukkan salah';
        }
    }

    $captcha = new MathCaptcha();
    $_SESSION['MathCaptcha_ContactUs'] = $captcha->ChaptaResult();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi kita</title>
    <meta name="description" content="Sistem Informasi Manajemen Puskesmas SIMPUS Lerep">
    <meta name="keywords" content="simpus lerep, puskesmas lerep, puskesmas, hubungi kami, contact us, kritik dan saran, masukan pasien, review pasien">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
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
        <?php $active_menu = 'home' ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.html') ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    
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
                        <option value="saran">Saran</option>
                        <option value="keluhan">Keluhan pelanggan</option>
                        <option value="sponsor">Tertarik dengan Kami</option>
                        <option value="bug">Masalah pada website</option>
                        <option value="other">Yang lainnya</option>
                    </select>
                    
                    <label for="input-message">Pesan</label>
                    <textarea name="message" id="input-message" cols="30" rows="10" class="textbox outline blue rounded small" placeholder="saran dari Anda"></textarea>
                    
                    <div class="grub-control horizontal">
                        <label for="input-ampcaptcha"><?= $captcha->ChaptaQuest() ?></label>
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
            window.location.href = "/p/auth/login/?url=<?= $_SERVER['REQUEST_URI'] ?>&logout=true"
        },
        () => {          
            // close fuction : just logout
            window.location.href = "/p/auth/logout/?url=<?= $_SERVER['REQUEST_URI'] ?>"
        }
    );

</script>
</html>
