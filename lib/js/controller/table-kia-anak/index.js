let tableKiaAnak = {
    el: "table.data-rm tbody",
    table_type: "search",
    sort: "tanggal_dibuat",
    order: "ACS",
    curentPage: 1,
    maksPage: 1,

    search_query: "",
    search_name: "",
    search_nameKK: "",

    ucwords: str => (str + '').replace(/^([a-z])|\s+([a-z])/g, $1 => $1.toUpperCase()),
    addTag: (str, find) => str.replace(find, `<span style="color:blue">${find}</span>`),
    newDate: date => date.replace(/(\d{4})\-(\d{2})\-(\d{2})/, '$3-$2-$1'),

    create_el: function (innerValue, innerType = "text") {
        let th = document.createElement('th')
        if (innerType == "html") {
            th.innerHTML = innerValue
        } else if (innerType == "text") {
            th.innerText = innerValue
        }
        return th
    },

    fetchJSON: async function (url) {
        const response = await fetch(url, {
            header: {
                'Content-Type': 'application/json'
            }
        })
        return response.json()
    },

    getData: function (sort, order, cure_page, search_query) {
        const url_search = `/lib/ajax/json/private/kia-anak/search/?sort=${sort}&order=${order}&page=${cure_page}${search_query}`
        const url_view = `/lib/ajax/json/private/med-rec/table-view/?sort=${sort}&order=${order}&page=${cure_page}${search_query}`
        const url = this.table_type === "search" ? url_search : url_view

        this.fetchJSON(url)
            .then(json => {
                if (json['status'] == 'ok') {
                    this.maksPage = json['maks_page']
                    this.curentPage = json['cure_page']
                    if (this.table_type == 'search') {
                        this.renderTableSearch(json['data'])
                    } else {
                        this.renderTableView(json['data'])
                    }

                    let dom_not_found = document.querySelector('.box-right p.info')
                    dom_not_found.style.display = "none"
                    if (json['data'].length == 0) {
                        dom_not_found.style.display = "block"
                    }

                    this.renderPagination()
                }
            })
    },

    sortTable: function (sort) {
        if (this.order == 'ASC' && this.sort == sort) {
            this.order = 'DESC'
        } else {
            this.order = 'ASC'
        }
        this.sort = sort
        this.curentPage = 1
        this.getData(this.sort, this.order, this.curentPage, this.search_query)

    },

    renderTableSearch: function (data) {
        const page = 10
        let i = (this.curentPage * page) - (page - 1)
        let dom_target = document.querySelector(this.el)
        dom_target.innerHTML = ''
        
        data.forEach(element => {
            let row = document.createElement('tr');

            // validate value
            nama        = this.ucwords(this.addTag(element['nama'], this.search_name));
            tanggal     = element['tanggal_lahir'] === "" ? '0000-00-00' : element['tanggal_lahir'];
            tanggal     = this.newDate(tanggal);
            alamat      = this.ucwords(element['alamat']);
            rt_rw       = element['nomor_rt'] + ' / ' + element['nomor_rw'];
            nama_kk     = this.ucwords(this.addTag(element['nama_kk'], this.search_nameKK));
            grups_posyandu = element['posyandu'];
            action      = `<a class="link" href="/kia-anak/edit/biodata/?document_id=${element['code_hash']}">edit</a>`

            // append row
            row.appendChild(this.create_el( i ))
            row.appendChild(this.create_el(nama,    "html"))
            row.appendChild(this.create_el( tanggal ))
            row.appendChild(this.create_el( alamat ))
            row.appendChild(this.create_el( rt_rw ))
            row.appendChild(this.create_el(nama_kk, "html"))
            row.appendChild(this.create_el(grups_posyandu, "html"))
            row.appendChild(this.create_el(action,  "html"))

            // append row ke table
            dom_target.appendChild(row)
            i++
        })
    },
    obj: {
        other: "hay"
    },

    renderTableView: function (data) {
        const page = 25
        let i = (this.curentPage * page) - (page - 1)
        let dom_target = document.querySelector(this.el)
        dom_target.innerHTML = ''

        data.forEach(element => {
            let row = document.createElement('tr')

            // validate value
            nama    = this.ucwords(this.addTag(element['nama'], this.search_name))
            tanggal = element['tanggal_lahir'] === "" ? '0000-00-00' : element['tanggal_lahir']
            tanggal = this.newDate(tanggal)
            alamat  = this.ucwords(element['alamat'])
            rt_rw   = element['nomor_rt'] + ' / ' + element['nomor_rw']
            nama_kk = this.ucwords(this.addTag(element['nama_kk'], this.search_nameKK))
            action  = `<a class="link" href="/kia-anak/edit/biodata/?document_id=${element['id_hash']}">edit</a>`
            // biodata
            jk      = element['jenis_kelamin'] == 1 ? "Laki-laki" : "Perempuan"

            // append row
            row.appendChild(this.create_el( i       ))
            row.appendChild(this.create_el( nama    ))
            row.appendChild(this.create_el( tanggal ))
            row.appendChild(this.create_el( alamat  ))
            row.appendChild(this.create_el( rt_rw   ))
            row.appendChild(this.create_el( nama_kk ))
            row.appendChild(this.create_el( action  ))
            // biodata
            row.appendChild(this.create_el( jk      ))
            row.appendChild(this.create_el( element['bbl'] ))
            row.appendChild(this.create_el( element['pbl'] ))
            row.appendChild(this.create_el( element['imd'] ))
            row.appendChild(this.create_el( element['kia'] ))
            row.appendChild(this.create_el( element['asi_eks'] ))

            // append row ke table
            dom_target.appendChild(row)
            i++
        })
    },

    renderPagination() {
        let dom_paginaiton = document.querySelector('.pagination')
        dom_paginaiton.innerHTML = '<!-- pagination -->'

        let creat_p = (page, text) => {
            let p = document.createElement('a')
            if (page == this.curentPage) {
                p.className = 'active'
            }
            p.href = 'javascript:void(0)'
            p.addEventListener('click', () => {
                this.curentPage = page
                this.getData(this.sort, this.order, this.curentPage, this.search_query)
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

        if (this.curentPage > 1) {
            creat_p(this.curentPage - 1, '&laquo;')
        }
        if (this.maksPage > 5) {
            // satu didepan
            creat_p(1, 1)
            // tiga ditengah
            if (this.curentPage > 2 && this.curentPage < (this.maksPage - 1)) {
                creat_s()
                // prev page, curret page, next page
                creat_p(this.curentPage - 1, this.curentPage - 1)
                creat_p(this.curentPage, this.curentPage)
                creat_p(this.curentPage + 1, this.curentPage + 1)
                creat_s()
            } else if (this.curentPage < 4) {
                // page 2 & 3
                creat_p(2, 2)
                creat_p(3, 3)
                creat_s()
            } else if (this.curentPage > (this.maksPagee - 2)) {
                // 2 dari belakang
                creat_s()
                creat_p(this.maksPage - 2, this.maksPage - 2)
                creat_p(this.maksPage - 1, this.maksPage - 1)
            }
            // satu dibelakang
            creat_p(this.maksPage, this.maksPage)
        } else if (this.maksPage < 6) {
            // page 1-5
            for (let i = 1; i <= this.maksPage; i++) {
                creat_p(i, i)
            }
        }
        if (this.curentPage < this.maksPage) {
            creat_p(this.curentPage + 1, '&raquo;')
        }

    }
}
