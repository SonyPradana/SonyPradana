<!DOCTYPE html>
<html lang="en">
<head>
  <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/meta/metatag.php') ?>

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

    @media screen and (max-width: 767px) {
      .container.width-view {
          grid-template-columns: 1fr
      }
    }


    .boxs-card.stories {
      display: grid;
      grid-template-columns: repeat(4, minmax(150px, 200px));
      gap: 12px;
    }

    .card.storie:first-child {
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .card.storie {
      height: 250px;
      background-color: #606060;

      border-radius: 8px;
      overflow: hidden;
      position: relative;
    }

    #add-new-storie {
      max-width: 100px;
    }

    .content .img img {
      object-fit: cover;
      width: 100%;
      height: 250px;
    }

    .viewer {
      position: absolute;
      top: 5%;
      left: 50%;
      transform:translate(-50%, -5%);
      color: #fff;
      font-size: 1rem;
    }
    .img:hover .caption {
      position: absolute;
      top: 75%;
      left: 50%;
      transform: translateX(-50%);
      color: #fff;
      font-size: 1.8rem;
      background-color: #2d2d2dc2;
      width: 100%;
      height: 25%;
      text-align: center;
      cursor: pointer;
      transition: .2s;
    }

    @media screen and (max-width: 767px) {
      .boxs-card.stories {
        grid-template-columns: repeat(3, minmax(150px, 200px));
      }
    }
    @media screen and (max-width: 447px) {
      .boxs-card.stories {
        grid-template-columns: repeat(2, minmax(150px, 200px));
      }
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
          <li><a href="/stories">Stories</a></li>
          <li>Stories</li>
        </ul>
      </div>
      <div class="title">
        <h1>Lihat Stori</h1>
        <p>Lihat Apa Yang Saat Ini Terjadi di Puskesmas Lerep</p>
      </div>
      <div id="boxs-stories" class="boxs-card stories">
        <div class="card storie">
          <div class="content">
            <button
              id="add-new-stories"
              class="btn fill blue small rounded"
              v-on:click="newStories()"
              >+</button>
          </div>
        </div>
        <div
          class="card storie"
          v-for="story in stories"
          :key="story.id"
          v-on:click='storiesView(story.id)'>
          <div class="content" v-on:click="preview(story.id)">
            <div class="img">
              <img
                v-bind:src="`/data/img/stories/thumbnail/${story.image_id}`"
                v-bind:alt="story.uploader">
                <div
                  class="viewer"
                  v-text="story.date_taken">
                </div>
                <div
                  class="caption"
                  v-text="story.caption">
                </div>
            </div>
          </div>
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
    <?php include($_SERVER['DOCUMENT_ROOT'] . '/resources/components/control/stories.modal.html') ?>
  </div>
</body>
<script src="/lib/js/index.end.js"></script>
<script>
  // sticky header
  window.onscroll = function(){
        stickyHeader('.container', '82px', '32px')
  };

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
  // dialog box (add stories)
  $id('add-new-stories').addEventListener('click', function(){
    $work()
  });


  // Vue
  const cardsStories = new Vue({
    el: "#boxs-stories",
    data: {
      stories: 0
    },
    methods: {
      loadStories: function() {
        $json(`/API/v1.0/Stories/Stories.json`)
          .then( json => {
            if (json.status == 'ok') {
              this.stories = json.data;
            }
          })
      },
      newStories: function() {
        $id('stories-modal').style.display = "block"
      },
      storiesView: function(storiesID) {
        window.location.href = `/stories/view/${storiesID}`;
      }
    },
    mounted() {
      this.loadStories();
    },
  });

</script>
</html>
