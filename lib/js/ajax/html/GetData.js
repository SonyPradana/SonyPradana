
    /**
     * ambil data dari databe menggunakan ajax keambalian berupa html tag
     * @param {string} sort sort using ? eg: nama, nomor_rm, dln
     * @param {string} order oreder using ASC or DESC
     * @param {array} filters costume filter eg: range y=umur, desa, satasu kk
     */
    function GDcostumeFilter(sort, order, page, filters) {
        
        var sendAjax = new XMLHttpRequest();
        sendAjax.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {            
                //berhasil dipanggil
                // this.responseText;
                var box = document.querySelector(".box-right")
                box.innerHTML = this.responseText;
                // console.log(this.responseText);
            }
        }
        if (filters.length < 8) { 
            // default
            sendAjax.open('GET', "/lib/ajax/inner-html/table-view-rm.php?sort="+ sort + "&order=" + order + "&page=" + page, true);
        }else{
            // using filters
            var r = filters[0]; // filter umur
            var d1 = filters[1]; // filter ds bandarjo
            var d2 = filters[2]; // filter ds branjang
            var d3 = filters[3]; // filter ds kalisidi
            var d4 = filters[4]; // filter ds keji
            var d5 = filters[5]; // filter ds lerep
            var d6 = filters[6]; // filter ds nyatnyono
            var sk = filters[7]; // filter status kk

            sendAjax.open('GET', "/lib/ajax/inner-html/table-view-rm.php?sort="+ sort + "&order=" + order + "&page=" + page
                                    + "&r=" + r
                                    + "&d1=" + d1 
                                    + "&d2=" + d2 
                                    + "&d3=" + d3 
                                    + "&d4=" + d4 
                                    + "&d5=" + d5 
                                    + "&d6=" + d6 
                                    + "&sk=" + sk, true);
        }
        
        sendAjax.send();
    }
    /**
     * Ambil data dari database menggunakan paramater yg ada dan dikembalikan dalam html-tag
     * @param {string} sortby Sorting data using (nomir_rm, nama, dln)
     * @param {string} orderby Ordeing data using ACS or DESC
     * @param {int} page Menampilkan data pada halaman
     */
    function getTableSearch(sortby, orderby, page, main_search, nomor_rm, strict_search, tgl_lahir, alamat, nomor_rt, nomor_rw, nama_kk, nomor_rm_kk) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) { 
                var box = document.querySelector(".box-right")
                box.innerHTML = this.responseText;
            }
        }
        // set/get url parameter
        // var search_query = JSON.parse(filters);
        xhr.open('GET', "/lib/ajax/inner-html/table-search-rm.php?sort="+ sortby + "&order=" + orderby + "&page=" + page
                        + urlBuilder("main-search", main_search)
                        + urlBuilder("strict-search", strict_search)
                        + urlBuilder("nomor-rm-search", nomor_rm)
                        + urlBuilder("tgl-search", tgl_lahir)
                        + urlBuilder("alamat-search", alamat)
                        + urlBuilder("no-rt-search", nomor_rt)
                        + urlBuilder("no-rw-search", nomor_rw)
                        + urlBuilder("nama-kk-search", nama_kk)
                        + urlBuilder("no-rm-kk-search", nomor_rm_kk), true);
        
        xhr.send();
    };
    function urlBuilder(key, value) {
        if( value !== '' ){
            return '&' + key + '=' + value;
        }
        return '';
    }
