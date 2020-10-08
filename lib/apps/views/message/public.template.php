<!DOCTYPE html>
<html lang="en">
<head>
<meta content="id" name="language">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
    <link rel="stylesheet" href="/lib/css/ui/v1/table.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
    <script src="/lib/js/bundles/message.js"></script>
    <style>
        /* costume main container */
        .container.width-view{
            display: grid;
            grid-template-columns: 1fr minmax(250px, 280px);
            grid-column-gap: 24px; grid-row-gap: 24px;
        }        
        main.message{            
            overflow-x: hidden
        }
    </style>
</head>
<body>
    <header>
        <?php include(BASEURL . '/lib/components/header/header.php'); ?>
    </header>

    <div class="container width-view">
        <main class="message">
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Public</li>
                    <li>Pesan Masuk</li>
                </ul>
            </div>
            <h2>Pesan Masuk</h2>
            <div class="toolbox"></div>
            <div class="table-boxs">
                <table class="info-covid">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pengirim</th>
                            <th>Tanggal</th>
                            <th>Subject</th>
                            <th>Pesan</th>
                            <th>Info</th>
                        </tr>
                    </thead>
                    <tbody class="result">
                    </tbody>
                </table>
            </div>
            <!-- paganation -->
        </main>
        <aside class="side">

        </aside>
    </div>

    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
    </footer>

    <!-- hidden -->
    <div id="modal">
        <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/control/modal.html') ?>
    </div>
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

    // require
    ReadMessage('100', renderTable);
    function renderTable(json){
        let i = 1;
        let perrent_table = document.querySelector('tbody.result');
        perrent_table.innerHTML = '';
        json.forEach(element => {
            let perrent_row = document.createElement('tr');
            let th_n = document.createElement("th"); let th_s= document.createElement("th");
            let th_d= document.createElement("th");  let th_t= document.createElement("th");
            let th_m= document.createElement("th");  let th_i= document.createElement("th");

            // mengambil nilai
            th_n.innerText = i;
            th_s.innerText = element['sender'];
            th_d.innerText = convertPhpTime(element['date']);
            th_t.innerText = element['type'];
            th_m.innerText = element['message'];
            let meta = JSON.parse(element['meta']);
            th_i.innerText = "rating " + meta['rating'] + '/' + meta['maks-rating'];

            // append child
            perrent_row.appendChild(th_n); perrent_row.appendChild(th_s);
            perrent_row.appendChild(th_d); perrent_row.appendChild(th_t);
            perrent_row.appendChild(th_m); perrent_row.appendChild(th_i);
            // assamble
            perrent_table.appendChild(perrent_row);
            i++;
        });

        function convertPhpTime(time_convert){
            var now = new Date(Number(time_convert) * 1000);
            return now.getHours() + ':' + now.getMinutes() + ' ' + now.getDate() + '/' + (now.getMonth() + 1) + '/' + now.getFullYear();
        }
    }
</script>
</html>
