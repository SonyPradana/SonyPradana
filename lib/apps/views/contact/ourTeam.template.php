<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <link rel="stylesheet" href="/lib/css/ui/v1.1/cards.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
    <style>
        .container{
            display: grid;
            grid-template-columns: 1fr minmax(250px, 280px);
            grid-column-gap: 24px; grid-row-gap: 24px;
        }
        main{ margin: 24px}
        article .about p{
            line-height: 1.5;
            font-size: 1.3rem;
            margin: 4px 0;
        }

        .team-boxs .box.header{
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%
        }
        .team-boxs .box.cards{
            width: auto; height: auto;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .team-card{margin: 0 16px; background-color: #fff;}

        @media screen and (max-width: 600px) {
            .container{grid-template-columns: 1fr}
        }
        @media screen and (max-width: 347px) {
            .team-boxs .box.cards{flex-direction: column}
            .team-card{margin: 16px 0}
        }
    </style>
</head>
<body>    
    <header>
        <?php include(BASEURL . '/lib/components/header/header.php')?>
    </header>
    
    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Contact</li>
                    <li>Team Kita</li>
                </ul>
            </div>
            <div class="team-boxs">
                <div class="box header"><h1>Our Team</h1></div>
                <div class="box cards">
                    <?php foreach ($content->profiles_card as $value) :?>
                    <div class="team-card">
                        <div class="card image">
                            <img data-src="<?= $value['display_picture'] ?>" src="/data/img/display-picture/user/blur-no-image.png" alt="profile @<?= $value['display_name'] ?>">
                        </div>
                        <div class="card content">
                            <div class="title"><?= $value['display_name'] ?></div>
                            <div class="subtitle"><?= $value['section'] ?></div>
                        </div>
                    </div>
                    <?php endforeach ;?>
                </div>
            </div>
        </main>
        <aside class="right-side">

        </aside>
    </div>

    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>

    <!-- hidden -->    
    <div id="modal">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    </div>
</body>
<script src="/lib/js/index.end.js"></script>
<script>    
    // sticky header
    window.onscroll = function(){
            stickyHeader('.container', '82px', '32px')
    }
        
    // keep alive
    keepalive(
        () => {
            // ok function : redirect logout and then redirect to login page to accses this page
            window.location.href = "/login?url=<?= $_SERVER['REQUEST_URI'] ?>&logout=true"
        },
        () => {          
            // close fuction : just logout
            window.location.href = "/logout?url=<?= $_SERVER['REQUEST_URI'] ?>"
        }
    );

    window.onload = function(){        
        lazyImageLoader();
    }
</script>
</html>
