<!DOCTYPE html>
<html lang="en">
<head>
  <?php include(APP_FULLPATH['component'] . 'meta/metatag.php') ?>

  <link rel="stylesheet" href="/lib/css/ui/v1.1/full.style.css">
  <script src="/lib/js/index.min.js"></script>
  <script src="/lib/js/bundles/message.js"></script>
  <script src="/lib/js/bundles/keepalive.min.js"></script>
  <script src="/lib/js/vendor/vue/vue.min.js"></script>
  <style>
    .container.width-view {
        display: grid;
        grid-template-columns: 1fr 300px;
    }
    main {
        overflow-x: auto;
    }

    .header-content .nav-bar {
      display: flex;
      align-items: center;
      height: 40px;
      margin: 12px 0;
    }

    .header-content {
      margin-bottom: 20px;
    }

    .header-content .title h1 {
      margin: 0;
    }

    .qna-content {
      margin-right: 32px;
      display: flex;
      flex-direction: column;
      gap: 24px;
      overflow-x: auto;
    }

    .qna-card {
      gap: 24px;
      cursor: default;
    }

    .perent-card {
      width: 100%;
      min-width: 320px;
      display: grid;
      grid-template-columns: 32px 32px minmax(320px, 1fr) minmax(48px, 92px);
      gap: 16px;
    }

    .counter-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      height: 32px;
    }

    .counter-box .count-title {
      font-size: 15px;
    }

    .counter-box .count-rating {
      text-align: center;
      font-weight: 700;
      font-size: 15px;
    }

    .avatar-box .avatar-text {
      border-radius: 4px;
      background-color: #e85670;
      color: #fff;
      width: 32px;
      height: 32px;
      text-align: center;
      line-height: 32px;
    }

    .info-title a {
      text-decoration: none;
    }

    .info-title a:hover {
      text-decoration: underline;
    }

    .info-title a:focus {
      border: none;
      outline: none;
    }

    .info-title a h3 {
      margin: 0;
      font-size: 20px;
      line-height: 24px;
    }

    .info-preview p {
      margin: 8px 0;
      font-size: 16px;
      color: #404348;
    }

    .details-box {
      display: flex;
      gap: 12px;
      color: #4d5156;
      min-width: 320px;
    }

    .details-box a {
      text-decoration-line: none;
      color: #404348;
    }

    .details-box a:hover {
      text-decoration-line: underline
    }

    .details-box a:focus {
      border: none;
      outline: none;
    }

    @media screen and (max-width: 767px) {
      .container.width-view {
        grid-template-columns: 1fr
      }
    }

</style>
</head>
<body>
  <header>
    <?php include(APP_FULLPATH['component'] . 'header/header.php'); ?>
  </header>

  <div class="container width-view">
    <main>
      <div class="coit breadcrumb">
        <ul class="crumb">
          <li><a href="/">Home</a></li>
          <li><a href="/stories">Question and Answer</a></li>
        </ul>
      </div>
      <div class="mainbar">
        <div class="header-content">
          <div class="title text-gray-800">
            <h1>Forum Simpus Lerep</h1>
          </div >
          <div class="nav-bar">
            <a href="/question/ask" class="btn fill blue light rounded">Buat Pertanyaan</a>
          </div>
        </div>
        <div class="qna-content" id="qna-app">
          <div class="qna-card" v-for="ask in asks">
            <div class="perent-card">
              <div class="counter-box">
                <div class="count-rating" v-text="ask.perent.vote"></div>
                <div class="count-text">Like</div>
              </div>
              <div class="avatar-box">
                <div class="avatar-text">Q</div>
              </div>
              <div class="content-box">
                <div class="info-box">
                  <div class="info-title">
                    <a v-bind:href="`/question/${ask.perent.id}/${ask.perent.slug}`">
                      <h3
                        v-text="ask.perent.title">
                      </h3>
                    </a>
                  </div>
                  <div class="info-preview">
                    <p
                      v-text="ask.perent.content"></p>
                  </div>
                </div>
                <div class="details-box">
                  <div class="detail author">
                    oleh <span class="text-blue-400" v-text="ask.perent.author"></span>
                  </div>
                  <div class="detail comment-count">
                    <a href="#"
                      v-text="`${ask.childs_id.length} commnets`"
                      v-bind:href="`/question/${ask.perent.id}/${ask.perent.slug}`"></a>
                  </div>
                  <div
                    class="detail tag text-blue-400"
                    v-text="ask.perent.tag">
                  </div>
                </div>
              </div>
              <div class="dete-box">
                <p v-text="ask.perent.date_creat"></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>

    <aside>
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

    // vue app
    const qnaApp = new Vue({
      el: '#qna-app',
      data: {
        asks: []
      },
      methods: {
        loadPost: function() {
          $json(`/API/v1.0/QuestionAnswer/get-post.json`)
            .then( json => {
              if (json.status == 'ok') {
                this.asks = json.data;
              }
            })
        },
      },
      mounted() {
        this.loadPost()
      },
    })

    function newPost() {
      window.location = '/question/ask/'
    }
</script>
</html>
