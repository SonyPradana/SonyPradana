<?php
    #import modul 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/auth/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/library/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/core/simpus/init.php';
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/config/DbConfig.php';
?>
<?php
    #Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 2);
    if( !$auth->TrushClient() ){ 
        exit();
    }
?>
<?php
    # ambil dari url
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'nomor_rm';
    $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
    # ambil data
    $show_data = new View_RM();
    //set up data base
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
            $show_data->filtersAddAlamat("barnjang");
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

        #setup data
        if( $sk == 'on'){
            $show_data->filtereStatusKK();
        }
        # result database
        $get_data = $show_data->results();

    }else{
        # url ke array -> tidak diset   
        $arr = "[]";      
        // result data base  
        $get_data = $show_data->resultAll();
    }
?>
    <?php if ( $get_data ): ?>
        <table>
            <tr>
                <th>No.</th>
                <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nomor_rm', <?= $order == 'ASC' && $sort == 'nomor_rm' ? "'DESC'" : "'ASC'"?>, <?= $arr?>)">No RM</a></th>
                <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nama', <?= $order == 'ASC' && $sort == 'nama' ? "'DESC'" : "'ASC'"?>, <?= $arr ?>)">Nama</a></th>
                <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('tanggal_lahir', <?= $order == 'ASC' && $sort == 'tanggal_lahir' ? "'DESC'" : "'ASC'"?>, <?= $arr ?>)">Tanggal Lahir</a></th>
                <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('alamat', <?= $order == 'ASC' && $sort == 'alamat' ? "'DESC'" : "'ASC'"?>, <?= $arr ?>)">Alamat</a></th>
                <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nomor_rw',<?= $order == 'ASC' && $sort == 'nomor_rt' ? "'DESC'" : "'ASC'"?>, <?= $arr ?>)">RT / RW</a></th>
                <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nama_kk',<?= $order == 'ASC' && $sort == 'nama_kk' ? "'DESC'" : "'ASC'"?>, <?= $arr ?>)">Nama KK</a></th>
                <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="GDcostumeFilter('nomor_rm_kk', <?= $order == 'ASC' && $sort == 'nomor_rm_kk' ? "'DESC'" : "'ASC'"?>, <?= $arr ?>)">No. Rm KK</a></th>
                <th><a href="javascript:void(0)">Action</a></th>
            </tr>                         
        <?php $idnum = (int) 1; ?>
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
        </table>
    <?php else : ?>
        <p>gagal memuat data</p>
    <?php endif; ?>
