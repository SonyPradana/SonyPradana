<!DOCTYPE html>
<html lang="en">
<head>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/meta/metatag.php') ?>

  <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
  <link rel="stylesheet" href="/lib/css/ui/v1.1/widgets.css">
  <link rel="stylesheet" href="/lib/css/ui/v1.1/cards.css">
  <script src="/lib/js/index.min.js"></script>
  <script src="/lib/js/bundles/message.js"></script>
  <script src="/lib/js/bundles/keepalive.min.js"></script>
  <script src="/lib/js/vendor/vue/vue.min.js"></script>
  <style>
    .boxs-app {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
    }
    .group-input {
      display: flex;
      gap: 12px;
    }
    section {
      flex-basis: 100%;
      display: flex;
      flex-direction: column;
      margin-bottom: 12px;
      gap: 4px;
    }
    section textarea {
      resize: vertical;
    }
    section p {
      margin: 0;
      color: red;
    }

    /* mobile */
    @media screen and (max-width: 470px) {            
      .boxs-app {
        grid-template-columns: 1fr
      }
      .group-input {
        display: flex;
        flex-direction: column;
        gap: 0;
      }
    }
  </style>
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
          <li>Trivia</li>
          <li><?= $portal['meta']['title'] ?></li>
        </ul>
      </div>

      <div class="boxs-app">
        <div class="box-left">
          <h1>Buat Pertanyaan Baru</h1>
          <form action="" method="post">
            <div class="group-input">
              <section>
                <label for="input-author">Author</label>
                <input class="textbox outline black rounded small" type="text" name="author" id="input-author" required placeholder="Author">
                <?php if (isset($portal['error']['author'])): ?>                
                  <p  class="input-error"><?= $portal['error']['author'] ?? ''?></p>
                <?php endif; ?>
              </section>
              <section>
                <label for="input-level">Level</label>
                <!-- <input class="textbox outline black rounded small" type="number" name="level" id="input-level" required placeholder="Level" value="1"> -->
                <select class="textbox outline black rounded small" name="level" id="input-level">
                  <option value="1" selected>level 1 - umum</option>
                  <option value="2">level 2 - umum</option>
                  <option value="3">level 3 - profesi</option>
                </select>
                <?php if (isset($portal['error']['level'])): ?>                
                  <p  class="input-error"><?= $portal['error']['level'] ?? ''?></p>
                <?php endif; ?>
              </section>
            </div>
            <section>
              <label for="input-quest">Quest</label>
              <input class="textbox outline black rounded small" type="text" name="quest" id="input-quest" required placeholder="Pertanyaan">
              <?php if (isset($portal['error']['quest'])): ?>                
                <p  class="input-error"><?= $portal['error']['quest'] ?? ''?></p>
              <?php endif; ?>
            </section>
            <section>
              <input type="hidden" name="quest_img" id="input-image" value="">
            </section>
            <section>
              <label for="input-answer-1">Jawaban benar</label>
              <input class="textbox outline black rounded small" type="text" name="answer_1" id="input-answer-1" required placeholder="jawaban benar">
            </section>
            <section>
              <label for="input-answer-2">Jawaban kedua</label>
              <input class="textbox outline black rounded small" type="text" name="answer_2" id="input-answer-2" required placeholder="Opsi jawaban">
            </section>
            <section>
              <label for="input-answer-3">Jawaban ketiga</label>
              <input class="textbox outline black rounded small" type="text" name="answer_3" id="input-answer-3" required placeholder="Opsi jawaban">
            </section>
            <section>
              <label for="input-answer-4">Jawaban keempat</label>
              <input class="textbox outline black rounded small" type="text" name="answer_4" id="input-answer-4" required placeholder="Opsi jawaban">
            </section>
            <section>
              <label for="input-explanation">Penjelasan</label>
              <textarea name="explanation" id="input-explanation" cols="30" rows="10" class="textbox outline blue rounded small" placeholder="optional"></textarea>
            </section>


            <button type="submit" name="sumbit" class="btn rounded small blue fill">Send</button>
          </form>
        </div>
        <div class="box-right">

        </div>
      </div>
    </main>
  </div>
  <div class="gotop" onclick="gTop()"></div>
  <footer>
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/lib/components/footer/footer.html') ?>
  </footer>
  <!-- hidden -->
  <div id="modal">
      <?php include(BASEURL . '/lib/components/control/modal.html') ?>
  </div>
  <?php if ($portal['message']['show']): ?>
      <div class="snackbar <?= $portal['message']['type'] ?>">
          <div class="icon">
              <!-- css image -->
          </div>
          <div class="message">
              <?= $portal['message']['content'] ?>
          </div>
      </div>
  <?php endif; ?> 
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
</script>
</html>
