<!DOCTYPE html>
<html lang="en">
<head>
<meta content="id" name="language">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
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
        <?php include(BASEURL . '/resources/components/header/header.php'); ?>
    </header>

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
                                <th style="vertical-align: middle;">No.</th>
                                <th>Nama</a></th>
                                <th>Alamat</th>
                                <?php for($i=0; $i < $content->kolom_terbanyak; $i++): ?>
                                <th colspan="3">Kunjungan <?= $i+1 ?></th>
                                <?php endfor; ?>
                                <th>Action</th>
                            </tr>
                            <tr>
                                <th rowspan="2"></th>
                                <th rowspan="2"></a></th>
                                <th rowspan="2"></th>
                                <?php for($i=0; $i < $content->kolom_terbanyak; $i++): ?>
                                <th>cm</th>
                                <th>gram</th>
                                <th>edit</th>
                                <?php endfor; ?>
                                <th rowspan="2"></th>
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
                                    <td><?= $pertemuan['tinggi_badan'] ?></td>
                                    <td><?= $pertemuan['berat_badan'] ?></td>
                                    <td><a style="text-align: center;" class="btn rounded light blue text number" href="/kia-anak/edit/posyandu?document_id=<?= $data['id_hash'] . '-'. $pertemuan['id']?>"> edit</a></td>
                                <?php endforeach; ?>
                                <?php $minus =  $content->kolom_terbanyak - count($data['data']); ?>
                                <?php for ($i=0; $i < $minus; $i++): ?>
                                    <td>--</td>
                                    <td>--</td>
                                    <td></td>
                                <?php endfor; ?>
                                <th><a class="btn rounded light blue fill number" href="/kia-anak/edit/biodata?document_id=<?= $data['id_hash']?>">edit</a></th>
                                <?php $num++ ?>
                            </tr>
                            <?php endforeach ; ?>
                        </tbody>
                    </table>
                    <div class="box-pagination">
                        <div class="pagination">
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/footer/footer.html') ?>
    </footer>

    <!-- hidden -->
    <div id="modal">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/control/modal.html') ?>
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
