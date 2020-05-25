<?php 
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/apps/init.php';
?>
<?php 
    # Aunt cek
    session_start();
    $token = (isset($_SESSION['token']) ) ? $_SESSION['token'] : '';
    $auth = new Auth($token, 2);
    $user = new User($auth->getUserName());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Covid 19 Ungaran Barat</title>
    <meta name="description" content="Data Pasien Dalam Pengawasan dan Positif di Wilayah Kecamtan Ungaran Barat">
    <meta name="keywords" content="simpus lerep, info covid, kawal covid, covid ungaran, covid branjang, wilyah ungran, pdp, pasien dalam pengawasan">
    <link rel="stylesheet" href="/lib/css/main.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/control.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/card.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/alert.css">
    <script src="/lib/js/index.js"></script>
    <script src="/lib/js/bundles/keepalive.js"></script>
    <style>
        /* costume main container */
        .container.width-view{
            display: grid;
            grid-template-columns: 1fr 300px;
        }
        main{
            overflow-x: hidden
        }

        /* prototipe article - tamplate */
        .header-article{margin-bottom: 16px}
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
            overflow-x: auto;
            display: flex;
            flex-direction: row;
            padding: 16px 0;
            justify-content: center;
            gap: 12px;
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
        /* hai youtube */
    </style>
</head>
<body>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    <header>    
        <?php $active_menu = 'home' ?>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/header/header.html') ?>
    </header>
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
                        <div class="author">Angger Mulia Pradana</div>
                        <div class="time">11 April 2020</div>
                    </div>
                </div>
                <div class="media-article">
                    <div class="box cards">
                        <div class="covid-card gradient-one">
                            <div class="card title">Pasien Positif</div>
                            <div class="card content">XXX</div>
                            <div class="card note">Orang</div>
                        </div>
                        <div class="covid-card gradient-two">
                            <div class="card title">Pasien Sembuh</div>
                            <div class="card content">XXX</div>
                            <div class="card note">Orang</div>
                        </div>
                        <div class="covid-card gradient-three">
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
                                    <td>Positf</td>
                                    <td>Positf Sembuh</td>
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
    keepalive(() => {
        location.reload()
    })
    
    // menagbil data
    async function getData(url){
        const response = await fetch(url);
        return response.json();
    }

    // render card
    function renderCard(postif, sembuh, meninggal){
        let card_postif = document.querySelector('.covid-card.gradient-one .card.content');
        let card_sembuh = document.querySelector('.covid-card.gradient-two .card.content');
        let card_meninggal = document.querySelector('.covid-card.gradient-three .card.content');

        card_postif.innerHTML = postif;
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

            // mengambil nilai
            th_n.innerText = i;
            th_ds.innerText = element['desa'];
            // odp
            th_op.innerText = element['pdp']['dirawat'];
            th_os.innerText = element['pdp']['sembuh'];
            th_om.innerText = element['pdp']['meninggal'];
            // positif
            th_pp.innerText = element['positif']['dirawat'];
            th_ps.innerText = element['positif']['sembuh'];
            th_pm.innerText = element['positif']['meninggal'];

            // add classes
            th_op.classList.add("number"); th_os.classList.add("number"); th_om.classList.add("number");
            th_pp.classList.add("number"); th_ps.classList.add("number"); th_pm.classList.add("number");
            // append child
            perrent_row.appendChild(th_n); perrent_row.appendChild(th_ds);
            perrent_row.appendChild(th_op); perrent_row.appendChild(th_os); perrent_row.appendChild(th_om);
            perrent_row.appendChild(th_pp); perrent_row.appendChild(th_ps); perrent_row.appendChild(th_pm);
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
                renderCard(data['kasus_posi'], data['kasus_semb'], data['kasus_meni']);
            })
    }
</script>
</html>
