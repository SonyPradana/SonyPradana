<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <meta name="description" content="lupa password sistem informasi kesehatan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep, forgot password, recovery passeord, lupa password">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.html') ?>
    <link rel="stylesheet" href="/lib/css/ui/v1.1/admin.style.css">
    <style>
        .gg-voicemail{box-sizing:border-box;position:relative;display:block;transform:scale(var(--ggs,1));width:12px;height:10px;border-bottom:2px solid}.gg-voicemail::after,.gg-voicemail::before{content:"";display:block;box-sizing:border-box;position:absolute;width:10px;height:10px;border:2px solid;border-radius:10px;top:0;left:-5px}.gg-voicemail::after{left:7px}
    </style>
</head>
<body>
    <main class="container">
        <div class="boxs">
            <div class="logo">
                <img src="/data/img/logo/logo-puskesmas.png" alt="logo" width="60px" height="60px">
            </div>
            <h1>Verifikasai Email Pemulih</h1>
            <?php if( $content->message == true )  :?>
                <p>Emali Verifikasi sudah dikirim</p>
            <?php else: ?>
            <form action="" method="post">  
                <div class="form-body">
                    <div class="form-groub">
                        <i class="gg-voicemail"></i>
                        <input type="email" name="email" id="input-email" required placeholder="masukan email terdaftar" autocomplete="off">
                    </div>   
                </div> 
                <div class="form-footer">            
                <button type="submit" name="submit">kirim</button>
                </div>
            </form>
            <?php endif;?>
        </div>
    </main>
    <footer>
        <div class="footer-box">
            <a href="/">Home Page</a>
        </div>
    </footer>
</body>
</html>
