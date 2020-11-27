// terser -c -m -- lib/js/bundles/dialog.modal.js > lib/js/bundles/dialog.modal.min.js
const def_pref =  {
    type    : 'search-by-name-adress',
    status  : 'run',
    parrent : $event('modal-dialog'), 
    trigger : 'show-modal',             /** triger dom id name*/
    result  : function(res){
        /** do somethink (this is callback) */
        console.table( res );
    },
    content : {
        input_nama   : $id('input-modal-nama'),
        input_alamat : $id('input-modal-alamat'),
        table_result : $id('modal-table-body')
    }
}
function modal_dialog( app ){
    app = Object.assign(def_pref, app)

    $event(app.trigger).click(function () {
        app.content.table_result.innerHTML = null
        app.parrent.show()
    })

    $event('modalClose').click(function () {
        app.parrent.hidden()
    })

    $event('btn-modal-close').click(function (){
        app.parrent.hidden()
    })

    window.onclick = function (e) {
        if (e.target == app.parrent.el) {
            app.parrent.hidden()
        }
    }

    $event('btn-modal-cari').click( function(){
        let nama         = app.content.input_nama.value
        if( nama == null) return false;
        let alamat       = app.content.input_alamat.value
        let search_query = `main-search=${nama}&alamat-search=${alamat}`
        $json(`/api/ver1.0/kia-anak/search.json?strict-search=on&${search_query}`)
            .then( json => {
                if( json.status == 'ok'){
                    app.status = 'ok'
                    renderTable(json.data)             
                }
            })
    })

    // const search_by_name_adress

    function renderTable(data){
        app.content.table_result.innerHTML = null
        let num = 1
        
        if( data.length == 0 ){
            app.content.table_result.innerText = 'data not found'
            return false
        }

        data.forEach(res => {
            let tr = document.createElement('tr')

            let action = document.createElement('th')
            let button = document.createElement('button')
            button.className = "btn rounded light blue fill number" 
            button.innerText = "pilih"
            button.addEventListener('click', function(){
                app.result(res)
                app.status = 'found'
                app.parrent.hidden()
            })
            action.appendChild( button )

            tr.appendChild( creat_th(num) )
            tr.appendChild( creat_th(res.nama) )
            tr.appendChild( creat_th(res.tanggal_lahir) )
            tr.appendChild( creat_th(res.nama_kk) )
            tr.appendChild( action, 'html' )

            app.content.table_result.appendChild(tr)
            num++;
        });
        
    }

    function creat_th(innerValue, innerType = "text") {
        let th = document.createElement('th')
        if (innerType == "html") {
            th.innerHTML = innerValue
        } else if (innerType == "text") {
            th.innerText = innerValue
        }
        return th
    }
}
