<!DOCTYPE html>
<html lang="en">
<head>
  <?php include(APP_FULLPATH['component'] . 'meta/metatag.php') ?>

  <link rel="stylesheet" href="/lib/css/ui/v1.1/style.css">
  <link rel="stylesheet" href="/lib/css/ui/v1/table.css">

  <script src="/lib/js/index.min.js"></script>
  <script src="/lib/js/bundles/keepalive.min.js"></script>
  <script src="/lib/js/controller/table-rm/index.js"></script>
  <style>
    .boxs {
      display: grid;
      grid-template-columns: minmax(250px, 300px) minmax(30%, auto);
      grid-column-gap: 10px;
    }
    .boxs .box.left { margin-right: 24px }
    .box.left form.search-box{
      position: -webkit-sticky;
      position: sticky;
      top: 80px;
    }
    .box.left form > input {
      width: 100%
    }
    .box.left form > input:not(:first-child),
    .box.left form > .grub-control.horizontal{
      margin-top: 10px
    }
    .box-right p.info{ display: none}
    .boxs .box.right .box-right {
      width: 100%;
      overflow-x: auto;
    }

    input[type=text] {
      min-width: 100px;
    }

    /* mobile */
    @media screen and (max-width: 600px) {
      .box.left form.search-box {
        position: unset;
        top: unset;
      }
      .boxs{
        display: block
      }
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
          <li>Cari Data</li>
        </ul>
      </div>
      <h1>Cari Data Rekam Medis</h1>
      <div class="boxs">
        <div class="box left">
          <form action="" method="get" class="search-box">
            <input class="textbox outline black rounded small block" type="text" name="main-search" id="input-main-search" placeholder="cari nama" value="<?= $content->nama ?>" <?= $portal["DNT"] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
            <div class="grub-control horizontal right">
              <button class="btn rounded light blue outline" type="submit" id="submit">Cari</button>
              <div class="gap-space"><!-- helper --></div>
              <button class="btn rounded light red outline" type="reset" id="reset-btn">Batal</button>
            </div>

            <div class="grub-control horizontal">
              <input type="checkbox" name="strict-search" id="input-strict-search" <?= $content->strict == true ? "checked" : ""?>>
              <label for="input-strict-search">Pencarian Mendalam</label>
            </div>

            <input class="textbox outline black rounded small block" type="text" name="nomor-rm-search" id="input-nomor-rm-seacrh" placeholder="cari nomor rm" value="<?= $content->nomor_rm ?>">
            <input class="textbox outline black rounded small block" type="date" name="tgl-search" id="input-tgl-search" data-date-format="DD MMMM YYYY" value="<?= (isset($_GET['tgl-search'])) ? $_GET['tgl-search'] : '' ?>">
            <input class="textbox outline black rounded small block" type="text" name="alamat-search" id="input-alamat-seacrh" placeholder="cari alamat" value="<?= $content->alamat ?>" <?= $portal["DNT"] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
            <div class="grub-control horizontal">
              <input class="textbox outline black rounded small block" type="text" name="no-rt-search" id="input-no-rt-search" placeholder="cari alamat rt" value="<?= $content->nomor_rt ?>">
              <div class="gap-space"><!-- helper --></div>
              <div class="gap-space"><!-- helper --></div>
              <input class="textbox outline black rounded small block" type="text" name="no-rw-search" id="input-no-rw-search" placeholder="cari alamat rw" value="<?= $content->nomor_rw ?>">
            </div>
            <input class="textbox outline black rounded small block" type="text" name="nama-kk-search" id="input-nama-kk-search" placeholder="cari nama kk" value="<?= $content->nama_kk ?>" <?= $portal["DNT"] ? 'autocomplete="off"' : 'autocomplete="on"' ?>>
            <input class="textbox outline black rounded small block" type="text" name="no-rm-kk-search" id="input-no-rm-kk" placeholder="cari nomor rm kk" value="<?= $content->nomor_rm_kk ?>">

            <!-- biodata -->
            <input class="textbox outline black rounded small block" type="text" name="nik-jaminan" id="input-nik-jaminan" placeholder="NIK / BPJS" value="<?= $content->nik_jaminan ?>" minlength="8" maxlength="16" inputmode="numeric" pattern="[0-9]*" >
          </form>
        </div>
        <div class="box right">
          <div class="box-right">
            <table class="data-rm">
              <thead>
                <tr>
                  <th>No.</th>
                  <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('nomor_rm')">No RM</a></th>
                  <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('nama')">Nama</a></th>
                  <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('tanggal_lahir')">Tanggal Lahir</a></th>
                  <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('alamat')">Alamat</a></th>
                  <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('nomor_rw')">RT / RW</a></th>
                  <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('nama_kk')">Nama KK</a></th>
                  <th scope="col"><a class="sort-by" href="javascript:void(0)" onclick="sort_table('nomor_rm_kk')">No. Rm KK</a></th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
            <div class="box-pagination">
              <div class="pagination">
                  <!-- pagination -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <div class="gotop" onclick="gTop()"></div>
  <footer>
    <?php include(APP_FULLPATH['component'] . 'footer/footer.html') ?>
  </footer>

  <!-- hidden -->
  <div id="modal">
    <?php include(APP_FULLPATH['component'] . 'control/modal.html') ?>
  </div>
  <script src="/lib/js/index.end.js"></script>
  <script type="text/javascript">
    // clear url
    let myform = document.querySelector('form.search-box');
    myform.addEventListener('submit', () => {
      elements = myform.elements
      for (let i = 0, element; element = elements[i++];) {
        if ((element.type === "text" || element.type === "date") && element.value === "")
          element.disabled = true
      }
    })

    //clear button
    var btnBack = document.querySelector('#reset-btn');
    btnBack.onclick = function () {
      document.querySelector('#input-main-search').setAttribute('value', '');
      document.querySelector('#input-nomor-rm-seacrh').setAttribute('value', '');
      document.querySelector('#input-tgl-search').setAttribute('value', '');
      document.querySelector('#input-alamat-seacrh').setAttribute('value', '');
      document.querySelector('#input-no-rt-search').setAttribute('value', '');
      document.querySelector('#input-no-rw-search').setAttribute('value', '');
      document.querySelector('#input-nama-kk-search').setAttribute('value', '');
      document.querySelector('#input-no-rm-kk').setAttribute('value', '');
      document.querySelector('#input-nik-jaminan').setAttribute('value', '');
    };

    window.addEventListener('load', () => {
      // get data from DOM or URL
      const queryString = window.location.search
      let searchParams = new URLSearchParams(queryString)

      _search_name = searchParams.get('main-search')
      _search_name_kk = searchParams.get('nama-kk-search')

      let query = '&' + searchParams.toString()
      if( query != '&'){
        getData(_sort, _order, _cure_page, query)
        _search_query = query
      } else {
        const tr =  document.createElement('tr');
        const td =  document.createElement('td');
        const desc = document.createElement('h3');
        desc.innerText = 'Mencari sesuatu?';
        td.setAttribute('colspan', 9);
        td.appendChild(desc)
        tr.appendChild(td)

        $query('.data-rm tbody').appendChild(tr)
      }
    })

    // sticky header
    window.onscroll = function() {
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
</body>
</html>
