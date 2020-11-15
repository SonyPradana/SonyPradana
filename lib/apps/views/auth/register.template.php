<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Acunt Baru</title>
    <meta name="description" content="daftar akun baru sistem informasi kesehatan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep, register">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.html') ?>
       
<style>
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
        .container {
            display: flex;
            justify-content: center;
        }
        .boxs {
            background-color: #fff;
            padding: 24px;
            width: 60%;
            min-width: 472px;
            max-width: 1000px;
            box-shadow: 0 4px 8px 0 #00000022, 0 6px 20px 0 #00000010;
            gap: 8px;
            height: 450px;

            display: grid;
            grid-template-columns: 1fr 1fr;
        }
        input{
            width: calc(100% - 56px);
            display: block;
            margin-bottom: 10px;
            font-size: 17px;
        }
        .box.display_picture img {
            height: 48px;
        }
        .boxs .box-right {
            border-left:  0.1px solid #ece9e9;
            padding: 10px;
        }
        .boxs .box-right .logo {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .boxs .box-right p {
            text-align: center;
            font-size: 20px
        }
        footer {
            display: flex;
            justify-content: center;
            align-self: end;
            margin: 12px
        }
        /* mobile */
        @media screen and (max-width: 479px) {
            html, body {
                height: calc(100% - 12px);
                top: 12px;
            }
            .boxs {
                grid-template-columns: 1fr;
                width: 300px;
                min-width: 320px;
                max-width: 479;
                height: auto;
            }
            input{
                width: 100%;
            }
            .boxs .box-right {
                border-left:  none;
                border-top: 1px solid #ece9e9;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <main class="container">
        <div class="boxs">
            <div class="box-left">
                <h1>Register User Baru</h1>
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
            <div class="box-right">
                <div class="logo">
                    <img src="/data/img/logo/logo-puskesmas.png" alt="logo" width="100px" height="100px">
                </div>
                <p>Bergabunglah untuk mendapatkan akses penuh dalam simpus</p>
            </div>
        </div>        
    </main>
    <footer>
        <div class="footer-box">
            <a href="/">Home Page</a>
        </div>
    </footer>
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
