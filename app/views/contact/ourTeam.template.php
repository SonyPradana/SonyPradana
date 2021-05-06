<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(APP_FULLPATH['component'] . 'meta/metatag.php') ?>

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
        main{ margin: 24px; overflow: hidden;}
        article .about p{
            line-height: 1.5;
            font-size: 1.3rem;
            margin: 4px 0;
        }

        .team-boxs {
          max-width: 100%;
        }

        .team-boxs .box.header{
          text-align: center;
        }

        .team-boxs .box.cards{
            height: auto;
            display: grid;
            grid-template-columns: repeat(10, 200px);
            column-gap: 16px;
            row-gap: 16px;
            padding: 8px;
            overflow-x: auto;
        }

        .team-card{background-color: #fff;}

        @media screen and (max-width: 767px) {
          .team-boxs .box.cards {
            grid-template-columns: repeat(1, 200px);
            place-items: end center;
          }
            .container{grid-template-columns: 1fr}
        }
    </style>
</head>
<body>
    <header>
        <?php include(APP_FULLPATH['component'] . 'header/header.php')?>
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
        <?php include(APP_FULLPATH['component'] . 'footer/footer.html') ?>
    </footer>

    <!-- hidden -->
    <div id="modal">
        <?php include(APP_FULLPATH['component'] . 'control/modal.html') ?>
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
