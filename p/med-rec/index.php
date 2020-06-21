<?php
    #import modul 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
?>
<?php
    #Aunt cek
    session_start();
    $token = $_SESSION['token'] ?? '';
    $auth = new Auth($token, 2);
    if( !$auth->TrushClient() ){
        header("Location: /p/auth/login");   
        exit();
    }
?>
<?php
    $user = new User($auth->getUserName());

    $rm = new MedicalRecords();
    // mengambil data jumlah rm bedasarkan desa
    $arr_desa = ['bandarjo', 'branjang', 'kalisidi', 'keji', 'lerep', 'nyatnyono'];
    $arr_data = [];
    $jumlah_rm = $rm->maxData();
    foreach( $arr_desa as $desa ){
        $rm->filterByAlamat($desa);
        $arr_data[$desa] = $rm->maxData();
    }
    // mengambil data jumlah rm berdasarkan range umur
    $rm->reset();
    $arr_umur  = ["0-5", "5-16", "17-25", "26-45", "46-65", "65-100"];
    $arr_data2 = [];
    foreach( $arr_umur as $umur ){        
        $min_max = explode("-", $umur);
        $min =  $min_max[0];
        $max =  $min_max[1];        
        $min  = date("Y-m-d", time() - ($min * 31536000) );
        $max  = date("Y-m-d", time() - ($max * 31536000) );
        $rm->filterRangeTanggalLahir($min, $max);
        $arr_data2[$umur] = $rm->maxData();
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
    <title>Liat data rm</title>
    <meta name="description" content="sisteminformasi kesehtan puskesmas Lerep">
    <meta name="keywords" content="simpus lerep, pkm lerep">
    <meta name="author" content="amp">
<?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.html') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/card.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
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
        <?php $active_menu = 'home' ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.html') ?>
    </header>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
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
                        <a href="/p/info/jadwal-pelayanan/" class="btn fill blue small rounded">Lihat &raquo</a>
                    </div>
                </div>
                <div class="card respone-card grad-blue shadow-bottom-left-medium" data-tooltips="click untuk melihat">
                    <div class="card title">
                        Info Covid Ungaran
                    </div>
                    <div class="card action">
                        <a href="/p/info/covid-kabupaten-semarang/" class="btn fill blue small rounded">Lihat &raquo</a>
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
                    <?php foreach( $arr_data as $desa => $jumlah ): ?>
                        <a href="/p/med-rec/search-rm/?alamat-search=<?= $desa ?>" class="card rm-card grad-pinktoyellow shadow-bottom-left-medium">
                            <div class="card title">
                                <p><?= $jumlah ?></p>
                                <span class="detail">&nbsp;~ <?= round( ($jumlah / $jumlah_rm ) * 100, 1) ?>%</span>
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
                                <?php foreach( $arr_data2 as $r_umur => $t_jumlah ): ?>
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
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>
</body>
<script src="/lib/js/index.end.js"></script>
<script>
    // sticky header
    window.onscroll = function(){
            stickyHeader('.container', '82px', '32px')
    }

    // keep alive
    keepalive(() => {
        window.location.href = "/p/auth/login/?url=<?= $_SERVER['REQUEST_URI'] ?>"
    })
</script>
</html>
