<?php 
use Simpus\Auth\User;

$author = new User("angger");
$portal = [
    "auth" => $this->getMiddleware()['auth'],
    "meta"  => [
        "title"   => "Simulasi Antrian Online",
        "discription"   => "Display Antrian Digital Puskesmas Lerep (Tahap Uji Coba)",
        "keywords"   => "simpus lerep, puskesmas lerep, Antrian Online, BPJS, Display antrian, Nomor Urut, Poli Umum, Poli Lansia, Poli KIA Ibu dan Anak"
    ],
    "header"   => [
        "active_menu"   => 'home',
        "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
    ],
    "contents" => [
        "article" => [
            "display_name"    => $author->getDisplayName(),
            "display_picture_small" => $author->getSmallDisplayPicture(),
            'article_create' => '15 Oktober 2020',
            'title' => 'Simulasi Antrian online'
        ],
    ]
];

$content = (object) $portal['contents'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <style>
        .container.width-view {
            display: grid;
            grid-template-columns: 1fr 300px;
        }
        main {
            overflow-x: auto;
        }

        @media screen and (max-width: 767px) {
            .container.width-view {
                grid-template-columns: 1fr
            }
        }
    </style>
    <link rel="stylesheet" href="/lib/css/pages/v1.1/antrian.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
    <script src="/lib/js/vendor/vue/vue.min.js"></script>
    <script src="/lib/js/vendor/pusher/pusher.min.js"></script>
</head>

<body>
    <header>
        <?php include(BASEURL . '/lib/components/header/header.php'); ?>
    </header>

    <div class="container width-view">
        <main>
            <div class="coit breadcrumb">
                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li>Info</li>
                    <li>Simulasi Antrian Online</li>
                </ul>
            </div>

            <article>
                <div class="article-header">
                    <h1><?= $content->article['title'] ?></h1>
                    <div class="article breadcrumb">
                        <div class="author">
                            <img src="<?= $content->article['display_picture_small'] ?>" alt="@<?= $content->article['display_name'] ?>">    
                            <div class="author-name"><a href="/Ourteam"><?= $content->article['display_name'] ?></a></div>
                        </div>
                        <div class="time"><?= $content->article['article_create'] ?></div>
                    </div>
                </div>
                <div id="app" class="article-media">                    
                    <div class="cards-box blue">
                        <div class="box-title">Antrian Pendaftaran {{ tanggal }}</div>
                        <div class="box-container">
                            <div class="antrian-container">
                                <div class="antrian-box left">
                                    <div class="big-antrian-card  card neum-blue neum-light neum-concave radius-small">
                                        <div class="card-content">
                                            <div class="details">Nomor dipanggil</div>
                                            <div class="title">{{ last_call }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="gab-12pt"></div>
                                <div class="antrian-small-title">
                                    Dalam Antrian
                                </div>
                                <div class="antrian-box right">
                                    <div class="antraian-container-small">                                        
                                        <div class="small-antrian-card card neum-blue neum-light neum-concave radius-small">
                                            <div class="title">A </br> {{ poli['A'].current }} / {{ poli['A'].queueing }}</div>
                                            <div class="details">{{ poli['A'].times}}</div>
                                        </div>                                        
                                        <div class="gab-12pt"></div>
                                        <div class="small-antrian-card card neum-blue neum-light neum-concave radius-small">
                                            <div class="title">B </br> {{ poli['B'].current }} / {{ poli['B'].queueing }}</div>
                                            <div class="details">{{ poli['B'].times}}</div>
                                        </div>                                        
                                        <div class="gab-12pt"></div>
                                        <div class="small-antrian-card card neum-blue neum-light neum-concave radius-small">
                                            <div class="title">C </br> {{ poli['C'].current }} / {{ poli['C'].queueing }}</div>
                                            <div class="details">{{ poli['C'].times}}</div>
                                        </div>
                                        <div class="gab-12pt"></div>
                                        <div class="small-antrian-card card neum-blue neum-light neum-concave radius-small">
                                            <div class="title">D </br> {{ poli['D'].current }} / {{ poli['D'].queueing }}</div>
                                            <div class="details">{{ poli['D'].times}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="media note">
                        <ul>
                            <p>Keterangan</p>
                            <li>A: Poli KIA (Kesehatan Ibu dan Anak), Imunisasi</li>
                            <li>B: Poli Gigi dan Mulut</li>
                            <li>C: Poli Umum, Pemreriksan Kesehatan</li>
                            <li>D: Poli Lansia, Untuk Usia diatas 60 Tahun</li>
                        </ul>
                    </div>
                </div>
    
                <div class="article-body">
                    <?php if ($portal['auth']['login']): ?>
                        <h2>Tombol Antrian</h2>
                        <div class="antrian-contoller">
                            <div class="controller-group">
                                <span>Poli KIA</span>
                                <div class="buton-group">
                                    <button type="button" id="kia-plus" class="btn fill blue small rounded">+</button>
                                    <button type="button" id="kia-minus" class="btn fill blue small rounded">-</button>
                                    <button type="button" id="kia-reset" class="btn fill red small rounded">reset</button>
                                </div>
                            </div>
                            <div class="controller-group">
                                <span>Poli Gigi</span>
                                <div class="buton-group">
                                    <button type="button" id="gigi-plus" class="btn fill blue small rounded">+</button>
                                    <button type="button" id="gigi-minus" class="btn fill blue small rounded">-</button>
                                    <button type="button" id="gigi-reset" class="btn fill red small rounded">reset</button>
                                </div>
                            </div>
                            <div class="controller-group">
                                <span>Poli Umum</span>
                                <div class="buton-group">
                                    <button type="button" id="umum-plus" class="btn fill blue small rounded">+</button>
                                    <button type="button" id="umum-minus" class="btn fill blue small rounded">-</button>
                                    <button type="button" id="umum-reset" class="btn fill red small rounded">reset</button>
                                </div>
                            </div>
                            <div class="controller-group">
                                <span>Poli Lansia</span>
                                <div class="buton-group">
                                    <button type="button" id="lansia-plus" class="btn fill blue small rounded">+</button>
                                    <button type="button" id="lansia-minus" class="btn fill blue small rounded">-</button>
                                    <button type="button" id="lansia-reset" class="btn fill red small rounded">reset</button>
                                </div>
                            </div>
                            <div class="controller-group">
                                <span>Reset Semua</span>
                                <div class="button-group">
                                    <button type="button" id="reset-all" class="btn fill red small rounded">reset</button>
                                </div>
                            </div>
                        </div>
                        <script>
                            function clicker_plus(id_poli, code_poli)
                            {
                                $id(id_poli).addEventListener('click', function() {
                                    const plus = parseInt(app.poli[code_poli].current) + 1
                                    $json(`/api/v1.0/Antrian-Poli/dipanggil.json?poli=${code_poli}&antrian=${plus}`)
                                        .then( json => {
                                            $work("berhasil ditambahkan")
                                        })
                                });
                            }
                            function clicker_minus(id_poli, code_poli)
                            {
                                $id(id_poli).addEventListener('click', function() {
                                    const plus = parseInt(app.poli[code_poli].current) - 1
                                    $json(`/api/v1.0/Antrian-Poli/dipanggil.json?poli=${code_poli}&antrian=${plus}`)
                                        .then( json => {
                                            $work("berhasil dikurangi");
                                        })
                                });
                            }
                            function clicker_reset(id_poli, code_poli)
                            {
                                $id(id_poli).addEventListener('click', function(){
                                    $json(`/api/v1.0/Antrian-Poli/reset.json?poli=${code_poli}`)
                                        .then( json => {
                                            $work("berhasil reset");
                                        })
                                });
                            }
                            
                            clicker_plus('kia-plus', 'A');      clicker_minus('kia-minus', 'A');        clicker_reset('kia-reset', 'A');
                            clicker_plus('gigi-plus', 'B');     clicker_minus('gigi-minus', 'B');       clicker_reset('gigi-reset', 'B');
                            clicker_plus('umum-plus', 'C');     clicker_minus('umum-minus', 'C');       clicker_reset('umum-reset', 'C');
                            clicker_plus('lansia-plus', 'D');   clicker_minus('lansia-minus', 'D');     clicker_reset('lansia-reset', 'D');

                            $id('reset-all').addEventListener('click', function(){
                                app.reset_app();
                            });
                        </script>
                    <?php endif; ?>

                </div>
            </article>


        </main>
        
        <aside>
        
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

        // pusher
        Pusher.logToConsole = false;

        let pusher = new Pusher('6c9ed3de96e572726af9', {
            cluster: 'ap1'
        });

        let channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function (data) {            
            if(! Array.isArray(data)) {
                app.tanggal = data.date_time;
                app.poli[data.poli].current = data.current;
                app.poli[data.poli].times = data.current_times;
                app.poli[data.poli].queueing = data.queueing;
                app.poli[data.poli].queueing_times = data.queueing_times;

                app.last_call = ` ${data.poli} ${data.current}`;
            }
        });

        // Vue application
        const app = new Vue({
            el: '#app',
            data: {
                test: [],
                tanggal: '',
                last_call: '--',
                poli: {
                    A: {
                        name: 'kia',
                        current: '--',
                        times: '--',
                        queueing: '--',
                        queueing_times: '--',
                    },
                    B: {
                        name: 'gigi',
                        current: '--',
                        times: '--',
                        queueing: '--',
                        queueing_times: '--',
                    },
                    C: {
                        name: 'umum',
                        current: '--',
                        times: '--',
                        queueing: '--',
                        queueing_times: '--',
                    },
                    D: {
                        name: 'lansia',
                        current: '--',
                        times: '--',
                        queueing: '--',
                        queueing_times: '--',
                    },
                },
            },
            mounted (){
                $json('/api/v1.0/Antrian-Poli/antrian.json')
                    .then( json => {
                        let data = json.data;
                        let biggest = 0;

                        data.forEach(element => {
                            let poli = element.poli;
                            this.poli[poli].current = element.current;
                            this.poli[poli].times = element.current_times;
                            this.poli[poli].queueing = element.queueing;
                            this.poli[poli].queueing_times = element.queueing_times;

                            this.last_call = `${json.last.poli} ${json.last.current}`;
                            this.tanggal = json.date;
                        });
                        this.tanggal = data[0].date_time;
                    })
            },
            methods: {
                reset_app: function() {
                    $json('/api/v1.0/Antrian-Poli/reset.json?poli=full_reset')
                        .then( json => {
                            let data = json.data;
                            let biggest = 0;

                            data.forEach(element => {
                                let poli = element.poli;
                                this.poli[poli].current = element.current;
                                this.poli[poli].times = element.current_times;
                                this.poli[poli].queueing = element.queueing;
                                this.poli[poli].queueing_times = element.queueing_times;

                                this.last_call = `--`;
                                this.tanggal = json.date;
                            });
                            this.tanggal = data[0].date_time;
                        })
                }
            }
        });
    </script>
</body>

</html>
