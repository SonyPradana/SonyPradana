// All Dom form
let DOM_nomor_rm = document.getElementById('input-nomor-rm');
let DOM_nama = document.getElementById('input-nama');
let DOM_tanggal = document.getElementById('input-tgl-lahir');
let DOM_alamat = document.getElementById('input-alamat');
let DOM_rt = document.getElementById('input-nomor-rt');
let DOM_rw = document.getElementById('input-nomor-rw');
let DOM_set_KK = document.getElementById('input-mark-as-kk');
let DOM_nama_KK = document.getElementById('input-nama-kk');
let DOM_nomor_KK = document.getElementById('input-nomor-rm-kk');
// optional DOM
let DOM_nomor_rm_akhir = document.getElementById('tambah-nomor-rm');

// function
async function fetchJSON(url){
    const response = await fetch(url, {
        header: {        
          'Content-Type': 'application/json'
        }
    });
    return response.json();
}

function cekRM(){
    const nr = DOM_nomor_rm.value;
    if(nr == '') return;
    fetchJSON('/api/ver1.0/RekamMedis/valid-nomor-rm.json?nr=' + nr)
        .then( data => {
            let res = document.querySelector('.input-information.warning');
            let para = document.createElement("p");
            let alink = document.createElement('a');
            res.textContent = '';
            para.innerHTML = 'nomor rekam medis sama : ';
            alink.href = '/rekam-medis/search?nomor-rm-search=' + nr;
            alink.innerHTML = 'lihat'
            alink.target = '_blank';
            para.appendChild(alink);
            const nrm = data['found'];
            if( nrm > 0){
                res.appendChild(para);
            }
        })
}

function cekRM_KK(){
    let nm = DOM_nama.value;
    let n = DOM_nama_KK.value;
    let a = DOM_alamat.value;
    let r = DOM_rt.value;
    let w = DOM_rw.value;

    fetchJSON("/api/ver1.0/RekamMedis/search-nomor-rm-kk.json?n="+ n + "&a=" + a + "&r=" + r + "&w=" + w)
        .then( data => {
            let info_rm_kk = document.querySelector('.input-information.no-rm-kk');
            const nrk = data['nomor_rm_kk'];
            let para = document.createElement('p');
            let paralink = document.createElement('a');
            info_rm_kk.textContent = '';
            if( nrk != undefined && nrk != ''){
                para.innerHTML = 'nomor rm kk : ';
                paralink.href = 'javascript:void(0)';
                paralink.id = 'tambah-nomor-rm-kk';
                paralink.tabIndex = 12;
                paralink.innerHTML = nrk;
                para.appendChild(paralink);
                info_rm_kk.appendChild(para);
                paralink.addEventListener('click', () => {
                    DOM_nomor_KK.value = nrk;
                })
            }

            // nama kk yang sama
            let info_kk = document.querySelector('.input-information.kk-sama');
            let para2 = document.createElement('p');
            let alink2 = document.createElement('a');
            info_kk.textContent = '';
            if( nrk != '' && n == nm && nrk != undefined){
                para2.innerHTML = 'nama kk indentik : '
                alink2.href = '/rekam-medis/search?strict-search=on&alamat-search='+ a +'&no-rt-search=' + r + '&no-rw-search=' + w + '&nama-kk-search=' + n;
                alink2.innerHTML = 'lihat'
                alink2.target = '_blank';
                para2.appendChild(alink2);
                info_kk.appendChild(para2);
            }
        })
}

function cekDesa(){
    $query('p.dusun').innerText = '--'
    let desa = DOM_alamat.value
    let rw = DOM_rw.value
    if( desa != '' && rw != '' ){
        getDesa(desa, rw)
    }
}

let cash_desa = null;
$load(function(){
    fetchJSON('/api/ver1.0/Wilayah-Kab-Semarang/Data-Desa.json?kecamatan=ungaran-barat')
    .then( data => {
        cash_desa = data['data']
    })
});
function getDesa(desa, rw) {
    if( cash_desa == null ) {
        fetchJSON('/api/ver1.0/Wilayah-Kab-Semarang/Data-Desa.json?kecamatan=ungaran-barat')
            .then( data => {
                let dusun = data['data'][desa][rw];
                dusun = dusun == undefined || dusun == '' ? '--' : dusun;
                $query('p.dusun').innerText = dusun
            })
    }else if( desa in cash_desa ){
        let dusun = cash_desa[desa][rw];
        dusun = dusun == undefined || dusun == '' ? '--' : dusun;
        $query('p.dusun').innerText = dusun
    }
}

// event handler
if( DOM_nomor_rm_akhir != null ){
    DOM_nomor_rm_akhir.addEventListener('click', () => {
        let input_no_Rm = document.querySelector("#input-nomor-rm");
        input_no_Rm.value = last_nomor_rm + 1;
        DOM_nomor_rm.focus();
        cekRM();
    })
}
DOM_set_KK.onclick = () => {
    if( DOM_set_KK.checked == true){
        DOM_nama_KK.value = DOM_nama.value;
        DOM_nomor_KK.value = DOM_nomor_rm.value;
    }else{
        DOM_nama_KK.value = "";
        DOM_nomor_KK.value = "";
    }
    cekRM_KK();
}

DOM_nomor_rm.addEventListener('input', () => {
    cekRM();
})

DOM_nama_KK.addEventListener('input', () => {
    cekRM_KK();
})

DOM_alamat.addEventListener('input', () => {
    cekDesa()
})

DOM_rw.addEventListener('input', () => {
    cekDesa()
})
