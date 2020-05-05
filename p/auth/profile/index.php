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
    $user_name = $new_auth->getUserName();
    $email = $user->getEmail();
    $display_name = $user->getDisplayName();
    $unit_kerja = $user->getSection();

    # cek form
    if( isset($_POST['submit'])){
        # isi parameter baru
        $user->setDisplayName( $_POST['disp-name']);
        $user->setSection( $_POST['section']);
        #simpan data
        $user->saveProfile();
        #header ke home page
        header("Location: /");
        exit();
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta content="id" name="language">
    <meta content="id" name="geo.country">
    <meta http-equiv="content-language" content="In-Id">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    title>Edit Profile | SIMPUS Lerep</title>
    <meta name="description" content="Edit Profile, Sistem informasi manajeman puskesmas, lerep">
    <meta name="keywords" content="simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang, edit profile">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/include/html/metatag.html') ?>
       
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
    </style>
</head>
<body>
    <main>
        <div class="container">
            <div class="body right">
                <h1>Ubah Profile</h1>
                <form action="" method="post">
                    <label for="input-user-name">User Name</label>
                    <input type="text" name="user-name" id="input-user-name" value="<?= $user_name  ?>" disabled>
                
                    <label for="input-email">Email</label>
                    <input type="email" name="email" id="input-email" value="<?= $email ?>" disabled>
                    
                    <label for="input-display-name">Display name</label>
                    <input type="text" name="disp-name" id="input-display-name" value="<?= $display_name ?>">

                    <label for="input-section">Unit Kerja</label>
                    <input type="text" name="section" id="input-section" value="<?= $unit_kerja ?>">
                    
                                  
                    <button type="submit" name="submit">Ubah Data</button>
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
</body>
</html>
