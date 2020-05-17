<?php
    #import modul 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
    // header 
    header_remove("Expires");
    header_remove("Pragma");
    header_remove("X-Powered-By");
    header_remove("Connection");
    header_remove("Server");
    header("Cache-Control:	private");
    header("Content-Type: text/html; charset=UTF-8");
?>
<?php
    #Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 2);
    if( !$auth->TrushClient() ){ 
        header("HTTP/1.1 401 Unauthorized");
        exit();
    }
    header("HTTP/1.1 200 Ok");
?>
<?php
    # ambil dari url
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'nomor_rm';
    $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
    $page = isset( $_GET['page'] ) ? $_GET['page'] : 1;
    $page = is_numeric($page) ? $page : 1;

    # ambil data
    $show_data = new View_RM();

    # set up data base
    $show_data->sortUsing($sort);
    $show_data->orderUsing($order);
    $show_data->limitView(25);

    # setup data filter 
    if(  isset($_GET['r']) && isset($_GET['d1']) && isset($_GET['d2']) && isset($_GET['d3'])
            && isset($_GET['d4']) && isset($_GET['d5']) && isset($_GET['d6']) && isset($_GET['sk'])){  
        # ambil data        
        $r  = $_GET['r'];
        $d1 = $_GET['d1']; $d2 = $_GET['d2'];
        $d3 = $_GET['d3']; $d4 = $_GET['d4'];
        $d5 = $_GET['d5']; $d6 = $_GET['d6'];
        $sk = $_GET['sk'];
    
        # url ke array
        $arr = "['$r', '$d1', '$d2', '$d3', '$d4', '$d5', '$d6', '$sk']";

        # konvert data
        $r = substr_count($r, "-") > 0 ? $r : "0-100"; # nilai default
        $min_max = explode("-", $r);
        $min =  $min_max[0];
        $max =  $min_max[1];        
        $min  = date("Y-m-d", time() - ($min * 31536000) );
        $max  = date("Y-m-d", time() - ($max * 31536000) );        
        # setup data
        $show_data->filterRangeTanggalLahir($min, $max);

        # Setup data
        if( $d1 == 'on'){
            $show_data->filtersAddAlamat("bandarjo");
        }
        if( $d2 == 'on'){
            $show_data->filtersAddAlamat("branjang");
        }
        if( $d3 == 'on'){
            $show_data->filtersAddAlamat("kalisidi");
        }
        if( $d4 == 'on'){
            $show_data->filtersAddAlamat("keji");
        }
        if( $d5 == 'on'){
            $show_data->filtersAddAlamat("lerep");
        }
        if( $d6 == 'on'){
            $show_data->filtersAddAlamat("nyatnyono");
        }

        # setup data
        if( $sk == 'on'){
            $show_data->filterStatusKK();
        }

        #setup page
        $max_page = $show_data->maxPage();
        $page = $page > $max_page ? $max_page : $page;
        $show_data->currentPage($page);

        # result database
        $get_data = $show_data->results();

    }else{
        # url ke array -> tidak diset   
        $arr = "[]";      

        #setup page
        $max_page = $show_data->maxPage();
        $page = $page > $max_page ? $max_page : $page;
        $show_data->currentPage($page);
        # result data base  
        $get_data = $show_data->resultAll();
    }
