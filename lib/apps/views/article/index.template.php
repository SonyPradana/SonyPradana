<!DOCTYPE html>
<html lang="en">
<head>    
<?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>
</head>

<style>
    .container.width-view {
        display: grid;
        grid-template-columns: 1fr 300px;
    }
    main {
        overflow-x: auto;
    }

    @media screen and (max-width: 767px) {
        .container.width-view {
            grid-template-columns: 1fr
        }
    }
</style>
<link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
<?= $portal['meta']['css'] ?>
<script src="/lib/js/index.min.js"></script>
<script src="/lib/js/bundles/keepalive.min.js"></script>
<body>
    <header>
        <?php include(BASEURL . '/lib/components/header/header.php'); ?>
    </header>

    <div class="container width-view">
        <main>            
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Article</li>
                    <li><?= $portal['meta']['title'] ?></li>
                </ul>
            </div>
            <article class="article">
                <div class="article-header">
                    <h1><?= $content->article['title'] ?></h1>
                    <div class="article breadcrumb">
                        <div class="author">
                            <img src="<?= $content->article['display_picture_small'] ?>" alt="@<?= $content->article['display_name'] ?>">    
                            <div class="author-name"><a href="/Ourteam"><?= $content->article['display_name'] ?></a></div>
                        </div>
                        <div class="time"><?= $content->article['article_create'] ?></div>
                    </div>
                </div>
                <div class="article-media">
                    <img data-src="<?= $content->article['image_url'] ?>" src="/data/img/thumbnail/thumbnail.png"
                        alt="<?= $content->article['image_alt'] ?>"
                        width="100%" height="auto">
                    <div class="media note">
                        <p><?= $content->article['media_note'] ?></p>
                    </div>
                </div>
                <div class="article-body">
                   <?= $content->article['raw_content'] ?>
                </div>     
            </article>
        </main>
        <aside></aside>
    </div>

    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>
    
    <!-- hidden -->
    <div id="modal">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    </div>
    <script src="/lib/js/index.end.js"></script>
    <script>
        // onload
        $load(function(){
            lazyImageLoader();
        });

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
    </script>
</body>
</html>
