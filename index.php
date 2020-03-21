<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/auth/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/library/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/simpus/init.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/db/db_crud/DbConfig.php';
?>
<?php ;

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMPUS Lerep</title>
    <style>
    body{
        background-color: black;
        color: white
    }
    a{
        margin: 15px 10px;
    }
    </style>
</head>
<body>
    <header>
        <div class="banner">Welcome To Simpus Lerep</div> 
        <div>
        <?php 
        session_start();
        $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
        $test_auth = new Auth($token, 0);
        if( $test_auth->TrushClient() == true){
            $newUser = new User($test_auth->getUserName());
            echo 'Hai, ' , $newUser->getDisplayName() . '<br/>';
            echo 'Your Email is, ' , $newUser->getEmail() . '<br/>';           
        }else{
            echo 'not trust user';
        }
        ?>
        </div>        
    <?php if( $test_auth->TrushClient()): ?>
        <ul>
            <li> <a href="/p/med-rec/view-rm/">lihat data rm</a> </li>
            <li> <a href="/p/med-rec/search-rm/">cari data rm</a></li>
            <li><a href="/p/med-rec/new-rm/">buat data rm</a><br></li>
        </ul>        
        
        <a href="/p/auth/reset-password/">password baru</a>
        <a href="/p/auth/logout/index.php?url=/p/auth/login">logout</a>
    <?php else: ?>
        <a href="/p/auth/login">login</a>
    <?php endif; ?>


    </header>
</body>
</html>
