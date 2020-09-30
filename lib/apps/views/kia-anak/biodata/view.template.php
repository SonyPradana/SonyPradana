<!DOCTYPE html>
<html lang="en">
<head>
<meta content="id" name="language">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

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
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >Nama</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >Tanggal Lahir</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >Alamat</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >RT/RW</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >Nama Orangtua</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >Jenis Kelamin</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >BBL</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >PBL</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >IMD</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >KIA</a></th>
                                <th scope="col"><a class="sort-by" href="javascript:void(0)" >Asi Ekslusif</a></th>
                                <th>Action</th>                                                     
                            </tr>                       
                        </thead>
                        <tbody>
                            <?php $num = (int) 1 ?>
                            <?php foreach( $content->data_kia as $data) : ?>
                            <tr>       
                                <th><?= $num ?></th>
                                <th><?= ucwords( $data['nama'] ) ?></th>
                                <?php $new_date = date("d-m-Y", strtotime( $data['tanggal_lahir'])) ?>
                                <th><?= $new_date == '01-01-1970' ? '00-00-0000' : $new_date ?></th>
                                <th><?= ucwords( $data['alamat'] )?></th>
                                <th><?= $data['nomor_rt'] . ' / ' . $data['nomor_rw']?></th>
                                <th><?= ucwords( $data['nama_kk'] )?></th>
                                <th><?= $data['jenis_kelamin'] == 1 ? "Laki-laki" : "Perempuan" ?></th>
                                <th><?= $data['bbl'] ?></th>
                                <th><?= $data['pbl'] ?></th>
                                <th><?= $data['imd'] ?></th>
                                <th><?= $data['kia'] ?></th>
                                <th><?= $data['asi_eks'] == 0 ? "Tidak" : "Iya" ?></th>
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
</script>
</html>
