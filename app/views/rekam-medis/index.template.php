<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <link rel="stylesheet" href="/lib/css/ui/v1.1/cards.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
    <style>
        h2{
            margin: 0;
            font-size: 1.8rem;
        }

        .container.width-view{
            display: grid;
            grid-template-columns: 1fr minmax(250px, 280px);
            grid-column-gap: 24px; grid-row-gap: 24px;
        }
        .boxs-card{
            overflow-x: visible;
            display: flex;
            min-height: 115px;
        }

        .respone-card .card.action .btn{
            width: 64px;
            text-align: center;
        }
        #jadwal-imunisasi{
            margin-right: 16px;
        }
        .dashbord{
            overflow-x: auto;
        }
        /* Templatebox container */
        .cards-box.data-desa .box-container{
            display: grid;
            grid-template-columns: repeat(3, minmax(250px, 200px));
        }
        .rm-card{
            margin-right: 16px; margin-bottom: 16px;
        }

        /* tablet vie view */
        @media screen and (max-width: 1000px) {
            .boxs-card{
                overflow: auto;
            }
            .cards-box.data-desa .box-container{
                grid-template-columns: repeat(2, minmax(250px, 200px)) ;
            }
        }
        @media screen  and (max-width: 767px) {
            /* costume main container */
            .container.width-view{
                display: grid;
                grid-template-columns: 1fr;
            }
            .boxs-card{min-height: 80px}
            .cards-box.data-desa .box-container{
                grid-template-columns: 1fr ;
            }
        }
    </style>
</head>
<body>
    <header>
        <?php include(BASEURL . '/resources/components/header/header.php'); ?>
    </header>

    <div class="container width-view">
        <main class="dashbord">
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Rekam Medis</li>
                </ul>
            </div>
            <div class="boxs-card">
                <div class="card respone-card grad-pinktoyellow shadow-bottom-left-medium" data-tooltips="click untuk melihat" id="jadwal-imunisasi">
                    <div class="card title">
                        Jadwal Imunisai
                    </div>
                    <div class="card action">
                        <a href="/info/jadwal-pelayanan/" class="btn fill blue small rounded">Lihat &raquo</a>
                    </div>
                </div>
                <div class="card respone-card grad-blue shadow-bottom-left-medium" data-tooltips="click untuk melihat">
                    <div class="card title">
                        Info Covid Ungaran
                    </div>
                    <div class="card action">
                        <a href="/info/covid-kabupaten-semarang" class="btn fill blue small rounded">Lihat &raquo</a>
                    </div>
                </div>
            </div>
            <div class="sparator blue">
                <div class="sparator-title">Dashbord RM</div>
                </div>
            <div class="boxs-dashbord">
                <section class="boxs cards-box data-desa">
                    <div class="box-title"><h2>Data RM Perdesa</h2></div>
                    <div class="box-container">
                    <?php foreach( $content->arr_data as $desa => $jumlah ): ?>
                        <a href="/rekam-medis/search?alamat-search=<?= $desa ?>" class="card rm-card grad-pinktoyellow shadow-bottom-left-medium">
                            <div class="card title">
                                <p><?= $jumlah ?></p>
                                <span class="detail">&nbsp;~ <?= round( ($jumlah / $content->jumlah_rm ) * 100, 1) ?>%</span>
                            </div>
                            <div class="card content">
                                <p>Rm <?= $desa == 'bandarjo' ? 'Kelurahan ' . ucwords($desa) : 'Desa ' . ucwords($desa) ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    </div>
                </section>
                <section class="grub splint">
                    <section class="boxs cards-box range-umur">
                        <div class="box-title"><h2>Data RM Range Umur</h2></div>
                        <div class="box-container">
                            <table>
                                <thead>
                                    <tr>
                                        <td>No</td>
                                        <td style="min-width: 80px;">Umur</td>
                                        <td style="min-width: 80px;">Jumlah</td>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1 ?>
                                <?php foreach( $content->arr_data2 as $r_umur => $t_jumlah ): ?>
                                    <tr>
                                        <td><?= $i ?></td>
                                        <td  style="text-align: center;"><?= $r_umur ?></td>
                                        <td  style="text-align: center;"><?= $t_jumlah ?></td>
                                    </tr>
                                    <?php $i++ ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                    <section class="boxs cards-box undefine"></section>
                </section>
            </div>
        </main>
        <aside>

        </aside>
    </div>

    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/footer/footer.html') ?>
    </footer>

    <!-- hidden -->
    <div id="modal">
        <?php include(BASEURL . '/resources/components/control/modal.html') ?>
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
</script>
</html>
