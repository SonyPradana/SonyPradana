let _sort = 'nomor_rm'
let _order = 'ASC'
let _cure_page = 1
let _maks_page = 1

let _search_query = ''
let _search_name = ''
let _search_name_kk = ''

let ucwords = str => (str + '').replace( /^([a-z])|\s+([a-z])/g, $1 => $1.toUpperCase() )
let addTag = (str, find) => str.replace(find, `<span style="color:blue">${find}</span>`)

async function fetchJSON(url){
  const response = await fetch(url, {
      header: {        
        'Content-Type': 'application/json'
      }
  });
  return response.json();
}

function getData(sort, order, cure_page, search_query){
    fetchJSON(`/lib/ajax/json/private/med-rec/table-search/?sort=${sort}&order=${order}&page=${cure_page}${search_query}`)
    .then(json => {
        if( json['status'] == 'ok'){
            _maks_page = json['maks_page']
            _cure_page = json['cure_page']
            render_table(json['data'])
            
            if( json['data'].length == 0){
                let dom_target = document.querySelector('.box-right')
                let dom_before = document.querySelector('.box-pagination')
                let dom_not_found = document.createElement('p')
                dom_not_found.innerText = 'data tidak ditemukan'
                // dom_target.appendChild(dom_not_fouund)
                dom_target.insertBefore(dom_not_found, dom_before)
            }

            render_pagination()
        }
    })
}

function sort_table(sort){
    if(_order == 'ASC' && _sort == sort){
        _order = 'DESC'
    }else{
        _order = 'ASC'
    }
    _sort = sort
    _cure_page = 1
    getData(_sort, _order, _cure_page, _search_query)
}

function render_table(data){
    let i = (_cure_page * 10) - 9
    let dom_target = document.querySelector('table.data-rm tbody')
    dom_target.innerHTML = ''

    data.forEach(element => {
      // membuat elemnt kosong
      let perent_row = document.createElement('tr')  
      let th_nomor = document.createElement('th')
      let th_rm = document.createElement('th')
      let th_nama = document.createElement('th')
      let th_tgl = document.createElement('th')
      let th_alamat = document.createElement('th')
      let th_rtrw = document.createElement('th')
      let th_kk = document.createElement('th')
      let th_rm_kk = document.createElement('th')
      let th_action = document.createElement('th')

      // isi element
      th_nomor.innerText = i
      th_rm.innerText = element['nomor_rm']
      th_nama.innerHTML = ucwords( addTag(element['nama'], _search_name) )
      th_tgl.innerText = element['tanggal_lahir']
      th_alamat.innerText = ucwords(element['alamat'])
      th_rtrw.innerText = element['nomor_rt'] + ' / ' + element['nomor_rw']
      th_kk.innerHTML = ucwords( addTag(element['nama_kk'], _search_name_kk))
      th_rm_kk.innerText = element['nomor_rm_kk']
      th_action.innerHTML = `<a class="link" href="/p/med-rec/edit-rm/index.php?document_id=${element['id']}">edit</a>`

      // append ke row
      perent_row.appendChild(th_nomor)
      perent_row.appendChild(th_rm)
      perent_row.appendChild(th_nama)
      perent_row.appendChild(th_tgl)
      perent_row.appendChild(th_alamat)
      perent_row.appendChild(th_rtrw)
      perent_row.appendChild(th_kk)
      perent_row.appendChild(th_rm_kk)
      perent_row.appendChild(th_action)

      // append row ke table
      dom_target.appendChild(perent_row)
      i++
    })
}
    
function render_pagination(){
    let dom_paginaiton = document.querySelector('.pagination')
    dom_paginaiton.innerHTML = '<!-- pagination -->'
        
    let creat_p = (page, text) => {
        let p = document.createElement('a')
        if(page == _cure_page){
            p.className = 'active'
        }
        p.href = 'javascript:void(0)'
        p.addEventListener('click', () => {
            _cure_page = page
            getData(_sort, _order, _cure_page, _search_query)
        })
        p.innerHTML = text
        dom_paginaiton.appendChild(p)
    }
    let creat_s = () => {
        let s = document.createElement('a')
        s.href = 'javascript:void(0)'
        s.className = 'sperator'
        s.innerText = '...'
        dom_paginaiton.appendChild(s)
    }

    if( _cure_page > 1){
        creat_p(_cure_page - 1, '&laquo;')
    }
    if( _maks_page > 5){
        // satu didepan
        creat_p(1, 1)
        // tiga ditengah
        if( _cure_page > 2 && _cure_page < (_maks_page -1) ){
            creat_s()
            // prev page, curret page, next page
            creat_p(_cure_page - 1, _cure_page - 1)
            creat_p(_cure_page, _cure_page)
            creat_p(_cure_page + 1, _cure_page + 1)
            creat_s()
        }else if( _cure_page < 4 ){
            // page 2 & 3
            creat_p(2, 2)
            creat_p(3, 3)
            creat_s()
        }else if( _cure_page > ( _maks_page - 2 ) ){
            // 2 dari belakang
            creat_s()
            creat_p(_maks_page - 2, _maks_page - 2)
            creat_p(_maks_page - 1, _maks_page - 1)
        }
        // satu dibelakang
        creat_p(_maks_page, _maks_page)
    }else if( _maks_page < 6 ){
        // page 1-5
        for (let i = 1; i <= _maks_page; i++) {
            creat_p(i, i)
        }
    }
    if( _cure_page < _maks_page){
        creat_p(_cure_page + 1, '&raquo;')
    }
}

