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

    .gap-12px {
      gap: 12px;
    }

    .w-min-100px {
      width: 100px;
    }

    .text-bold {
      font-weight: 700;
      text-transform: capitalize;
    }

    .box-grup-control > div {
      margin-bottom: 12px;
      font-size: 1rem;
      line-height: 1.5rem;
    }

    .box-border {
      border-radius: 4px;
      border: 1px solid #F3F4F6;
      border-top: 3px solid #2563EB;
      padding: 8px;
    }

    .app-container {
      color: #111827;

      display: grid;
      grid-template-columns: minmax(200px, 360px) 1fr;
      grid-template-rows: auto 1fr;
      row-gap: 12px;
      column-gap: 12px;
    }

    .container-header {
      grid-column: 1 / 3;
      grid-row: 1;

      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .container-left {
      display: grid;
      grid-template-rows: min-content min-content;
      row-gap: 12px;
      column-gap: 12px;
    }

    .box-header {
      padding: 8px;
      display: flex;
      gap: 12px;
      justify-content: right;
      align-items: center;
    }

    .box-table {
      overflow-x: auto;
    }

    .box-table table {
      width: 100%;
      font-size: .95rem;
    }

    @media screen and (max-width: 640px) {
      .container-header {
        flex-flow: column;
        align-items: flex-start;
      }
    }

    @media screen and (max-width: 1024px) {
      .container.width-view {
        grid-template-columns: 1fr
      }

      .app-container {
        grid-template-columns: 1fr;
        grid-template-rows: min-content min-content min-content;
      }

      .container-left {
        grid-column: 1 / 3;
        grid-row: 2;
      }
      .container-right {
        grid-column: 1 / 3;
        grid-row: 3;
      }
    }
  </style>
</head>
<body>
  <header>
    <?php include(BASEURL . '/resources/components/header/header.php'); ?>
  </header>

  <div class="container width-view">
    <main id="app" class="app-container">
      <div class="container-header">
        <h1>Pendaftaran Puskesmas Lerep</h1>
        <div class="coit breadcrumb">
          <ul class="crumb">
            <li><a href="/">Home</a></li>
            <li><a href="/rekam-medis">Rekam Medis</a></li>
            <li>Pendaftaran</li>
          </ul>
        </div>
      </div>

      <div class="container-left">
        <div class="box-border box-search">
          <form>
            <label class="v-group-input">
              Nomor RM
              <input
                v-model="nomor_rm"
                v-on:keyup.enter="getInfo"
                @keypress="isNumber($event)"
                type="search"
                class="textbox outline blue rounded small"
                placeholder="Nomor Rm"
              >
            </label>
            <label class="v-group-input">
              Nomor BPJS / KTP
              <input
                v-model="nomor_jaminan"
                v-on:keyup.enter="getInfo"
                @keypress="isNumber($event)"
                type="search"
                class="textbox outline blue rounded small"
                placeholder="NIK atau BPJS"
              >
            </label>
            <div class="grub-control horizontal right gap-12px">
              <button v-on:click="getInfo" type="button" class="btn rounded light blue outline">cari</button>
              <button v-on:click="reset" type="button" class="btn rounded light red outline">batal</button>
            </div>
          </form>
        </div>
        <div class="box-border box-registration">
          <div class="box-grup-control">
            <div class="grub-control horizontal gap-12px">
              <div class="w-min-100px">Nomor Rm:</div>
              <div class="text-bold">{{ info_rm.nomor_rm ?? '--' }}</div>
            </div>
            <div class="grub-control horizontal gap-12px">
              <div class="w-min-100px">Nama:</div>
              <div class="text-bold">{{ info_rm.nama ?? '--' }}</div>
            </div>
            <div class="grub-control horizontal gap-12px">
              <div class="w-min-100px">Tanggal Lahir:</div>
              <div class="text-bold">{{ info_rm.tanggal_lahir ?? '--' }}</div>
            </div>
            <div class="grub-control horizontal gap-12px">
              <div class="w-min-100px">Alamat:</div>
              <div class="text-bold">{{ info_rm.alamat ?? '--' }}, {{ info_rm.nomor_rt ?? 0 }}/{{ info_rm.nomor_rw ?? 0 }}</div>
            </div>
            <div class="grub-control horizontal gap-12px">
              <div class="w-min-100px">Nama KK:</div>
              <div class="text-bold">{{ info_rm.nama_kk ?? '--' }}</div>
            </div>
            <div class="grub-control horizontal gap-12px">
              <div class="w-min-100px">No KTP:</div>
              <div class="text-bold">{{ info_rm.nik ?? '--' }}</div>
            </div>
            <div class="grub-control horizontal gap-12px">
              <div class="w-min-100px">No BPJS:</div>
              <div class="text-bold">{{ info_rm.nomor_jaminan ?? '--' }}</div>
            </div>
            <div>
              <label class="v-group-input">
                Tanggal Input
                <input v-model="tanggal_kunjungan" type="date" class="textbox outline blue rounded small">
              </label>
              <label class="v-group-input">
                Status Kunjungan
                <select v-model="status_kujungan" class="textbox outline blue rounded light">
                  <option value="baru">Kunjungan Baru</option>
                  <option value="lama">Kunjungan Lama</option>
                </select>
              </label>
              <label class="v-group-input">
                Poli
                <select v-model="poli_tujuan" class="textbox outline blue rounded light">
                  <option value="umum">umum</option>
                  <option value="lanisa">lansia</option>
                  <option value="gigi">gigi</option>
                  <option value="kia-anak">kia-anak</option>
                  <option value="kia-ibu">kia-ibu</option>
                </select>
              </label>
              <label class="v-group-input">
                Jenis Jaminan
                <select v-model="jenis_peserta" class="textbox outline blue rounded light">
                  <option value="0">umum</option>
                  <option value="1">Bpjs</option>
                  <option value="2">Bpjs Luar</option>
                </select>
              </label>
              <div class="grub-control horizontal right gap-12px">
                <button v-if="valid_rm" v-on:click="daftar" type="button" class="btn rounded light blue outline">daftar</button>
                <button v-else v-on:click="getInfo" type="button" class="btn rounded light blue outline">baru</button>

                <button v-on:click="reset" type="button" class="btn rounded light red outline">batal</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container-right">
        <div class="box-border box-visitor">
          <div class="box-header">
            <label>
              Tanggal Kunjungan
              <input type="date" name="tanggal-visitor" v-model="today" class="textbox outline blue rounded small">
            </label>
            <button v-on:click="loadKunjungan" class="btn rounded light blue outline">Refresh</button>
          </div>
          <div class="box-table">
            <table>
              <thead>
                <tr>
                  <th>No</th>
                  <th>Tanggal</th>
                  <th>No Rm</th>
                  <th>Nama</th>
                  <th>Umur</th>
                  <th>Poli</th>
                  <th>Status</th>
                  <th>BPJS</th>
                  <th>Diagnosa</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, index) in kunjungan">
                  <td>{{ index + 1 }}</td>
                  <td>{{ item.tanggal_dibuat }}</td>
                  <td>
                    <a v-bind:href="`/rekam-medis/search?strict-search=on&nomor-rm-search=${item.nomor_rm}`">{{ item.nomor_rm }}</a>
                  </td>
                  <td>{{ item.nama }}</td>
                  <td>{{ item.umur }}</td>
                  <td>{{ item.poli }}</td>
                  <td>{{ item.status }}</td>
                  <td v-if="item.jenis_peserta == 0">umum</td>
                  <td v-if="item.jenis_peserta == 1">bpjs</td>
                  <td v-if="item.jenis_peserta == 2">luar</td>
                  <td>{{ item.diagnosa }}</td>
                  <td>
                    <button v-on:click="hapus(item.id)" type="button" class="btn rounded light red outline">hapus</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
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
  const app = new Vue({
    el: '#app',
    data() {
      return {
        today: new Date().toJSON().slice(0,10),
        nomor_rm: '',
        nomor_jaminan: '',
        info_rm: [],
        valid_rm: false,
        kunjungan: [],
        // registration
        status_kujungan: 'baru',
        tanggal_kunjungan: new Date().toJSON().slice(0,10),
        poli_tujuan: 'umum',
        jenis_peserta: 0,
      }
    },
    mounted() {
      this.loadKunjungan()
    },
    methods: {
      loadKunjungan() {
        $json(`/api/ver2/RegistrationMR/lihatkunjungan.json?tanggal_kunjungan=${this.today}`)
          .then(json => {
            if (json.status == 'oke') {
              this.kunjungan = json.data
            }
          })
      },
      getInfo() {
        this.valid_rm = false;
        $json(`/api/ver1.0/RekamMedis/search.json?strict-search=on&nomor-rm-search=${this.nomor_rm}&nik-jaminan=${this.nomor_jaminan}`)
          .then(json => {
            if (json.status == 'ok') {
              if (json.data[0] != null) {
                this.valid_rm = true;
                this.info_rm = json.data[0]
              }
            } else {
              this.info_rm = []
              this.valid_rm = false
            }
          })
      },
      reset() {
        this.nomor_rm = ''
        this.nomor_jaminan = ''
        this.info_rm = []
        this.valid_rm = false

        this.poli_tujuan = 'umum'
        this.jenis_peserta = 0
      },
      daftar() {
        if (this.valid_rm) {
          $json(`/api/ver2/RegistrationMR/tambahKunjungan.json`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            mode: 'cors',
            cache: 'no-cache',
            credentials: 'same-origin',
            redirect: 'follow',
            referrerPolicy: 'no-referrer',
            body: JSON.stringify({
              tanggal_kunjungan:  this.tanggal_kunjungan,
              rm_id: this.info_rm.id,
              poli_tujuan: this.poli_tujuan,
              jenis_peserta: this.jenis_peserta,
            })
          }).then(json => {
            if (json.status == 'ok') {
              this.reset()
              // tambah kunjungan tanpa refresh
              if (this.tanggal_kunjungan == this.today) {
                json.data.status = 'pendaftaran'
                this.kunjungan.unshift(json.data)
              }
            } else if (json.status == 'Bad Request') {
              alert("tidak dapat menyimpan data")
            }
          })
        }
      },
      hapus(id) {
        if (confirm("Hapus Kunjungan!")) {
          const index = this.kunjungan.findIndex(e => e.id == id)
          this.kunjungan.splice(index, 1)

          $json(`/api/ver2/RegistrationMR/hapuskunjungan.json`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            mode: 'cors',
            cache: 'no-cache',
            credentials: 'same-origin',
            referrerPolicy: 'no-referrer',
            body: JSON.stringify({
              kunjungan_id:  id,
            })
          })
            .then(json => {
              if (json.status == 'Accepted') {
                if (json.data[0] != null) {
                  this.valid_rm = true;
                  this.info_rm = json.data[0]
                }
              }
            })
        }
      },
      isNumber: function(evt) {
        evt = evt ? evt : window.event;
        const charCode = evt.which ? evt.which : evt.keyCode;
        if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
          evt.preventDefault();;
        } else {
          return true;
        }
      }

    },
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
