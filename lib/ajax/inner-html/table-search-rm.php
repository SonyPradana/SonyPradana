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
    # ambil parameter dari url
    $sort = isset( $_GET['sort'] ) ? $_GET['sort'] : 'nomor_rm';
    $order = isset( $_GET['order'] ) ? $_GET['order'] : 'ASC';
    $page = isset( $_GET['page'] ) ? $_GET['page'] : 1;
    $max_page = 1;
    $limit = 10;
    # parameter untuk search data
    $main_search = isset( $_GET['main-search'] ) ? $_GET['main-search'] : '';
    $nomor_rm_search = isset( $_GET['nomor-rm-search']) ? $_GET['nomor-rm-search'] : '';
    $alamat_search = isset( $_GET['alamat-search'] ) ? $_GET['alamat-search'] : '';
    $no_rt_search = isset( $_GET['no-rt-search'] ) ? $_GET['no-rt-search'] : '';
    $no_rw_search = isset( $_GET['no-rw-search'] ) ? $_GET['no-rw-search'] : '';
    $nama_kk_search = isset( $_GET['nama-kk-search'] ) ? $_GET['nama-kk-search'] : '';
    $no_rm_kk_search = isset( $_GET['no-rm-kk-search'] ) ? $_GET['no-rm-kk-search'] : '';
    $strict_search = isset( $_GET['strict-search'] ) ? true : false;

    # cari data
    $show_data = new View_RM();

    # setup data
    $show_data->sortUsing($sort);
    $show_data->orderUsing($order);
    $show_data->limitView($limit);

    # query data
    $show_data->filterByNama( $main_search );
    $show_data->filterByNomorRm( $nomor_rm_search);
    $show_data->filterByAlamat($alamat_search );
    $show_data->filterByRt( $no_rt_search );
    $show_data->filterByRw( $no_rw_search );
    $show_data->filterByNamaKK( $nama_kk_search );
    $show_data->filterByNomorRmKK( $no_rm_kk_search );

    # setup page
    $max_page = $show_data->maxPage();
    $page = $page > $max_page ? $max_page : $page;
    $show_data->currentPage($page);

    # excute query
    $get_data = $show_data->result( $strict_search );

    # param query untuk dikirim via js
    $parram = "'$main_search', '$nomor_rm_search', '$strict_search', '', '$alamat_search', '$no_rt_search', '$no_rw_search', '$nama_kk_search', '$no_rm_kk_search'";
    
