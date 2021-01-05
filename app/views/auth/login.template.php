<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <meta name="description" content="sistem informasi kesehatan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/admin.style.css">
    <style>
        .gg-lastpass{box-sizing:border-box;position:relative;display:block;transform:scale(var(--ggs,1));width:20px;height:12px}.gg-lastpass::after,.gg-lastpass::before{content:"";display:block;box-sizing:border-box;position:absolute;background:currentColor;border-radius:22px}.gg-lastpass::before{width:4px;height:4px;box-shadow:6px 0 0,12px 0 0;top:4px}.gg-lastpass::after{width:2px;height:12px;right:0}.gg-user{display:block;transform:scale(var(--ggs,1));box-sizing:border-box;width:12px;height:18px}.gg-user::after,.gg-user::before{content:"";display:block;box-sizing:border-box;position:absolute;border:2px solid}.gg-user::before{width:8px;height:8px;border-radius:30px;top:0;left:2px}.gg-user::after{width:12px;height:9px;border-bottom:0;border-top-left-radius:3px;border-top-right-radius:3px;top:9px}
    </style>
</head>
<body>
    <main class="container">
        <div class="boxs">
            <div class="logo">
                <img src="/data/img/logo/logo-puskesmas.png" alt="logo" width="60px" height="60px">
            </div>
            <h1>Login SIMPUS</h1>
            <?php if(! $content->session_bane_fase ) :?>
            <?='<script>console.log("'. $content->stat_bane . '")</script>'?>
            <form action="" method="post">
                <div class="form-body">
                    <div class="form-groub">
                        <i class="gg-user"></i>
                        <input type="text" name="userName" id="userName-input" required placeholder="username" value="<?= $content->user_name ?>" maxlength="32" tabindex="1" autocomplete="off">
                    </div>
                    <div class="form-groub">
                        <i class="gg-lastpass"></i>
                        <input type="password" name="password" id="password-input" required placeholder="Password" tabindex="2">
                    </div>
                </div>
                <div class="form-footer">
                    <button type="submit" name="login" tabindex="3">Login</button>
                    <?= ( $content->validate_user_name == false ) ? '<p style="color: red;margin: 5px 0;">kombinasi username atau password tidak tepat</p>' : ''?>
                </div>
            </form>
            <?php else: ?>
            <?= '<p style="color:red;font-size=11px"> Anda sedang berda di bane fase : ' . ($content->exp_bane - time()) . '</p>'?>
            <?php endif; ?>
            <a href="/forgot/send">lupa kata sandi?</a>
        </div>
    </main>
    <footer>
        <div class="footer-box">
            <a href="/">Home Page</a>
        </div>
    </footer>
</body>
</html>
