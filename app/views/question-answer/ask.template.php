<!DOCTYPE html>
<html lang="en">
<head>
  <?php include(APP_FULLPATH['component'] . 'meta/metatag.php') ?>

  <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
  <link rel="stylesheet" href="/lib/css/ui/v1.1/tailwind-colors.css">
  <script src="/lib/js/index.min.js"></script>
  <script src="/lib/js/bundles/message.js"></script>
  <script src="/lib/js/bundles/keepalive.min.js"></script>
  <script src="/lib/js/vendor/alpine/alpine.min.js"></script>
  <style>
    .container.width-view {
      display: grid;
      grid-template-columns: 1fr 300px;
    }
    main {
      overflow-x: auto;
    }

    .box-qoute {
      border: 1px solid #1D4ED8;
      padding: 8px;
      border-radius: 4px;
      margin-bottom: 20px;
    }

    .box-qoute h3 {
      color: #374151;
      margin: 0;
    }
    .box-qoute h1 {
      color: #111827;
      margin: 8px 0;
    }

    .box-qoute p {
      margin: 0;
      color: #4B5563;
    }

    .ask-form {
      max-width: 447px;
    }

    .ask-form h3 {
      color: #374151;
    }

    p.error-message {
      margin: 0;
      color: #e60a0a
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
          <li><a href="/QnA">QnA</a></li>
          <li>Pendapat Anda</li>
        </ul>
      </div>
      <div class="qoute-container">
<?php if ($content->isPerent): ?>
        <div class="box-qoute">
          <h3>Qute:</h3>
          <h1><?= $content->perent_title ?></h1>
          <p>komentar dari <a href="#"><?= $content->perent_author ?></a></p>
        </div>
<?php endif; ?>
      </div>
      <div class="ask-form">
        <h3>Pendapat Anda:</h3>
        <form x-data="askForm()" x-on:submit.prevent="submitData" x-init="captchaProvider()">

          <label class="v-group-input">
            Nama*
            <input name="name" type="text" class="textbox outline blue rounded small"
              x-model='data.name'>
            <p class="error-message" id="error-name"
              x-show="error.name.err == true"
              x-text="error.name.msg"></p>
          </label>

          <label class="v-group-input">
            Judul*
            <input name="title" type="text" class="textbox outline blue rounded small"
              x-model="data.title">
            <p class="error-message" id="error-title"
              x-show="error.title.err == true"
              x-text="error.title.msg"
              ></p>
          </label>

          <label class="v-group-input">
            Komentar
            <textarea name="content" class="textbox outline blue rounded small"
              x-model="data.content"></textarea>
            <p class="error-message" id="error-content"
              x-show="error.content.err == true"
              x-text="error.content.msg">oke</p>
          </label>

          <label class="v-group-input">
            Tag
            <input name="tag" type="text" class="textbox outline blue rounded small"
              x-model="data.tag">
            <p class="error-message" id="error-tag"
              x-show="error.tag.err == true"
              x-text="error.tag.msg">oke</p>
          </label>

          <section class="h-group-input wrap">
            <img src="<?= $content->captcha_image ?>" id="captcha-image" alt="captcha" width="200" height="70">
            <label class="v-group-input">
              Captcha
              <input type="text" class="textbox outline blue rounded small"
                x-model="data.scrf_secret">
              <p class="error-message" id="error-captcha"
                x-show="error.captcha.err == true"
                x-text="error.captcha.msg"></p>
            </label>
          </section>

          <div class="form-footer left">
            <button class="btn fill blue small rounded" type="submit">Kirim</button>
          </div>
        </form>
      </div>
    </main>
    <aside class="right-side"></aside>
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

  // app
  function askForm() {
    return {
      perent: {
        isPerent: false,
        id: 0
      },
      data: {
        scrf_key: '<?= $content->scrf_key ?>',
        scrf_secret: '',
        name: '',
        perent_id: '<?= $content->perent_id ?>',
        tag: '',
        title: '',
        content: ''
      },
      error: {
        name: {
          msg: 'ulangi kembali',
          err: false
        },
        title: {
          msg: 'ulangi kembali',
          err: false
        },
        content: {
          msg: 'ulangi kembali',
          err: false
        },
        tag: {
          msg: 'ulangi kembali',
          err: false
        },
        captcha: {
          msg: 'ulangi kembali',
          err: false
        },
      },
      onSubmit: false,
      submitData() {
        this.onSubmit = true;
        let success = false;
        let new_id = 0

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
          console.log(json);
          if (json.error == false) {
            console.log('Form sucessfully submitted!');
            success = true;
            new_id = json.data.new_id;
          } else {
            this.error.name.msg = json.error.name;
            this.error.name.err = true;

            this.error.title.msg = json.error.title;
            this.error.title.err = true;

            this.error.captcha.msg = json.error.scrf_secret;
            this.error.captcha.err = true;

            // create new captcha
            this.captchaProvider();
          }
        })
        .finally(() => {
          this.onSubmit = false;
          if (success) {
            if (this.data.perent_id == '') {
              window.location = `/question/${new_id}/qna`;
            } else {
              window.location = `/question/${this.data.perent_id}/qna`
            }

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
          $id('captcha-image').setAttribute('src', json.data.captcha_image);
        })
      }
    }
  }
</script>
</html>
