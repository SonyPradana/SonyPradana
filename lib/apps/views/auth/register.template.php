<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Acunt Baru</title>
    <meta name="description" content="sistem informasi kesehatan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.html') ?>
       
    <style>
        body{background-color: #d2d6de;}
        form{
            padding: 0 20px;
        }
        input{
            display: block;
            margin-bottom: 10px;
            font-size: 17px;
        }        
        main .container {
            background-color: #fff;    
            padding: 15px;        
            margin: 7% auto;
            width: 60%;
            min-width: 320px;
            max-width: 900px;
            box-shadow: 0 4px 8px 0 #00000022, 0 6px 20px 0 #00000010;

            display: grid;
            grid-template-columns: 2fr 1fr;
        }
        .container .body.right p{
            font-size: 24px;
        }
        .container .body.left{
            border-left:  0.1px solid #ece9e9 ;
            padding: 10px;
        }
        .container .body.left .center{
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .container .body.left p{
            text-align: center;
            font-size: 20px
        }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <div class="body right">
                <p>Selamat Datang Di System Informasi Majaemen Puskesmas Lerep</p>
                <form id="form-register" action="" method="post">
                    <label for="userName">User Name</label>
                    <input type="text" name="userName" id="userName-input" required value="<?= $content->user_name  ?>">
                    <?= isset( $portal['error']['userName'] ) ? '<p>' . $portal['error']['userName'] . '</p>' : '' ?>
                
                    <label for="email">Email</label>
                    <input type="email" name="email" id="emali-input" required value="<?= $content->email ?>">
                    <?= isset( $portal['error']['email'] ) ? '<p>' . $portal['error']['email'] . '</p>' : '' ?>
                    
                    <label for="dispName">Display name</label>
                    <input type="text" name="dispName" id="dispName-input" required value="<?= $content->display_name ?>">
                    <?= isset( $portal['error']['dispName'] ) ? '<p>' . $portal['error']['dispName'] . '</p>' : '' ?>
                    
                    <label for="password">password</label>
                    <input type="password" name="password" id="password-input" required>
                    <?= isset( $portal['error']['password'] ) ? '<p>' . $portal['error']['password'] . '</p>' : '' ?>
                    
                    <label for="password2">Konfirm Password</label>
                    <input type="password" name="password2" id="password2-input" required>
                    <?= isset( $portal['error']['password2'] ) ? '<p>' . $portal['error']['password2'] . '</p>' : '' ?>
                
                    <button type="submit" name="submit">Buat Akun</button>
                <?php  
                if( isset( $content->veryNewUser )){
                    if( $content->veryNewUser == 1){
                        echo '<p>user name telah digunakan</p>';
                    }elseif( $content->veryNewUser == 2){
                        echo '<p>email telah terdaftar</p>';
                    }else{
                        echo '<p>user name atau email telah digunakan</p> ';
                    }
                }                    
                ?>
                 </form>
            </div>
            <div class="body left">
                <div class="logo">
                    <img  class="center" src="/data/img/logo/logo-puskesmas.png" alt="logo" width="100px" height="100px">
                </div>
                
                <p>Bergabunglah untuk mendapatkan akses penuh dalam simpus</p>
            </div>
        </div>
        
    </main>
</body>
<script>
    document.getElementById('form-register').onsubmit = function(e) {
        var user_name = document.getElementById('userName-input').value.length;
        var display_name = document.getElementById('dispName-input').value.length;
        var password = document.getElementById('password-input').value;
        var password_confirm = document.getElementById('password2-input').value;

        // validasi user name dan displayname
        if (user_name < 4 || user_name > 32 ||
            display_name < 4 || display_name > 32) {
                console.log('invalid user name or displayname');
                e.preventDefault();
                return false;
            }
        // validasi password
        if (password.length < 8 || password.length > 100 ||
            password != password_confirm) {
                console.log('invalid password');
                e.preventDefault();
                return false;
            }
    }
</script>
</html>
