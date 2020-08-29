<!DOCTYPE html>
<html lang="en">
<head>
<meta content="id" name="language">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/pagination.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <script src="/lib/js/controller/table-rm/index.js"></script>
    <style>
        body{
            transition: margin-left .3s;
        }

        .btn-boxs{
            display: flex;
            justify-content: start;
        }
        .btn-box.left{
            display: flex;
        }

        button{ margin: 8px 0;}
        p{ margin: 16px 0 }
        .boxs{
            width: 100%;
        }
        .box-right{
            width: 100%;
            overflow-x: auto;
        }
        .box-right p.info{ display: none;}
    
        table { width: 100% }  
        .gab{
            min-width: 20px;
        }
    </style>
</head>
<body>
    <header>
        <?php include(BASEURL . '/lib/components/header/header.php'); ?>
    </header>
    <div id="modal">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    </div>

    <div class="container">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li><a href="/rekam-medis">KIA Anak</a></li>
                    <li>Lihat Data</li>
                </ul>
            </div>
            <h1>Lihat Data KIA Anak</h1>
            <div class="btn-boxs">
                <div class="btn-box-right">
                    <button class="btn fill blue rounded small block" id="btnFilter">Filter</button>
                </div>
            </div>
            <div class="boxs">
                <div class="box-left">
                </div>
                <div class="box-right">
                    <!-- table -->
                    <table class="data-rm">       
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama</a></th>
                                <th>Alamat</th>
                                <?php for($i=0; $i < $content->kolom_terbanyak; $i++): ?>
                                <th>Pemeriksaan ke <?= $i+1 ?></th>
                                <?php endfor; ?>
                                <th>Action</th>                                                     
                            </tr>                       
                        </thead>
                        <tbody>
                            <?php $num = (int) 1 ?>
                            <?php foreach( $content->data_posyandu as $data) : ?>
                            <tr>       
                                <th><?= $num ?></th>
                                <th><?= ucwords( $data['nama'] ) ?></th>
                                <th><?= ucwords( $data['alamat'] ) ?></th>                                
                                <?php foreach($data['data'] as $pertemuan): ?>
                                <th><?= $pertemuan['tinggi_badan'] ?></th>
                                <?php endforeach; ?>
                                <?php $minus =  $content->kolom_terbanyak - count($data['data']); ?>
                                <?php for ($i=0; $i < $minus; $i++): ?>
                                    <th>--</th>
                                <?php endfor; ?>
                                <th><a class="btn rounded light blue fill number" href="/kia-anak/edit/biodata?document_id=<?= $data['id_hash']?>">edit</a></th>
                                <?php $num++ ?>
                            </tr>
                            <?php endforeach ; ?>
                        </tbody>
                    </table>
                    <p class="info">Data tidak ditemukan</p>
                    <div class="box-pagination">
                        <div class="pagination">
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <?php if( $portal['message']['show'] ) :?>
        <div class="snackbar <?= $portal['message']['type'] ?>">
            <div class="icon">
                <!-- css image -->
            </div>
            <div class="message">
                <?= $portal['message']['content'] ?>
            </div>
        </div>
    <?php endif; ?> 
    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>
</body>
<script src="/lib/js/index.end.js"></script>
<script>
    // onload
    window.addEventListener('load', () => {
    })

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

    // row click tampilkan data
</script>
</html>
