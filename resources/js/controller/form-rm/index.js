// terser -c -m -- resources/js/controller/form-rm/index.js > public/lib/js/controller/form-rm/index.min.js
const default_form_element = {
  nomor_rm: $id('input-nomor-rm'),
  nama: $id('input-nama'),
  tanggal: $id('input-tgl-lahir'),
  alamat: $id('input-alamat'),
  rt: $id('input-nomor-rt'),
  rw: $id('input-nomor-rw'),
  mark_kk: $id('input-mark-as-kk'),
  nama_kk: $id('input-nama-kk'),
  nomor_kk: $id('input-nomor-rm-kk'),
  // other
  nomor_RmTerahir: $id('tambah-nomor-rm'),
  nomor_RmUpper: $id('tambah-nomor-rm-upper'),
  info_nomorRm: $query('.input-information.warning'),
  info_nomorRmKk: $query('.input-information.no-rm-kk'),
  info_Kk: $query('.input-information.kk-sama'),
  info_dusun: $query('p.dusun'),
};

const form_rm = function (el = default_form_element) {
  const data = default_form_element;
  el = Object.assign(data, el);
  
  // private function
  let dataDesa = undefined;
  let nomor_RmTerahir = {
    last_nomor_rm: 0,
    last_id: 0
  };

  function cekRm() {
    const nomor_rm = el.nomor_rm.value;

    if (nomor_rm.length != 0 && nomor_rm.length < 7) {
      $json(`/api/ver1.0/RekamMedis/valid-nomor-rm.json?nr=${nomor_rm}`)
        .then(json => {
          el.info_nomorRm.innerHTML = '';

          if (json.status == 'ok' && json.found > 0) {
            // creat virtual DOM
            el.info_nomorRm.innerHTML =
              `<p>
                  nomor rekam medis sama :
                  <a href="/rekam-medis/search?nomor-rm-search=${nomor_rm}" target="_blank" tabindex="12">lihat</a>
                </p>`;
          }
        })
    }
  }

  function cekRmKK() {
    const nama = el.nama.value;
    const namaKk = el.nama_kk.value;
    const alamat = el.alamat.value;
    const nomor_rt = el.rt.value;
    const nomor_rw = el.rw.value;

    $json(`/api/ver1.0/RekamMedis/search-nomor-rm-kk.json?n=${namaKk}&a=${alamat}&r=${nomor_rt}&w=${nomor_rw}`)
      .then(json => {
        el.info_nomorRmKk.innerHTML = '';
        el.info_Kk.innerHTML = ``;

        if (json.status == 'ok' &&
          json.nomor_rm_kk != undefined &&
          json.nomor_rm_kk != '') {

          // nomor rm kk ditemukan
          el.info_nomorRmKk.innerHTML =
            `<p>
                nomor rm kk :
                <a href="javascript:void(0)" id="tambah-nomor-rm-kk" tabindex="12">
                  ${json.nomor_rm_kk}
                </a>
              </p>`;
          $id('tambah-nomor-rm-kk').addEventListener('click', () => {
            el.nomor_rm_kk.value = json.nomor_rm_kk;
          });

          // kk identik
          if (nama == namaKk) {
            el.info_Kk.innerHTML =
              `<p>
                  nama kk indentik :
                  <a href="/rekam-medis/search?strict-search=on&alamat-search=${alamat}&no-rt-search=${nomor_rt}&no-rw-search=${nomor_rw}&nama-kk-search=${namaKk}" target="_blank">
                    lihat
                  </a>
                </p>`;
          }

        }
      })
  }

  function getDataDesa(nama_desa, nomor_rw) {
    // search data
    let nama_dusun = '--';
    if (nama_desa in dataDesa) {
      nama_dusun = dataDesa[nama_desa][nomor_rw] ?? '--';
    }
    el.info_dusun.innerText = nama_dusun;
  }

  function getData() {
    $json('/api/ver1.1/Wilayah-Kab-Semarang/Data-Desa.json?kecamatan=ungaran-barat')
      .then(json => {
        // fetch data
        if (json.status == 'ok') {
          dataDesa = json.data;
          getDataDesa(el.alamat.value, el.rw.value);
        }
      });

    $json('/api/ver1.1/RekamMedis/nomor-rm-terahir.json?limit=14000')
      .then(json => {
        if (json.status == 'ok') {
          nomor_RmTerahir = json.data;
          el.nomor_RmTerahir.innerText = nomor_RmTerahir.last_nomor_rm;
        }
      });
  }

  // export private function
  return {
    refresh: () => {
      getData();
      return true;
    },
    init: () => {
      // setup
      getData();
      // cek nomor rm
      el.nomor_rm.addEventListener('input', () => {
        cekRm();
      });

      // cek nomor rm kk identik
      el.nama_kk.addEventListener('input', () => {
        cekRmKK();
      });

      // cari nama dusun
      el.alamat.addEventListener('input', () => {
        getDataDesa(el.alamat.value, el.rw.value);
      });

      el.rw.addEventListener('input', () => {
        getDataDesa(el.alamat.value, el.rw.value);
      });

      // nomor rm terahir
      el.nomor_RmTerahir.addEventListener('click', () => {
        el.nomor_rm.value = nomor_RmTerahir.last_nomor_rm + 1;
        el.nomor_rm.focus();
        cekRm();
      });
      el.nomor_RmUpper.addEventListener('click', () => {
        el.nomor_rm.value = nomor_RmTerahir.upper_nomor_rm + 1;
        el.nomor_rm.focus();
        cekRm();
      });

      // mark as kk
      el.mark_kk.addEventListener('click', (e) => {
        if (e.target.checked == true) {
          el.nama_kk.value = el.nama.value;
          el.nomor_kk.value = el.nomor_rm.value;
        } else {
          el.nama_kk.value = '';
          el.nomor_kk.value = '';
        }
        cekRmKK();
      });
    },
    distroy: () => {
      // remove event handler
    }
  }
};