?>
<script>var JSON_GET = '<?= $Get_JSON ?>'</script>
<!-- render table -->
<table>
    <thead>
        <tr>
            <th>No.</th>
            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="getTableSearch('nomor_rm', <?= $order == 'ASC' && $sort == 'nomor_rm' ? "'DESC'" : "'ASC'" ?>, '<?= $page ?>' , <?= $parram ?>)">No RM</a></th>
            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="getTableSearch('nama', <?= $order == 'ASC' && $sort == 'nama' ? "'DESC'" : "'ASC'" ?>, '<?= $page ?>' , <?= $parram ?>)">Nama</a></th>
            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="getTableSearch('tanggal_lahir', <?= $order == 'ASC' && $sort == 'tanggal_lahir' ? "'DESC'" : "'ASC'" ?>, '<?= $page ?>' , <?= $parram ?>)">Tanggal Lahir</a></th>
            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="getTableSearch('alamat', <?= $order == 'ASC' && $sort == 'alamat' ? "'DESC'" : "'ASC'" ?>, '<?= $page ?>' , <?= $parram ?>)">Alamat</a></th>
            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="getTableSearch('nomor_rw', <?= $order == 'ASC' && $sort == 'nomor_rw' ? "'DESC'" : "'ASC'" ?>, '<?= $page ?>' , <?= $parram ?>)">RT / RW</a></th>
            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="getTableSearch('nama_kk', <?= $order == 'ASC' && $sort == 'nama_kk' ? "'DESC'" : "'ASC'" ?>, '<?= $page ?>' , <?= $parram ?>)">Nama KK</a></th>
            <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="getTableSearch('nomor_rm_kk', <?= $order == 'ASC' && $sort == 'nomor_rm_kk' ? "'DESC'" : "'ASC'" ?>, '<?= $page ?>' , <?= $parram ?>)">No. Rm KK</a></th>
            <th>Action</th>                                                     
        </tr>
    </thead>
    <tbody>
    <?php if ( $get_data ): ?>
        <?php $idnum = (int) 1; ?>
        <?php foreach( $get_data as $data) :?>            
        <tr>       
            <th><?= $idnum ?></th>
            <th><?= $data['nomor_rm']?></th>
            <th><?= StringManipulation::addHtmlTag($data['nama'], $main_search, '<span style="color:blue">', '</span>')?></th>
            <th><?= date("d-m-Y", strtotime( $data['tanggal_lahir']))  ?></th>
            <th><?= $data['alamat']?></th>
            <th><?= $data['nomor_rt'] . ' / ' . $data['nomor_rw']?></th>
            <th><?= StringManipulation::addHtmlTag($data['nama_kk'], $nama_kk_search, '<span style="color:blue">', '</span>')?></th>
            <th><?= $data['nomor_rm_kk']?></th>
            <th><a class="link" href="/p/med-rec/edit-rm/index.php?document_id=<?= $data['id']?>">edit</a></th>
        </tr>   
        <?php $idnum++; ?>
        <?php endforeach ; ?>      
        <?= '</tbody>' ?>   
        <?= '</table>' ?>     
        <div class="box-pagination">
            <div class="pagination">
                <?php if( $page > 1  ):?>
                    <a href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', '<?= $page - 1 ?>' , <?= $parram ?>)">&laquo;</a>
                <?php endif;?>                            
                <?php if( $max_page > 5 ):?>
                    <!-- satu depan -->
                    <a <?= 1 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', 1 , <?= $parram ?>)">1</a>
                    <!-- tiga tengah -->                                
                    <?php if( $page  > 2 && $page < ($max_page - 1) ):?>
                        <a href="javascript:void(0)" class="sperator">...</a>
                        <a href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', '<?= $page - 1?>' , <?= $parram ?>)"><?= $page - 1 ?></a>
                        <a class="active" href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', '<?= $page ?>' , <?= $parram ?>)"><?= $page ?></a>
                        <a href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', '<?= $page + 1?>' , <?= $parram ?>)"><?= $page + 1 ?></a>
                        <a href="javascript:void(0)" class="sperator">...</a>
                    <?php elseif( $page < 4 ):?> 
                        <a <?= 2 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', 2 , <?= $parram ?>)">2</a>
                        <a <?= 3 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', 3 , <?= $parram ?>)">3</a>
                        <a href="javascript:void(0)" class="sperator">...</a>
                    <?php elseif( $page > ($max_page - 2) ):?>  
                        <a href="javascript:void(0)" class="sperator">...</a>
                        <a <?= $max_page - 2 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', '<?= $max_page - 2 ?>' , <?= $parram ?>)"><?= $max_page - 2 ?></a>
                        <a <?= $max_page - 1 == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', '<?= $max_page - 1 ?>' , <?= $parram ?>)"><?= $max_page -1 ?></a>
                    <?php endif;?>  
                    <!-- satu belakang -->
                    <a href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', '<?= $max_page ?>' , <?= $parram ?>)""><?= $max_page ?></a>
                <?php elseif( $max_page < 6 ):?>
                    <?php for ($i=1; $i <= $max_page; $i++) :?>
                        <a <?= $i == $page ? 'class="active"' : '' ?> href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', '<?= $i?>' , <?= $parram ?>)"><?= $i ?></a>
                    <?php endfor;?>
                <?php endif;?>  
                <?php if( $page < $max_page ):?>  
                    <a href="javascript:void(0)" onclick="getTableSearch('<?= $sort ?>', '<?= $order ?>', '<?= $page + 1 ?>' , <?= $parram ?>)">&raquo;</a>
                <?php endif;?>
            </div>
        </div>                 
    <?php else : ?>
    </tbody>
</table>    
    <p>data tidak ditemukan</p>
<?php endif;?>
