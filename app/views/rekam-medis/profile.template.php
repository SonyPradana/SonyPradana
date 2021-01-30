<!DOCTYPE html>
<html lang="en">
<head>
    <?php include(APP_FULLPATH['component'] . 'meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
    <style>
        .boxs{
            width: 100%; height: 100%;
            display: grid;
            grid-template-columns: 1fr;
        }
        /* .boxs * div{
            outline: 1px dashed pink;
        } */
        .gap-16{ width: 16px; height: 16px; }
        .gap-24{ width: 24px; height: 24px; }

        /* ==============================
           ==========> header <==========
           ============================== */
        .boxs-header{
            display: grid;
            grid-template-columns: 200px 16px 1fr;
        }
        .box.left{
            height: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .box-img, .img img{
            max-width: 150px;
            max-height: 150px;
        }
        .box.right{
            display: flex;
            justify-content: start;
            align-items: center;
        }
        .box-desc{
            display: grid;
            grid-template-columns: 1fr;
        }
        .box-desc > div{ margin-bottom: 12px;}
        .box-grup{
            display: flex;
        }
        .grup-itmes{ padding: 8px;}
        .grup-itmes > div{ margin-right: 8px;}
        /* style */
        .biodata .desc .title{
            font-size: 2rem;
        }


        /* ==============================
           ==========> body <==========
           ============================== */
        .boxs-body{
            display: flex;
            flex-direction: column;
        }
        /* style */
        .box-tap > button{
            margin-right: 12px;
        }
        .dashbord .title{
            display: flex;
        }
        .tabcontent .box-section{
            display: flex;
        }
        .dashbord .action{
            margin-left: 16px;
            display: flex;
            align-items: center;
        }
        /* style */
        .dashbord .selection{
            border: 1px dashed pink;
        }
        .dashbord .tabcontent{
            margin: 12px;
        }

    </style>
</head>
<body>
    <header>
    <header>
        <?php include(APP_FULLPATH['component'] . 'header/header.php'); ?>
    </header>

    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="/rekam-medis">Rekam Medis</a></li>
                    <li>Biodata</li>
                </ul>
            </div>
            <div class="boxs">
                <div class="boxs-header biodata">
                    <div class="box left img">
                        <div class="box-img">
                            <img src="/data/img/display-picture/no-image.png" alt="no image">
                        </div>
                    </div>
                    <div class="gap-16"></div>
                    <div class="box right desc">
                        <div class="box-desc">
                            <div class="title"><?= $content->nama ?></div>
                            <div class="detail">Nomor RM: <?= $content->nomor_rm ?></div>
                            <div class="content">
                                <div class="grup-name">Grup:</div>
                                <div class="grup-itmes box-grup">
                                    <a href="/rekam-medis/view" class="grup btn rounded light blue text"><?= $content->grup ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="gap-24"></div>
                <div class="boxs-body dashbord">
                    <div class="box-tap taps">
                        <button class="btn rounded light blue outline tablink" onclick="openTap('rekam-medis', this)" id="defaultOpen">Rekam Medis</button>
                        <button class="btn rounded light green outline tablink" onclick="openTap('kia', this)">KIA</button>
                        <button class="btn rounded light blue outline tablink" onclick="openTap('prolanis', this)">Prolanis</button>
                        <button class="btn rounded light green outline tablink" onclick="openTap('posyandu', this)">Posyandu</button>
                    </div>
                    <div class="box-content selection">
                        <div id="rekam-medis" class="tabcontent">
                            <div class="title">
                                <h2>Rekam Medis</h2>
                                <div class="action">
                                    <a class="btn rounded light blue fill" href="/rekam-medis/edit?document_id=<?= $content->profile_id ?>">Edit</a>
                                </div>
                            </div>
                            <div class="box-section">
                                <div class="section">
                                    <h3>Data diri</h3>
                                    <p>Nama: <?= $content->nama ?></p>
                                    <p>Tanggal Lahir: <?=  $content->tanggal_lahir  ?></p>
                                    <p>Alamat: <?= $content->alamat_lengkap ?></p>
                                </div>
                                <div class="gap-16"></div>
                                <div class="section">
                                    <h3>Biodata</h3>
                                    <p>Nama kepala keluarga: <?= $content->nama_kk ?></p>
                                </div>
                            </div>
                        </div>
                        <div id="kia" class="tabcontent">
                            <div class="title">
                                <h2>KIA</h2>
                                <div class="action">
                                </div>
                            </div>
                            <div class="box-section">
                                <div class="section">
                                    <p>Data tidak tersedia</p>
                                </div>
                            </div>
                        </div>
                        <div id="prolanis" class="tabcontent">
                            <div class="title">
                                <h2>Prolanis</h2>
                                <div class="action">
                                </div>
                            </div>
                            <div class="box-section">
                                <div class="section">
                                    <p>Data tidak tersedia</p>
                                </div>
                            </div>
                        </div>
                        <div id="posyandu" class="tabcontent">
                            <div class="title">
                                <h2>Posyandu</h2>
                                <div class="action">
                                </div>
                            </div>
                            <div class="box-section">
                                <div class="section">
                                    <p>Data tidak tersedia</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>
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
            window.location.href = "/login/?url=<?= $_SERVER['REQUEST_URI'] ?>&logout=true"
        },
        () => {
            // close fuction : just logout
            window.location.href = "/logout?url=<?= $_SERVER['REQUEST_URI'] ?>"
        }
    );

    function openTap(cityName, elmnt) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].style.backgroundColor = "";
    }
    document.getElementById(cityName).style.display = "block";

    }
    // Get the element with id="defaultOpen" and click on it
    document.getElementById("defaultOpen").click();
</script>
</html>
