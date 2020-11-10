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
<?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.html') ?>
       
    <style>
        body {
            background-color: #d2d6de;
        }
        main .container {
            background-color: #fff;    
            padding: 15px;        
            margin: 7% auto;
            width: 360px;
        }
        main .container .logo {
            margin-top: 10px
        }
        main .container .logo .center {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        main .container p{
            font-size: 24px;
            color: #666;
            text-align: center;
        }
        .form-groub{
            width: 100%;
            height: 30px;
            display: block;
            margin-bottom: 7px;      
            border: 1px solid #ccb8b8;
        } 
        input{
            width: 100%;
            max-width: 320px;
            margin: 6px 12px;
            padding: 0;
            border: 0;
            font-size: 14px
        }
        button{
            width: 100%;
            height: 32px;
        }

    </style>
</head>
<body>
    <main>
        <div class="container">
            <div class="logo">
                <img  class="center" src="/data/img/logo/logo-puskesmas.png" alt="logo" width="60px" height="60px">
            </div>
            <p>Reset password</p>
            <p><?= $content->display_name ?></p>
            <form id="form-reset" action="" method="post">                
                <div class="body">
                    <div class="form-groub">
                        <input type="password" name="password" id="password-input" required placeholder="curent password">
                    </div>

                    <div class="form-groub">
                        <input type="password" name="password2" id="password2-input" required placeholder="new password">
                    </div>

                    <div class="form-groub">
                        <input type="password" name="password3" id="password3-input" required placeholder="confirm password">
                    </div>
                </div>
                <div class="footer">
                    <button type="submit" name="reset">reset password</button>
                <?php if( isset($content->message) ) : ?>
                    <p style="color:red"><?= $content->message ?></p>    
                <?php endif ;?>
                </div>
            </form>
        </div>
    </main>
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
