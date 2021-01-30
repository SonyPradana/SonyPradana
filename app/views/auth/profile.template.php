<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile | SIMPUS Lerep</title>
    <meta name="description" content="Edit Profile, Sistem informasi manajeman puskesmas, lerep">
    <meta name="keywords" content="simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang, edit profile">
    <meta name="author" content="amp">
<?php include(APP_FULLPATH['component'] . 'meta/metatag.html') ?>

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
                <h1>Ubah Profile</h1>
                <form id="form-profile" action="" method="post" enctype="multipart/form-data">
                    <label for="input-user-name">User Name</label>
                    <input type="text" name="user-name" id="input-user-name" value="<?= $content->user_name  ?>" disabled>

                    <label for="input-email">Email</label>
                    <input type="email" name="email" id="input-email" value="<?= $content->email ?>" disabled>

                    <label for="input-display-name">Display name</label>
                    <input type="text" name="disp-name" id="input-display-name" required value="<?= $content->display_name ?>">

                    <label for="input-section">Unit Kerja</label>
                    <input type="text" name="section" id="input-section" required value="<?= $content->unit_kerja ?>">

                    <label for="input-section">Avatar</label>
                    <div class="box display_picture">
                        <img src="<?= $content->display_picture ?>" alt="@<?= $content->display_name ?>" sizes="10px" id="image-preview">
                    <?php if( isset($portal['validation']['image-preview']) ) : ?>
                        <p style="font-size: 14px; color:blue; margin: 0" id="info-warning"><?= $portal['validation']['image-preview'] ?></p>
                    <?php endif ; ?>
                    </div>
                    <input type="file" name="display-picture" id="input-display-picture">
                    <input type="hidden" name="url-display-picture" value="<?= $content->display_picture ?>">

                    <button type="submit" name="submit">Ubah Data</button>
                    <button type="button" name="close" id="close-button">Close</button>
                 </form>
            </div>
            <div class="box-right">
                <div class="logo">
                    <img src="/data/img/logo/logo-puskesmas.png" alt="logo" width="100px" height="100px">
                </div>

                <p>Selamat Datang Di System Informasi Majaemen Puskesmas Lerep</p>
            </div>
        </div>

    </main>
    <footer>
        <div class="footer-box">
            <a href="/">Home Page</a>
        </div>
    </footer>
    <script>
        document.getElementById("close-button").onclick = function(){
            window.location.href = "<?= isset( $_GET['url'] ) ? $_GET['url'] : '/' ?>";
        };

        document.getElementById("input-display-picture").onchange = function(event){
            var reader = new FileReader();
            reader.onload = function(){
                clear_info();
                var my_image = document.getElementById("image-preview");
                my_image.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        function clear_info(){
            var info_warning = document.getElementById("info-warning");
            if( info_warning != null){
                info_warning.remove();
            }
        }

        document.getElementById('form-profile').onsubmit = function(e) {
            var display_name = document.getElementById('input-display-name').value.length;
            var unit_kerja = document.getElementById('input-section').value.length;

            if (display_name < 4 || display_name > 32 ||
                unit_kerja < 2 || unit_kerja > 32) {
                e.preventDefault();
                return false;
            }
        }

    </script>
</body>
</html>
