<?php
use Helper\String\Manipulation;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>

  <link rel="stylesheet" href="/lib/css/ui/v1.1/full.style.css">
  <script src="/lib/js/index.min.js"></script>
  <script src="/lib/js/bundles/message.js"></script>
  <script src="/lib/js/bundles/keepalive.min.js"></script>
  <script src="/lib/js/vendor/alpine/alpine.min.js" defer></script>
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

    .question-title h1 {
      color: #1F2937;
      font-size: 32px;
    }

    .info p {
      color: #6B7280;
      font-size: 16px;
    }

    .question-content {
      display: flex;
      gap: 20px;
    }

    .vote-box {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 12px;
    }

    .vote-btn {
      width: 0;
      height: 0;
      border-left: 16px solid transparent;
      border-right: 16px solid transparent;
    }

    .like-btn {
      border-bottom: 16px solid #9CA3AF;
    }
    .like-btn:hover {
      border-bottom: 16px solid #4B5563;
    }

    .dislike-btn {
      border-top: 16px solid #9CA3AF;
    }

    .dislike-btn:hover {
      border-top: 16px solid #4B5563;
    }

    .vote-count {
      color: #6B7280;
    }

    .question-box p {
      color: #1F2937;
      font-size: 20px;
      margin: 0;
    }

    .sub-header h2,
    .form-header h3 {
      color: #374151;
      font-size: 24px;
    }

    .answers {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .answer {
      display: flex;
      gap: 12px;
      padding: 20px;
    }

    .answer:not(:first-child) {
      border-top: 1px solid #ddd;
    }

    .answer h4 {
      margin: 0;
      font-size: 20px;
      color: #1F2937;
    }

    .answer-respone {
      display: flex;
      gap: 12px;
    }

    .answer-discription p {
      font-size: 18px;
      color: #1F2937;
    }

    .form-box {
      padding: 0 20px;
    }

    form {
      max-width: 447px;
      min-width: 320px;
    }


</style>
</head>
<body>
  <header>
    <?php include(BASEURL . '/resources/components/header/header.php'); ?>
  </header>

  <div class="container width-view">
    <main>
      <div class="coit breadcrumb">
        <ul class="crumb">
          <li><a href="/">Home</a></li>
          <li><a href="/QnA">QnA</a></li>
          <li>Thread</li>
        </ul>
      </div>
      <div class="header-content">
        <div class="question-title">
          <h1><?= $content->perent['title'] ?></h1>
        </div>
        <div class="info">
          <p>Ditanyakan <?= $content->perent['date_creat'] ?> oleh <?= $content->perent['author'] ?></p>
        </div>
      </div>
      <div class="question-content" x-data="vote()">
        <div class="vote-box">
          <div class="like-btn vote-btn" x-on:click="perentLike()"></div>
          <div class="vote-count" x-text="perent.like"></div>
          <div class="dislike-btn vote-btn" x-on:click="perentDislike()"></div>
        </div>
        <div class="question-box">
          <p><?= $content->perent['content'] ?></p>
        </div>
      </div>
      <div class="answers-content">
        <div class="sub-header">
          <h2>Answer</h2>
        </div>
        <div class="answers">
          <?php foreach ($content->answers as $answer): ?>
            <div class="answer">
              <div class="vote-box">
                <div class="like-btn vote-btn" onclick="vote().childLike(<?= $answer['id'] ?>)"></div>
                <div id="child-vote-<?= $answer['id'] ?>" class="vote-count"><?= $answer['like_post'] ?></div>
                <div class="dislike-btn vote-btn" onclick="vote().childDislike(<?= $answer['id'] ?>)"></div>
              </div>
              <div class="answer-box">
                <div class="answer-title">
                  <h4><?= $answer['title'] ?></h4>
                </div>
                <div class="answer-discription">
                  <p><?= $answer['content'] ?></p>
                </div>
                <div class="answer-respone">
                  <a href="/question/answer/<?= $answer['id'] ?>"><?= $answer['childs'] ?> comment</a>
                  <a href="/question/<?= $answer['id'] ?>/<?= Manipulation::slugify($answer['title']) ?>">view</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="new-post">
        <div class="form-header">
          <h3>Jawaban kamu</h3>
        </div>
        <div class="form-box">
            <form  id="new-post" x-data="newPost()" x-on:submit.prevent="submitData">
              <label class="v-group-input">
                Nama
                <input class="textbox outline blue rounded small" type="text" x-model="data.name">
              </label>

              <label class="v-group-input">
                Judul
                <input class="textbox outline blue rounded small" type="text" x-model="data.title">
              </label>

              <label class="v-group-input">
                Komentar
                <textarea class="textbox outline blue rounded small" type="text" x-model="data.content"></textarea>
              </label>

              <section class="h-group-input wrap">
                <img src="<?= $content->captcha_image ?>" id="captcha-image" alt="captcha" width="200" height="70">
                <label class="v-group-input">
                  Captcha
                  <input type="text" class="textbox outline blue rounded small"
                    x-model="data.scrf_secret">
                </label>
              </section>


              <div class="form-footer left">
                <button class="btn fill blue light rounded" type="submit">Kirim</button>
              </div>

            </form>
        </div>

      </div>
    </main>
    <aside class="right-side"></aside>
  </div>

  <div class="gotop" onclick="gTop()"></div>
  <footer>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/footer/footer.html') ?>
  </footer>

  <!-- hidden -->
  <div id="modal">
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/control/modal.html') ?>
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

  function newPost() {
    return {
      data: {
        scrf_key: '<?= $content->scrf_key ?>',
        scrf_secret: '',
        name: '',
        perent_id: '<?= $content->perent['id'] ?>',
        tag: '<?= $content->perent['tag'] ?>',
        title: '',
        content: ''
      },
      onSubmit: false,
      submitData() {
        this.onSubmit = true;
        success = false;

        $json('/api/ver1.0/QuestionAnswer/submit-post.json', {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          mode: 'cors',
          cache: 'no-cache',
          credentials: 'same-origin',
          redirect: 'follow',
          referrerPolicy: 'no-referrer',
          body: JSON.stringify(this.data)
        })
        .then(json => {
          if (json.status == 'ok') {
            console.log('Form sucessfully submitted!');

            // clear field
            this.data.name = '';
            this.data.title = '';
            this.data.content = '';

            success = true;
          }
        })
        .finally(() => {
          $work('Done!');

          this.onSubmit = false
          if (success) {
            location.reload();
          } else {
            this.captchaProvider();
          }
        })
      },
      captchaProvider() {
        $json('/APi/ver1.0/Captcha/Generate.json', {
          method: 'GET',
          headers: { 'Content-Type': 'application/json' },
        })
        .then(json => {
          this.data.scrf_key = json.data.scrf_key;
          $work(json.data.scrf_key);
          $id('captcha-image').setAttribute('src', json.data.captcha_image);
        })
      }
    }
  }

  function vote() {
    return {
      perent: {
        like: <?= $content->perent['like_post'] ?>
      },

      perentLike() {
        $json(`/api/ver1.0/QAResponse/Like.json?thread_id=${<?= $content->perent['id'] ?>}`)
        .then(json => {
          if (json.status == 'ok') {
            this.perent.like = json.data.vote;
          }
        })
      },

      perentDislike() {
        $json(`/api/ver1.0/QAResponse/Dislike.json?thread_id=${<?= $content->perent['id'] ?>}`)
        .then(json => {
          if (json.status == 'ok') {
            this.perent.like = json.data.vote;
          }
        })
      },

      childLike(id) {
        $json(`/api/ver1.0/QAResponse/Like.json?thread_id=${id}`)
        .then(json => {
          if (json.status == 'ok') {
            $id(`child-vote-${id}`).innerText = json.data.vote;
          }
        })
      },

      childDislike(id) {
        $json(`/api/ver1.0/QAResponse/Dislike.json?thread_id=${id}`)
        .then(json => {
          if (json.status == 'ok') {
            $id('child-vote-' + id).innerText = json.data.vote;
          }
        })
      }

    }
  }
</script>
</html>
