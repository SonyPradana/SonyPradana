<!DOCTYPE html>
<html lang="en">
<head>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/pages/v1.1/antrian.css">
    <link rel="stylesheet" href="/lib/css/ui/v1.1/full.style.css">
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
                        <div class="box-title" v-text="format_tanggal">Antrian Pendaftaran</div>
                        <div class="box-container">
                            <div class="antrian-container">
                                <div class="antrian-box left">
                                    <div class="big-antrian-card  card neum-blue neum-light neum-concave radius-small">
                                        <div class="card-content">
                                            <div class="details">Nomor dipanggil</div>
                                            <div class="title" v-text="last_call">Antrian Pendaftaran</div>
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
                                            <div class="title" v-html="poli_title('A')"></div>
                                            <div class="details" v-text="poli_details('A')"></div>
                                        </div>                                        
                                        <div class="gab-12pt"></div>
                                        <div class="small-antrian-card card neum-blue neum-light neum-concave radius-small">
                                            <div class="title" v-html="poli_title('B')"></div>
                                            <div class="details" v-text="poli_details('B')"></div>
                                        </div>                                        
                                        <div class="gab-12pt"></div>
                                        <div class="small-antrian-card card neum-blue neum-light neum-concave radius-small">
                                            <div class="title" v-html="poli_title('C')"></div>
                                            <div class="details" v-text="poli_details('C')"></div>
                                        </div>
                                        <div class="gab-12pt"></div>
                                        <div class="small-antrian-card card neum-blue neum-light neum-concave radius-small">
                                            <div class="title" v-html="poli_title('D')"></div>
                                            <div class="details" v-text="poli_details('D')"></div>
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
                                <div class="button-group">
                                    <button type="button" id="kia-plus" class="btn fill blue small rounded">+</button>
                                    <button type="button" id="kia-minus" class="btn fill blue small rounded">-</button>
                                    <button type="button" id="kia-reset" class="btn fill red small rounded">reset</button>
                                </div>
                            </div>
                            <div class="controller-group">
                                <span>Poli Gigi</span>
                                <div class="button-group">
                                    <button type="button" id="gigi-plus" class="btn fill blue small rounded">+</button>
                                    <button type="button" id="gigi-minus" class="btn fill blue small rounded">-</button>
                                    <button type="button" id="gigi-reset" class="btn fill red small rounded">reset</button>
                                </div>
                            </div>
                            <div class="controller-group">
                                <span>Poli Umum</span>
                                <div class="button-group">
                                    <button type="button" id="umum-plus" class="btn fill blue small rounded">+</button>
                                    <button type="button" id="umum-minus" class="btn fill blue small rounded">-</button>
                                    <button type="button" id="umum-reset" class="btn fill red small rounded">reset</button>
                                </div>
                            </div>
                            <div class="controller-group">
                                <span>Poli Lansia</span>
                                <div class="button-group">
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
                                    this.disabled = true;
                                    const plus = parseInt(app.poli[code_poli].current) + 1
                                    $json(`/api/v1.0/Antrian-Poli/dipanggil.json?poli=${code_poli}&antrian=${plus}`)
                                        .then( json => {
                                            if (json.status == 'ok' ) {
                                                creat_snackbar('berhasil ditambah');
                                                show_snackbar();
                                            }

                                            this.disabled = false;
                                        });
                                });
                            }
                            function clicker_minus(id_poli, code_poli)
                            {
                                $id(id_poli).addEventListener('click', function() {
                                    this.disabled = true;
                                    const plus = parseInt(app.poli[code_poli].current) - 1;
                                    if( plus >= 0) {
                                        $json(`/api/v1.0/Antrian-Poli/dipanggil.json?poli=${code_poli}&antrian=${plus}`)
                                            .then( json => {
                                                if (json.status == 'ok') {
                                                    creat_snackbar('berhasil dikurang');
                                                    show_snackbar();
                                                }

                                                this.disabled = false;
                                            });
                                    }
                                });
                            }
                            function clicker_reset(id_poli, code_poli)
                            {
                                $id(id_poli).addEventListener('click', function(){
                                    this.disabled = true;
                                    $json(`/api/v1.0/Antrian-Poli/reset.json?poli=${code_poli}`)
                                        .then( json => {
                                            if (json == 'ok') {
                                                $work("berhasil reset");
                                                    creat_snackbar('berhasil direset');
                                                    show_snackbar();
                                            }
                                            this.disabled = false;
                                        });
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
        
        <aside class="right-side">
            <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/widget/trivia.html') ?>
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
                },
                poli_title: function(p) {
                    return `${p} </br> ${this.poli[p].current} / ${this.poli[p].queueing}`
                },
                poli_details: function(p) {
                    return this.poli[p].times
                }
            },
            computed: {
                format_tanggal: function() {
                    return `Antrian Pendaftaran ${this.tanggal}`;
                }
            },
        });
    </script>
</body>

</html>