?>
    <?php if ( $get_data ): ?>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nomor_rm', <?= $order == 'ASC' && $sort == 'nomor_rm' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, <?= $arr?>)">No RM</a></th>
                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nama', <?= $order == 'ASC' && $sort == 'nama' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, <?= $arr ?>)">Nama</a></th>
                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('tanggal_lahir', <?= $order == 'ASC' && $sort == 'tanggal_lahir' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, <?= $arr ?>)">Tanggal Lahir</a></th>
                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('alamat', <?= $order == 'ASC' && $sort == 'alamat' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, <?= $arr ?>)">Alamat</a></th>
                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nomor_rw',<?= $order == 'ASC' && $sort == 'nomor_rt' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, <?= $arr ?>)">RT / RW</a></th>
                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nama_kk',<?= $order == 'ASC' && $sort == 'nama_kk' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, <?= $arr ?>)">Nama KK</a></th>
                    <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nomor_rm_kk', <?= $order == 'ASC' && $sort == 'nomor_rm_kk' ? "'DESC'" : "'ASC'"?>, <?= $page ?>, <?= $arr ?>)">No. Rm KK</a></th>
                    <th><a href="javascript:void(0)">Action</a></th>
                </tr>      
            </thead>
            <tbody>                               
            <?php $idnum = (int) ($page * 25) - 24; ?>
            <?php foreach( $get_data as $data) :?>            
                <tr>       
                    <th><?= $idnum ?></th>
                    <th><?= $data['nomor_rm']?></th>
                    <th><?= ucwords( $data['nama'] )?></th>
                    <th><?= date("d-m-Y", strtotime( $data['tanggal_lahir']))  ?></th>
                    <th><?= ucwords( $data['alamat'] )?></th>
                    <th><?= $data['nomor_rt'] . ' / ' . $data['nomor_rw']?></th>
                    <th <?= $data['nama_kk'] == $data['nama'] ? 'class="mark"' : ""?>><?= ucwords( $data['nama_kk'] )?></th>
                    <th><?= $data['nomor_rm_kk']?></th>
                    <th><a class="link" href="/p/med-rec/edit-rm/index.php?document_id=<?= $data['id']?>">edit</a><?= $data['nama_kk'] == $data['nama'] ? '<a class="link" href="/p/med-rec/search-rm/?submit=&no-rm-kk-search=' . $data['nomor_rm_kk']. '">view</a>' : ""?> </th>
                </tr>                       
                <?php $idnum++; ?>
            <?php endforeach ; ?>
        
            </tbody>
        </table>
        <div class="box-pagination">
            <div class="pagination">
                <?php if( $page > 1 ):?>
                    <a href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort?>', '<?= $order ?>', <?= $page -1 ?>, <?= $arr?>)">&laquo;</a>
                <?php endif;?>                
                <?php if( $max_page > 5 ):?>
                    <!-- satu depan -->
                    <a <?= 1 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', 1, <?= $arr?>)">1</a>
                    <!-- tiga tengah -->
                    <?php if( $page  > 2 && $page < ($max_page - 1)):?>
                        <a href="javascript:void(0)" class="sperator">...</a>
                        <a href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $page - 1 ?>, <?= $arr?>)"><?= $page - 1 ?></a>
                        <a class="active" href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $page ?>, <?= $arr?>)"><?= $page ?></a>
                        <a href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $page + 1 ?>, <?= $arr?>)"><?= $page + 1 ?></a>
                        <a href="javascript:void(0)" class="sperator">...</a>
                    <?php elseif( $page < 4 ):?>    
                        <a <?= 2 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', 2, <?= $arr?>)">2</a>
                        <a <?= 3 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', 3, <?= $arr?>)">3</a>
                        <a href="javascript:void(0)" class="sperator">...</a>
                    <?php elseif( $page > ($max_page - 2) ):?>  
                        <a href="javascript:void(0)" class="sperator">...</a>  
                        <a <?= $max_page - 2 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $max_page - 2 ?>, <?= $arr?>)"><?= $max_page - 2?></a>
                        <a <?= $max_page - 1 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $max_page - 1 ?>, <?= $arr?>)"><?= $max_page - 1?></a>                        
                    <?php endif;?> 
                    <!-- satu belakang -->
                    <a <?= $max_page == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort ?>', '<?= $order ?>', <?= $max_page ?>, <?= $arr?>)"><?= $max_page ?></a>                                
                <?php elseif( $max_page < 6 ):?>
                    <?php for ($i=1; $i <= $max_page; $i++) :?>
                        <a <?= $i == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort?>', '<?= $order ?>', <?= $i ?>, <?= $arr?>)"><?= $i ?></a>
                    <?php endfor;?>
                <?php endif;?> 
                <?php if( $page < $max_page ):?>
                    <a href="javascript:void(0)" onclick="GDcostumeFilter('<?= $sort?>', '<?= $order ?>', <?= $page +1 ?>, <?= $arr?>)">&raquo;</a>
                <?php endif;?>
            </div>
        </div>
    <?php else : ?>
        <p>gagal memuat data</p>
    <?php endif; ?>
