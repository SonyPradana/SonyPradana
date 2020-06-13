<?php
#import modul 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
?>
<?php
#authorization token
session_start();
$token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
$new_auth = new Auth($token, 2);
if( !$new_auth->TrushClient() ){
    #redirect ke home page
    header("Location: /");   
    exit();
}
?>
<?php
    # me load data dari data base
    $user = new User($new_auth->getUserName());

    # cek form
    if( isset($_POST['submit'])){
        $url_picture = isset( $_POST['url-display-picture'] ) ? $_POST['url-display-picture'] : '/data/img/display-picture/no-image.png';
        $request_upload = isset( $_FILES['display-picture']) ? true :false;

        // upload image
        $upload = new UploadFile($_FILES['display-picture']);
        $upload->setFileName( $new_auth->getUserName() );
        $upload->setFolderLocation('/data/img/display-picture/user/');
        $upload->setMimeTypes(['image/jpg', 'image/jpeg', 'image/png']);
        $upload->setMaxFileSize( 562500 ); #450 Kb
        $upload_url = $upload->upload();
        $file_location = $request_upload && $upload->Success() ? $upload_url: $url_picture;

        # isi parameter baru
        $user->setDisplayName( $_POST['disp-name']);
        $user->setSection( $_POST['section']);
        $user->setDisplayPicture( $file_location );

        #simpan data
        $user->saveProfile();

        // delate old image
        if($upload->Success()){
            if( $upload_url != $url_picture){
                $upload->delete($url_picture);
            }
        }

        $msg = $request_upload ? $upload->getError() : null;
    }
    // mengambil data untuk input value(form)
    $user_name       = $new_auth->getUserName();
    $email           = $user->getEmail();
    $display_name    = $user->getDisplayName();
    $unit_kerja      = $user->getSection();
    $display_picture = $user->getDisplayPicture();

?>
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
            height: 450px;

            display: grid;
            grid-template-columns: 1fr 1fr;
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
        .box.display_picture img{
            height: 48px;
        }
    </style>
</head>
<body>
    <main>
        <div class="container">
            <div class="body right">
                <h1>Ubah Profile</h1>
                <form action="" method="post" enctype="multipart/form-data">
                    <label for="input-user-name">User Name</label>
                    <input type="text" name="user-name" id="input-user-name" value="<?= $user_name  ?>" disabled>
                
                    <label for="input-email">Email</label>
                    <input type="email" name="email" id="input-email" value="<?= $email ?>" disabled>
                    
                    <label for="input-display-name">Display name</label>
                    <input type="text" name="disp-name" id="input-display-name" value="<?= $display_name ?>">

                    <label for="input-section">Unit Kerja</label>
                    <input type="text" name="section" id="input-section" value="<?= $unit_kerja ?>">
                    
                    <label for="input-section">Avatar</label>
                    <div class="box display_picture">
                        <img src="<?= $display_picture ?>" alt="@<?= $display_name ?>" sizes="10px" id="image-preview">
                    <?php if( isset($msg) ) : ?>
                        <p style="font-size: 14px; color:blue; margin: 0" id="info-warning"><?= $msg ?></p>
                    <?php endif ; ?>
                    </div>
                    <input type="file" name="display-picture" id="input-display-picture">
                    <input type="hidden" name="url-display-picture" value="<?= $display_picture ?>">
                                  
                    <button type="submit" name="submit">Ubah Data</button>
                    <button type="button" name="close" id="close-button">Close</button>
                 </form>
            </div>
            <div class="body left">
                <div class="logo">
                    <img  class="center" src="/data/img/logo/logo-puskesmas.png" alt="logo" width="100px" height="100px">
                </div>
                
                <p>Bergabunglah untuk mendapatkan akses penuh dalam sinpus</p>
            </div>
        </div>
        
    </main>
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

    </script>
</body>
</html>
