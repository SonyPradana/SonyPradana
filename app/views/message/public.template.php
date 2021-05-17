<!DOCTYPE html>
<html lang="en">
<head>
<meta content="id" name="language">
    <?php include(APP_FULLPATH['component'] . 'meta/metatag.php') ?>

    <link rel="stylesheet" href="/lib/css/ui/v1.1/full.style.css">
    <script src="/lib/js/index.min.js"></script>
    <script src="/lib/js/bundles/keepalive.min.js"></script>
    <script src="/lib/js/bundles/message.js"></script>
    <script src="/lib/js/vendor/vue/vue.min.js"></script>
    <style>
        .container.width-view {
            display: grid;
            grid-template-columns: 1fr 300px;
        }

        main {
            overflow-x:auto
        }

        @media screen and (max-width: 767px) {
            .container.width-view {
                grid-template-columns: 1fr
            }
        }

        section.group-input {
            margin: 12px 0;
        }
    </style>
</head>
<body>
    <header>
        <?php include(APP_FULLPATH['component'] . 'header/header.php'); ?>
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
            <div id="message-app">
                <h2>Pesan Masuk</h2>
                <div class="toolbox">
                    <section class="group-input">
                        <label for="input-filter">Filter Pesan</label>
                        <select class="textbox outline blue rounded light" id="input-filter" v-on:change="onChange($event)">
                            <option value="">all</option>
                            <option selected value="contact">contact</option>
                            <option value="review">review</option>
                        </select>
                    </section>
                </div>
                <div class="table-boxs" id="table-message">
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
                            <tr v-for="(message, index) in messages" :key="message.date">
                                <td v-text="(info.page * info.limit) + (index + 1) - info.limit"></td>
                                <td v-text="message.sender">Pengirim</td>
                                <td v-text="date_format(message.date)">Tanggal Masuk</td>
                                <td v-text="message.type">Jenis Pesan</td>
                                <td v-text="message.message">Isi Pesan</td>
                                <td v-text="parse_meta(message.meta)">Nilai</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- paganation -->
                <div class="box-pagination">
                    <div class="pagination" id="pagination">
                    </div>
                </div>
            </div>
        </main>

        <aside class="right-side">
          <?php include(APP_FULLPATH['component'] . 'widget/stories.html') ?>
          <?php include(APP_FULLPATH['component'] . 'widget/trivia.html') ?>
        </aside>
    </div>

    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include(APP_FULLPATH['component'] . 'footer/footer.html') ?>
    </footer>

    <!-- hidden -->
    <div id="modal">
        <?php include(APP_FULLPATH['component'] . 'control/modal.html') ?>
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

    const messageApp = new Vue({
        el: '#message-app',
        data: {
            option: {
                filter: 'contact'
            },
            messages: Array(),
            info: {
                maks_data: 0,
                page: 0,
                limit: 0,
                maks_page: 0
            }
        },
        methods: {
            refresh_message: function(page = 1, limit = 10) {
                $json(`/api/ver1.0/Message/read.json?page=${page}&limit=${limit}&type=${this.option.filter}`)
                    .then(json => {
                        if (json.status == 'ok') {
                            this.messages = json.data;
                            this.info = json.info;

                            this.render_pagination()
                        }
                })
            },
            date_format: function(timestamp_tobeconvert) {
                const timestamp_rightnow = new Date(Number(timestamp_tobeconvert) * 1000);
                return `${timestamp_rightnow.getHours()}:${timestamp_rightnow.getMinutes()} ${timestamp_rightnow.getDate()}/${timestamp_rightnow.getMonth() + 1}/${timestamp_rightnow.getFullYear()}`;
            },
            parse_meta: function(json_data) {
                const json = JSON.parse(json_data);
                return `rating ${json['rating'] ?? 0}/${json['maks-rating'] ?? 0}`;
            },
            onChange(event) {
                this.option.filter = event.target.value;
                this.refresh_message();
            },
            render_pagination: function (){
                let dom_paginaiton = document.querySelector('#pagination')
                dom_paginaiton.innerHTML = '<!-- pagination -->'

                let creat_p = (page, text) => {
                    let p = document.createElement('a')
                    if (page == this.info.page) {
                        p.className = 'active';
                    }
                    p.href = '#table-message';
                    p.addEventListener('click', () => {
                        this.info.page = page;
                        this.refresh_message(page, this.maks_page);
                    })
                    p.innerHTML = text;
                    dom_paginaiton.appendChild(p);
                }
                let creat_s = () => {
                    let s = document.createElement('a');
                    s.href = 'javascript:void(0)';
                    s.className = 'sperator';
                    s.innerText = '...';
                    dom_paginaiton.appendChild(s);
                }

                if (this.info.page > 1) {
                    creat_p(this.info.page - 1, '&laquo;');
                }
                if (this.info.maks_page > 5) {
                    // satu didepan
                    creat_p(1, 1);
                    // tiga ditengah
                    if (this.info.page > 2 && this.info.page < (this.info.maks_page -1)) {
                        creat_s();
                        // prev page, curret page, next page
                        creat_p(this.info.page - 1, this.info.page - 1);
                        creat_p(this.info.page, this.info.page);
                        creat_p(this.info.page + 1, this.info.page + 1);
                        creat_s();
                    } else if ( this.info.page < 4) {
                        // page 2 & 3
                        creat_p(2, 2);
                        creat_p(3, 3);
                        creat_s();
                    } else if ( this.info.page > ( this.info.maks_page - 2 )) {
                        // 2 dari belakang
                        creat_s();
                        creat_p(this.info.maks_page - 2, this.info.maks_page - 2);
                        creat_p(this.info.maks_page - 1, this.info.maks_page - 1);
                    }
                    // satu dibelakang
                    creat_p(this.info.maks_page, this.info.maks_page);
                } else if (this.info.maks_page < 6) {
                    // page 1-5
                    for (let i = 1; i <= this.info.maks_page; i++) {
                        creat_p(i, i);
                    }
                }
                if (this.info.page < this.info.maks_page) {
                    creat_p(this.info.page + 1, '&raquo;');
                }
            }
        },
        mounted() {
            this.refresh_message()
        }
    });
</script>
</html>
