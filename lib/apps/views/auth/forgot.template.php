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
        .white-space{
            width: 100%;
            height: 30px;
            display: block;
            margin-bottom: 7px;      
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
            <p>Buat Ulang Password</p>
            <form id="form-forgot" action="" method="post">
                <div class="body">
                    <div class="form-groub">
                        <input type="text" name="validate" id="validate-input" required placeholder="masukan 6 digit kode keamanan" maxlength="6">
                    </div>
                    <div class="white-space">
                    </div>
                    
                    <div class="form-groub">
                        <input type="password" name="password" id="password-input" required placeholder="new password">
                    </div>
                    <div class="form-groub">
                        <input type="password" name="password2" id="password2-input" required placeholder="confirm password">               
                    </div>
                </div>
                <div class="footer">
                    <button type="submit" name="reset">reset password</button>
                </div>
            </form>
        </div>
    </main>
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
