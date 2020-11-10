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
            <p>Login SIMPUS</p>
            <?php if(! $content->session_bane_fase ) :?>
            <?='<script>console.log("'. $content->stat_bane . '")</script>'?>
            <form action="" method="post">  
                <div class="body">
                    <div class="form-groub">
                        <input type="text" name="userName" id="userName-input" required placeholder="username" value="<?= $content->user_name ?>" maxlength="32" tabindex="1" autocomplete="off">
                    </div>   
                    <div class="form-groub">                
                        <input type="password" name="password" id="password-input" required placeholder="Password" tabindex="2">
                    </div>    
                </div> 
                <div class="footer">
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
</body>
</html>
