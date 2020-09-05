<?php 
    use  Simpus\Auth\User;
    require_once BASEURL . '/lib/ajax/json/public/jadwal-imunisasi/controller/jadwalKIA.php';

    $author = new User("angger");
    $imun   = new jadwalKIA(date('m'), date('Y'));

    $raw_data   = $imun->getData();

    $portal = [
        "auth"    => $this->getMiddleware()['auth'],
        "meta"     => [
            "title"         => "Jadwal Pelayanan di Poli KIA - Simpus Lerep",
            "discription"   => "Jadwal pelayanan imunisasi anak di Poli KIA",
            "keywords"      => "simpus lerep, puskesmas lerep,jadwal imunisasi, imunusasi, kia anak, jadwal, BCG, Campak, Rubella (MR), Hib, HB, DPT, IPV"
        ],
        "header"   => [
            "active_menu"   => 'home',
            "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
        ],
        "contents" => [
            "article"    => [
                "display_name"          => $author->getDisplayName(),
                "display_picture_small" => $author->getSmallDisplayPicture()
            ],
            "jadwal_pertama"    => explode(' ', $raw_data['jadwal'][0]),
            "jadwal_ketiga"     => explode(' ', $raw_data['jadwal'][2]),
            "jumat_pertama"     => implode(", ", $raw_data['jumat pertama']),
            "avilable_month"    => $imun->getAvilabeMonth(),
            "jadwal"            => $raw_data['jadwal'],
            "data_imun"         => $raw_data['data']
        ]
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>
    <meta name="robots" content="all,index,follow">
    
    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <link rel="stylesheet" href="/lib/css/ui/v1.1/cards.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <style>
        /* costume main container */
        .container.width-view{
            display: grid;
            grid-template-columns: 1fr 300px;
        }
        main{
            overflow-x: hidden;
        }
        /* Templatebox container */
        .cards-box .box-container{
            display: grid;
            grid-template-columns: minmax(300px, 320px) 16px minmax(300px, 320px);
        }
        
        .header-article{margin-bottom: 24px}
        .header-article h1{
            font-size: 2.3rem;
            font-weight: 700;
        }
        .header-article .article.breadcrumb{
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px; grid-gap: 8px;
        }
        .header-article .article.breadcrumb > div{ font-size: 1rem; color: #9aa6ad}

        /* .media-article{margin-right: 20px;} */
        .media-article{ padding: 0 16px;}
        .media.note p{color: #a2a2a2; margin: 0}
        .table-boxs{
            display: flex;
            justify-content: center;
            overflow-x: auto;
        }
        table.info-covid{max-width: 500px; min-width: 400px;}
        .article.body{margin: 16px 0;}
        .article.body h2{
            margin: 8px;
            text-align: center;
        }

        .gap{
            width: 16px; height: 16px;
            min-height: 16px; min-width: 16px;
        }
        @media screen and (max-width: 767px) {
            .container.width-view{grid-template-columns: 1fr}
            .table-boxs{justify-content: unset}
                        
            .cards-box .box-container{
                grid-template-columns: 1fr;
                grid-template-rows: auto 16px auto;
            }
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

    <div class="container width-view">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Info</li>
                    <li>Jadwal Pelayanan</li>
                </ul>
            </div>
            <article>
                <div class="header-article">
                    <H1>Jadwal Pelayanan Poli KIA Anak (Imunisasi) Setiap Hari Jumat</H1>
                    <div class="article breadcrumb">
                        <div class="author">
                            <img src="<?= $author->getSmallDisplayPicture() ?>" alt="@<?= $author->getDisplayName() ?>" srcset="">    
                            <div class="author-name"><a href="/Ourteam"><?= $author->getDisplayName() ?></a></div>
                        </div>
                        <div class="time">18 Juni 2020</div>
                    </div>
                </div>
                <div class="media-article">
                    <div class="cards-box blue">
                        <div class="box-title">Jadwal Bulan Ini (<?= date('M') ?>)</div>
                        <div class="box-container">
                            <div class="card event neum-blue neum-light neum-concave radius-small" id="jumat-pertama">
                                <div class="card-time">
                                    <div class="mount"><?= $portal['contents']['jadwal_pertama'][1] ?></div>
                                    <div class="day"><?= $portal['contents']['jadwal_pertama'][0] ?></div>
                                </div>
                                <div class="gab"></div>
                                <div class="card-content">
                                    <div class="title">Imunisasi
                                    </div>
                                    <div class="description"><?= $portal['contents']['jumat_pertama'] ?></div>
                                </div>
                            </div>
                            <div class="gab"></div>
                            <div class="card event neum-blue neum-light neum-concave radius-small" id="jumat-ketiga">
                                <div class="card-time">
                                    <div class="mount"><?= $portal['contents']['jadwal_ketiga'][1] ?></div>
                                    <div class="day"><?= $portal['contents']['jadwal_ketiga'][0] ?></div>
                                </div>
                                <div class="gab"></div>
                                <div class="card-content">
                                    <div class="title">Imunisasi
                                    </div>
                                    <div class="description"><?= $portal['contents']['jumat_pertama'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="media note">
                        <p>Jadwal Imunisasi Anak </p>
                        <p>Sumber: Puskesmas Lerep</p>
                    </div>
                </div>
                <div class="article body">
                    <div class="form-box">
                        <label for="input-pilih-bulan">Lihat Imunisasi Bulan Lainnya: </label>
                        <select name="pilih-bulan" id="input-pilih-bulan">
                            <option hidden selected>Pilih Bulan</option>
                            <?php foreach( $portal['contents']['avilable_month'] as $row ): ?>
                                <option value="<?= $row ?>"><?= date('F', mktime(0, 0, 0, $row, 10)); ?> 2020</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <h2>Jadwal Pelayanan</h2>
                    <div class="table-boxs">
                        <table class="jadwal-imunisasi">
                            <thead>
                                <tr>
                                    <td>No</td>
                                    <td>Jenis Vaksin</td>
                                    <!-- fleksible header -->
                                    <?php foreach( $portal['contents']['jadwal'] as $row): ?>
                                        <td>Jumat - <?= $row ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $i = 1;
                                    foreach( $portal['contents']['data_imun'] as $jenis_vaksin => $list_jadwal_vaksin ) {
                                        echo '<tr>';
                                        echo "<td>$i</td>";
                                        echo "<td>$jenis_vaksin</td>";
                                        foreach( $portal['contents']['jadwal'] as $element){
                                            $tb = in_array($element, $list_jadwal_vaksin) ? "Ya" : "Tidak";
                                            echo "<td>$tb</td>";
                                        }
                                        echo '</tr>';
                                        $i++;
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>  

            </article>
        </main>
        <aside class="right-side">

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
    
    // menagbil data
    async function getData(url){
        const response = await fetch(url);
        return response.json();
    }

    // render card
    function renderCard(data){
        if( Array.isArray( data['jadwal'])) return false;
        let first_card_decs = document.querySelector('#jumat-pertama .description');
        let third_card_decs = document.querySelector('#jumat-ketiga .description');
        let first_card_mount = document.querySelector('#jumat-pertama .mount');
        let third_card_mount = document.querySelector('#jumat-ketiga .mount');
        let first_card_day = document.querySelector('#jumat-pertama .day');
        let third_card_day = document.querySelector('#jumat-ketiga .day');

        first_card_decs.innerHTML = data['jumat pertama'].join(', ')
        third_card_decs.innerHTML = data['jumat pertama'].join(', ')

        let first_day = data['jadwal'][0].split(' ');
        let third_day = data['jadwal'][2].split(' ');

        first_card_mount.innerHTML = first_day[1]
        first_card_day.innerHTML = first_day[0] 
        third_card_mount.innerHTML = third_day[1]
        third_card_day.innerHTML = third_day[0] 
    }
    function renderTable(arr){
        let perrent_table = document.querySelector('tbody');
        perrent_table.innerHTML = '';

        // membuat header table
        let dom_perent_header_tb = document.querySelector('.jadwal-imunisasi thead tr')
        let vdom_header_nomor  = document.createElement('td')
        let vdom_header_vaksin = document.createElement('td')
        dom_perent_header_tb.innerHTML = null
        vdom_header_nomor.innerText  = "No"
        vdom_header_vaksin.innerText = "Jenis Vaksin"
        dom_perent_header_tb.appendChild( vdom_header_nomor )
        dom_perent_header_tb.appendChild( vdom_header_vaksin)
        
        let i = 1
        arr['jadwal'].forEach(element => {
            let vdom_td = document.createElement('td')
            vdom_td.innerText = `Jumat ${i} ~ ${element}`

            dom_perent_header_tb.appendChild(vdom_td)
            i++
        })

        let dom_perent_body = document.querySelector('.jadwal-imunisasi tbody')
        dom_perent_body.innerHTML = null
        let data = arr['data']
        i= 1
        for (const row in data) {
            let vdom_perrent_tr = document.createElement('tr')
            let vdom_td_nomor  = document.createElement('td')
            let vdom_td_vaksin = document.createElement('td')

            vdom_td_nomor.innerText = i
            vdom_td_vaksin.innerText = row
            
            vdom_perrent_tr.appendChild( vdom_td_nomor )
            vdom_perrent_tr.appendChild( vdom_td_vaksin )
            dom_perent_body.appendChild(vdom_perrent_tr)
            arr['jadwal'].forEach(element => {
                let vdom_td_jadwal = document.createElement('td')

                vdom_td_jadwal.innerText =  data[`${row}`].includes(element) ? 'Ya' : 'Tidak';
                vdom_perrent_tr.appendChild( vdom_td_jadwal )
            })
            i++
        }
    }

    const selectElement = document.querySelector('#input-pilih-bulan');
    selectElement.addEventListener('change', (event) => {
        getData(`/lib/ajax/json/public/jadwal-imunisasi/?month=${event.target.value}`)
            .then( data => {
                renderCard(data);
                renderTable(data);
            })
    });
</script>
</html>
