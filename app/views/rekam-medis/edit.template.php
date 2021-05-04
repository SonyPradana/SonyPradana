<!DOCTYPE html>
<html lang="en">
<head>
  <?php include(APP_FULLPATH['component'] . 'meta/metatag.php') ?>

  <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
  <script src="/lib/js/index.min.js"></script>
  <script src="/lib/js/bundles/keepalive.min.js"></script>
  <script src="/lib/js/controller/form-rm/index.min.js" defer></script>
  <style>
    .boxs {
      width: 100%; height: 100%;
      display: grid;
      grid-template-columns: 1fr 1fr;
    }
    .box.right { padding: 8px 16px }
    .input-information.auto-fill {
      display: flex;
      justify-content: space-between;
    }
    .input-information p,
    .input-information p a,
    p.dusun {
      margin: 0;
      color: #7f6cff;
    }

    form { max-width: 500px }
    form > input:not(:first-child),
    form > button,
    .grub-control.horizontal {
      margin-top: 12px
    }
    form > input { width: 100% }
    .grub-control.horizontal > .textbox{
      width: 100px;
    }

    /* mobile */
    @media screen and (max-width: 600px) {
      .boxs { grid-template-columns: 1fr }
      .box.right { padding: 5px }
    }
  </style>
</head>
<body>
  <header>
    <?php include(APP_FULLPATH['component'] . 'header/header.php'); ?>
  </header>

  <div class="container">
    <main>
      <div class="coit breadcrumb">
        <ul class="crumb">
          <li><a href="/">Home</a></li>
          <li><a href="/rekam-medis">Rekam Medis</a></li>
          <li>Edit Data Rekam Medis</li>
        </ul>
      </div>
      <div class="boxs">
        <div class="box left"></div>
        <div class="box right">
          <?php if( $portal['message']['type'] == 'danger') :?>
            <p style="color: red"><?= $portal['message']['content'] ?></p>
          <?php endif; ?>
          <h1>Edit data Rekam Medis</h1>
          <form action="" method="post">
            <input class="textbox outline black rounded small block" type="text" name="nomor_rm" id="input-nomor-rm" required placeholder="nomor rekam medis" value="<?= $content->nomor_rm ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*">
            <div class="input-information warning">
              <?php if( $content->status_double ) : ?>
                <p>nomor rekam medis sama :
                  <a href="/rekam-medis/search?nomor-rm-search=<?= $content->nomor_rm ?>"
                      target="_blank">lihat</a>
                </p>
              <?php endif; ?>
            </div>

            <input class="textbox outline black rounded small block" type="text" name="nama" id="input-nama" required placeholder="nama" value="<?= $content->nama ?>" maxlength="50" <?= $portal["DNT"] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
            <input class="textbox outline black rounded small block" type="date" name="tanggal_lahir" id="input-tgl-lahir" value="<?= $content->tanggal_lahir ?>">
            <input class="textbox outline black rounded small block" type="text" list="list-desa" name="alamat" id="input-alamat" placeholder="alamat tanpa rt/rw" value="<?= $content->alamat?>" <?= $portal["DNT"] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
            <div class="grub-control horizontal">
              <input class="textbox outline black rounded small" type="text" name="nomor_rt" id="input-nomor-rt" placeholder="nomor rt" max="2" value="<?= $content->nomor_rt ?>" inputmode="numeric" pattern="[0-9]*">
              <div class="gap-space"><!-- helper --></div>
              <input class="textbox outline black rounded small" type="text" name="nomor_rw" id="input-nomor-rw" placeholder="nomor rw" max="2" value="<?= $content->nomor_rw ?>" inputmode="numeric" pattern="[0-9]*">
              <div class="gap-space"><!-- helper --></div>
              <p class="dusun"></p>
            </div>
            <div class="grub-control horizontal">
              <input type="checkbox" name="tandai_sebagai_kk" id="input-mark-as-kk" tabindex="11" <?= $content->status_kk == true ? "checked" : ""?>>
              <label for="input-mark-as-kk">Tandai sebagai kk</label>
            </div>

            <input class="textbox outline black rounded small block" type="text" name="nama_kk" id="input-nama-kk" placeholder="nama kepala keluarga" value="<?= $content->nama_kk ?>" <?= $portal["DNT"] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
            <input class="textbox outline black rounded small block" type="text" name="nomor_rm_kk" id="input-nomor-rm-kk" placeholder="nomor rm kepla keluarga" value="<?= $content->nomor_rm_kk ?>" maxlength="6" inputmode="numeric" pattern="[0-9]*">
            <div class="input-information no-rm-kk"></div>
            <div class="input-information kk-sama"></div>

            <!-- biodata -->
            <div class="input-information auto-fill" style="margin-top: 8px;">
              <p style="margin: 0;">Data Pelengkap (Optional)</p>
              <a href="javascript:void(0)" id="toogle-panel">tampilkan</a>
            </div>
            <input class="textbox outline black rounded small block" type="text" name="nik" id="input-nik" placeholder="NIK" value="<?= $content->nik ?>" minlength="16" maxlength="16" inputmode="numeric" pattern="[0-9]*" >
            <input class="textbox outline black rounded small block" type="text" name="nomor_jaminan" id="input-nomor-jaminan" placeholder="Nomor BPJS" value="<?= $content->nomor_jaminan ?>" minlength="8" maxlength="13" inputmode="numeric" pattern="[0-9]*" >

            <div class="grub-control horizontal">
              <button class="btn rounded small blue outline" type="submit" name="submit">Edit Data RM</button>
              <div class="gap-space"><!-- helper --></div>
              <button class="btn rounded small red text" type="button" onclick="window.history.back()">Batal Perubahan</button>
            </div>

            <!-- helper -->
            <datalist id="list-desa">
              <option value="bandarjo">
              <option value="branjang">
              <option value="kalisidi">
              <option value="keji">
              <option value="lerep">
              <option value="nyatnyono">
            </datalist>
          </form>
        </div>
      </div>
    </main>
  </div>

    <div class="gotop" onclick="gTop()"></div>
    <footer>
        <?php include(APP_FULLPATH['component'] . 'footer/footer.html') ?>
    </footer>

    <!-- hiddeb -->
    <div id="modal">
        <?php include(APP_FULLPATH['component'] . 'control/modal.html') ?>
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
</body>
<script src="/lib/js/index.end.js"></script>
<script>
  // onload
  $load( () => {
    new form_rm({
      nomor_RmTerahir: document.createElement('div'),
      nomor_RmUpper: document.createElement('div'),
    }).init();
  })

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
