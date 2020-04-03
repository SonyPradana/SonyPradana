
    /**
     * ambil data dari databe menggunakan ajax keambalian berupa html tag
     * @param {string} sort sort using ? eg: nama, nomor_rm, dln
     * @param {string} order oreder using ASC or DESC
     * @param {array} filters costume filter eg: range y=umur, desa, satasu kk
     */
    function GDcostumeFilter(sort, order, filters) {
        
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
            sendAjax.open('GET', "/lib/ajax/inner-html/table-view-rm.php?sort="+ sort + "&order=" + order, true);
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

            sendAjax.open('GET', "/lib/ajax/inner-html/table-view-rm.php?sort="+ sort + "&order=" + order 
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
