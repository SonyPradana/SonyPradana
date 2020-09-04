<?php
    use  Simpus\Auth\User;
    $author = new User("angger");
    $portal = [
        "auth"    => $this->getMiddleware()['auth'],
        "meta"     => [
            "title"         => "Info Covid 19 Ungaran Barat",
            "discription"   => "Data Pasien Dalam Pengawasan dan Positif di Wilayah Kecamtan Ungaran Barat",
            "keywords"      => "simpus lerep, info covid, kawal covid, covid ungaran, covid branjang, wilyah ungran, pdp, pasien dalam pengawasan"
        ],
        "header"   => [
            "active_menu"   => 'home',
            "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
        ],
        "contents" => [
            "article"    => [
                "display_name"          => $author->getDisplayName(),
                "display_picture_small" => $author->getSmallDisplayPicture()
            ]
        ]
    ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

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

        /* prototipe article - tamplate */
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
        .box.cards{
            overflow-x: visible;
            display: flex;
            flex-direction: row;
            justify-content: center;
            margin-bottom: 12px;
        }
        .box.cards .gap-space{
            min-width: 16px;
        }
        .media.note p{color: #a2a2a2; margin: 0}
        .table-boxs{
            display: flex;
            justify-content: center;
            overflow-x: auto;
        }
        table.info-covid{max-width: 500px; min-width: 400px;}
        .article.body{margin: 16px 0;}
        .article.body h2{margin: 8px;}

        /* tablet vie view */
        @media screen and (max-width: 767px) {
            .container.width-view{grid-template-columns: 1fr}
            .box.cards{justify-content: unset}
            .table-boxs{justify-content: unset}
        }
        @media screen and (max-width: 1000px) {
            .box.cards{
                overflow: auto;
            }
        }
        
        /* hai youtube */
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
                    <li>Covid Kabupaten Semarang</li>
                </ul>
            </div>
            <article>
                <div class="header-article">
                    <H1>Info Covid Kabupaten Semarang (Kec Ungaran Barat)</H1>
                    <div class="article breadcrumb">
                        <div class="author">
                            <img src="<?= $portal['contents']['article']['display_picture_small'] ?>" alt="@<?= $portal['contents']['article']['display_name'] ?>" srcset="">    
                            <div class="author-name"><a href="/Ourteam"><?= $portal['contents']['article']['display_name'] ?></a></div>
                        </div>
                        <div class="time">11 April 2020</div>
                    </div>
                </div>
                <div class="media-article">
                    <div class="box cards">
                        <div class="card covid-card grad-blue" id="card-positif" data-tooltips="Pasien Positif">
                            <div class="card title">Pasien Positif</div>
                            <div class="card content">XXX</div>
                            <div class="card note">Orang</div>
                        </div>
                        <div class="gap-space"></div>
                        <div class="card covid-card  grad-yellowtored" id="card-isolasi" data-tooltips="Pasien Isolasi">
                            <div class="card title">Pasien Isolasi</div>
                            <div class="card content">XXX</div>
                            <div class="card note">Orang</div>
                        </div>
                        <div class="gap-space"></div>
                        <div class="card covid-card grad-pinktoyellow" id="card-sembuh" data-tooltips="Pasien Sembuh">
                            <div class="card title">Pasien Sembuh</div>
                            <div class="card content">XXX</div>
                            <div class="card note">Orang</div>
                        </div>
                        <div class="gap-space"></div>
                        <div class="card covid-card grad-yellowtored"  id="card-meninggal" data-tooltips="Pasien Meninggal">
                            <div class="card title">Pasien Meninggal</div>
                            <div class="card content">XXX</div>
                            <div class="card note">Orang</div>
                        </div>
                    </div>
                    <div class="media note">
                        <p>Data Pasien Wilayah Kabupaten Semarang (Update Otomatis)</p>
                        <p>Sumber: corona.semarangkab.go.id </p>
                    </div>
                </div>
                <div class="article body">
                    <h2>Data Sebaran Di Desa</h2>
                    <div class="table-boxs">
                        <table>
                            <thead>
                                <tr>
                                    <td>No</td>
                                    <td>Desa / Kelurahan</td>
                                    <td>PDP</td>
                                    <td>PDP Sembuh</td>
                                    <td>PDP Meninggal</td>
                                    <td>Positif</td>
                                    <td>Isolasi</td>
                                    <td>Positif Sembuh</td>
                                    <td>Meninggal</td>
                                </tr>
                            </thead>
                            <tbody>

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
    function renderCard(postif, isolasi, sembuh, meninggal){
        let card_postif = document.querySelector('#card-positif .card.content');
        let card_isolasi = document.querySelector('#card-isolasi .card.content');
        let card_sembuh = document.querySelector('#card-sembuh .card.content');
        let card_meninggal = document.querySelector('#card-meninggal .card.content');

        card_postif.innerHTML = postif;
        card_isolasi.innerHTML = isolasi;
        card_sembuh.innerHTML = sembuh;
        card_meninggal.innerHTML = meninggal;
    }
    function renderTable(arr){
        let i = 1;
        let perrent_table = document.querySelector('tbody');
        perrent_table.innerHTML = '';
        arr.forEach(element => {
            let perrent_row = document.createElement('tr');
            let th_n = document.createElement("th"); let th_ds= document.createElement("th");
            let th_op = document.createElement("th");  let th_os = document.createElement("th"); let th_om = document.createElement("th");
            let th_pp = document.createElement("th");  let th_ps = document.createElement("th"); let th_pm = document.createElement("th");
            let th_pi = document.createElement("th");

            // mengambil nilai
            th_n.innerText = i;
            th_ds.innerText = element['desa'];
            // odp
            th_op.innerText = element['pdp']['dirawat'];
            th_os.innerText = element['pdp']['sembuh'];
            th_om.innerText = element['pdp']['meninggal'];
            // positif
            th_pp.innerText = element['positif']['dirawat'];
            th_pi.innerText = element['positif']['isolasi'];
            th_ps.innerText = element['positif']['sembuh'];
            th_pm.innerText = element['positif']['meninggal'];

            // add classes
            th_op.classList.add("number"); th_os.classList.add("number"); th_om.classList.add("number");
            th_pp.classList.add("number"); th_pi.classList.add("number"); th_ps.classList.add("number"); th_pm.classList.add("number");
            // append child
            perrent_row.appendChild(th_n); perrent_row.appendChild(th_ds);
            perrent_row.appendChild(th_op); perrent_row.appendChild(th_os); perrent_row.appendChild(th_om);
            perrent_row.appendChild(th_pp); perrent_row.appendChild(th_pi); perrent_row.appendChild(th_ps); perrent_row.appendChild(th_pm);
            
            // assamble
            perrent_table.appendChild(perrent_row);
            i++;
        })
    }

    window.onload = () => {
        getData('/lib/ajax/json/public/covid-kab-semarang/info/?kecamatan=ungaran-barat')
            .then( data => {
                renderTable(data['data']);
            });
        getData('/lib/ajax/json/public/covid-kab-semarang/info/')
            .then( data => {
                renderCard(data['kasus_posi'], data['kasus_isol'], data['kasus_semb'], data['kasus_meni']);
                grapInfo(data);
            })

    }

    async function grapInfo(data){
        await data['data'].forEach(event => {
            let dom_posi = document.querySelector('#card-positif')
            let posi = dom_posi.getAttribute('data-tooltips')
            if( event['kasus_posi'] != 0){
                dom_posi.setAttribute('data-tooltips', posi + ', ' + event['kecamatan'] + `(${event['kasus_posi']})`)
            }
            let dom_isol = document.querySelector('#card-isolasi')
            let isol = dom_isol.getAttribute('data-tooltips')
            if( event['kasus_isol'] != 0){
                dom_isol.setAttribute('data-tooltips', isol + ', ' + event['kecamatan'] + `(${event['kasus_isol']})`)
            }
            let dom_semb = document.querySelector('#card-sembuh')
            let semb = dom_semb.getAttribute('data-tooltips')
            if( event['kasus_semb'] != 0){
                dom_semb.setAttribute('data-tooltips', semb + ', ' + event['kecamatan'] + `(${event['kasus_semb']})`)
            }
            let dom_meni = document.querySelector('#card-meninggal')
            let meni = dom_meni.getAttribute('data-tooltips')
            if( event['kasus_meni'] != 0){
                dom_meni.setAttribute('data-tooltips', meni + ', ' + event['kecamatan'] + `(${event['kasus_meni']})`)
            }
        })
    }
</script>
</html>
