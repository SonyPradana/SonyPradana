<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reset password</title>
    <meta name="description" content="sisteminformasi kesehatan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/admin.style.css">
    <style>
        .gg-lastpass{box-sizing:border-box;position:relative;display:block;transform:scale(var(--ggs,1));width:20px;height:12px}.gg-lastpass::after,.gg-lastpass::before{content:"";display:block;box-sizing:border-box;position:absolute;background:currentColor;border-radius:22px}.gg-lastpass::before{width:4px;height:4px;box-shadow:6px 0 0,12px 0 0;top:4px}.gg-lastpass::after{width:2px;height:12px;right:0}
    </style>
</head>
<body>
    <main class="container">
        <div class="boxs">
            <div class="logo">
                <img src="/data/img/logo/logo-puskesmas.png" alt="logo" width="60px" height="60px">
            </div>
            <h1>Reset password</h1>
            <h1><?= $content->display_name ?></h1>
            <form id="form-reset" action="" method="post">
                <div class="body-form">
                    <div class="form-groub">
                        <i class="gg-lastpass"></i>
                        <input type="password" name="password" id="password-input" required placeholder="curent password">
                    </div>

                    <div class="form-groub">
                        <i class="gg-lastpass"></i>
                        <input type="password" name="password2" id="password2-input" required placeholder="new password">
                    </div>

                    <div class="form-groub">
                        <i class="gg-lastpass"></i>
                        <input type="password" name="password3" id="password3-input" required placeholder="confirm password">
                    </div>
                </div>
                <div class="footer-form">
                    <button type="submit" name="reset">reset password</button>
                <?php if( isset($content->message) ) : ?>
                    <p style="color:red"><?= $content->message ?></p>
                <?php endif ;?>
                </div>
            </form>
        </div>
    </main>
    <footer>
        <div class="footer-box">
            <a href="/">Home Page</a>
        </div>
    </footer>
</body>
<script>
    document.getElementById('form-reset').onsubmit = function(e) {
        var password = document.getElementById('password-input').value;
        var password_new = document.getElementById('password2-input').value;
        var password_confirm = document.getElementById('password3-input').value;

        // validation lenght password
        if (password.length < 8 || password.length > 100 ||
        password_new.length < 8 || password_new.length > 100 ||
        password_confirm.length < 8 || password_confirm.length > 100) {
            console.log('invalid passowrd');
            e.preventDefault();
            return false;
        }
        // validation confirm password
        if (password_new != password_confirm ) {
            console.log('invalid confirm password');
            e.preventDefault();
            return false;
        }
    }
</script>
</html>
