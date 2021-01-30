<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot password</title>
    <meta name="description" content="sisteminformasi kesehatan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">
<?php include(APP_FULLPATH['component'] . 'meta/metatag.html') ?>

    <style>
        .gg-dialpad{transform:scale(var(--ggs,1));}.gg-dialpad,.gg-dialpad::before{box-shadow:-5px 0 0,5px 0 0}.gg-dialpad,.gg-dialpad::after,.gg-dialpad::before{box-sizing:border-box;position:relative;display:block;width:3px;height:3px;background:currentColor}.gg-dialpad::after,.gg-dialpad::before{content:"";position:absolute;left:0;}.gg-dialpad::before{bottom:5px}.gg-dialpad::after{box-shadow:-5px 0 0,5px 0 0,0 5px 0;top:5px}.gg-lastpass{box-sizing:border-box;position:relative;display:block;transform:scale(var(--ggs,1));width:20px;height:12px}.gg-lastpass::after,.gg-lastpass::before{content:"";display:block;box-sizing:border-box;position:absolute;background:currentColor;border-radius:22px}.gg-lastpass::before{width:4px;height:4px;box-shadow:6px 0 0,12px 0 0;top:4px}.gg-lastpass::after{width:2px;height:12px;right:0}

        html, body {
            background-color: #d9d9d9;
            height: calc(100% - 50px);
            margin: 0;
            padding: 0;
            display: grid;
            justify-content: center;
            top: 50px;
            position: relative;
        }
        main .boxs {
            background-color: #fff;
            padding: 15px;
            width: 360px;
        }
        .boxs .logo {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .boxs h1{
            margin: 28px;
            padding: 0;
            font-size: 24px;
            color: #666;
            text-align: center;
        }
        .white-space{
            width: 100%;
            height: 30px;
            display: block;
            margin-bottom: 7px;
        }
        .form-groub{
            width: 100%;
            margin-bottom: 7px;
            border: 1px solid #ccb8b8;
            display: flex;
            align-items: center;
        }
        i {
            margin-left: 8px;
            max-width: 10px;
            max-height: 16px;
        }
        input{
            width: 100%;
            max-width: 320px;
            margin: 6px 12px;
            padding: 0;
            border: 0;
            font-size: 16px
        }
        input:focus {
            box-shadow: none;
            border: none;
            outline: none;
        }
        input:required {
            box-shadow:none;
        }
        input:invalid {
            box-shadow: none
        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        input[type=number] {
            -moz-appearance: textfield;
        }
        button{
            width: 100%;
            height: 32px;
        }
        footer {
            display: flex;
            justify-content: center;
            align-self: end;
            margin: 12px
        }

    </style>
</head>
<body>
    <main class="container">
        <div class="boxs">
            <div class="logo">
                <img src="/data/img/logo/logo-puskesmas.png" alt="logo" width="60px" height="60px">
            </div>
            <h1>Buat Ulang Password</h1>
            <form id="form-forgot" action="" method="post">
                <div class="form-body">
                    <div class="form-groub">
                        <i class="gg-dialpad"></i>
                        <input type="number" name="validate" id="validate-input" required placeholder="masukan 6 digit kode keamanan" maxlength="6" tabindex="1">
                    </div>
                    <div class="white-space">
                    </div>

                    <div class="form-groub">
                        <i class="gg-lastpass"></i>
                        <input type="password" name="password" id="password-input" autocomplete="off" required placeholder="new password" tabindex="2">
                    </div>
                    <div class="form-groub">
                        <i class="gg-lastpass"></i>
                        <input type="password" name="password2" id="password2-input" required placeholder="confirm password" tabindex="3">
                    </div>
                </div>
                <div class="form-footer">
                    <button type="submit" name="reset" tabindex="4">reset password</button>
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
    document.getElementById('form-forgot').onsubmit = function(e) {
        var validate = document.getElementById('validate-input').value;
        var password = document.getElementById('password-input').value;
        var password_confirm = document.getElementById('password2-input').value;

        if (! Number.isInteger(validate)) {
            console.log('invalid validation number');
            e.preventDefault();
            return false;
        }

        // validation lenght
        if (validate.length != 6 || password.length < 8 || password.length > 100 ||
        password_confirm.length < 8 || password_confirm.length > 100) {
            console.log('invalid input');
            e.preventDefault();
            return false;
        }
        // validation confirm password
        if (password != password_confirm ) {
            console.log('invalid confirm password');
            e.preventDefault();
            return false;
        }
    }
</script>
</html>
