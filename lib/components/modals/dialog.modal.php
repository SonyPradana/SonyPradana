<h2>Cari Data anak</h2>
<div class="search-box">
    <section>
        <label for="input-modal-alamat">Alamat</label>
        <select class="textbox outline black rounded light" name="modal_alamat" id="input-modal-alamat">
            <option selected disabled hidden>Pilih Desa</option>
            <option value="bandarjo">Bandarjo</option>
            <option value="branjang">Branjang</option>
            <option value="kalisidi">Kalisidi</option>
            <option value="keji">Keji</option>
            <option value="lerep">Lerep</option>
            <option value="nyatnyono">Nyatnyono</option>
        </select>
    </section>
    <section>
        <label for="input-modal-nama">Nama</label>
        <input class="textbox outline black rounded light" type="text" name="modal_nama" id="input-modal-nama" autocomplete="off">
    </section>
    <button class="btn rounded light blue fill" type="button" id="btn-modal-cari">Cari</button>
</div>
<div class="result-box">
    <table>
        <thead>
            <tr>
                <th id="t-nomor">No</th>
                <th>Nama</th>
                <th>Tanggal lahir</th>
                <th>Nama KK</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id='modal-table-body'>
            <tr>
                <th>1</th>
                <th>2</th>
                <th>3</th>
                <th>4</th>
                <th><a href=""></a></th>
            </tr>
        </tbody>
    </table>
</div>
<div class="footer-box">
    <button class="btn rounded light red fill" type="button" id="btn-modal-close">close</button>
    </div>
