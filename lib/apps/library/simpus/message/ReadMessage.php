<?php

namespace Simpus\Message;

/**
 * class ini digunakan untuk melihat pesan yg dikirim/diterima dan disimpan pada data base.
 * 
 * @author sonypradana@gmail.com
 */
class ReadMessage{
    /** @var string pengirim */
    private $_sender;
    /** @var string penerima */
    private $_resiver;
    /** @var string category pesan */
    private $_type;
    /** @var string content message */
    private $_message;
    /** @var integer tanggal*/
    private $_date;
    /** @var integer data yang ditampilkan */
    private $_limit = 10;
    /** @var string lebih besar atau lebih kecil */
    private $_sign = ">";

    /** @var string opsi tampilkan data - sender */
    private $_view_sender = true;
    /** @var string opsi tampilkan data - resiver */
    private $_view_resiver = true;
    /** @var string opsi tampilkan data - type */
    private $_view_type = true;
    /** @var string opsi tampilkan data - date */
    private $_view_date = true;
    /** @var string opsi tampilkan data - message */
    private $_view_message = true;
    /** @var string opsi tampilkan data - meta */
    private $_view_meta = true;

    /** filter pencarian berdasarkan pengrim pesan
     * 
     * @param string $val pengirim pesan
     */
    public function filterByPengirim($val){
        // senitizer masukan
        $val = strtolower($val);

        $this->_sender = $val;
        return $this;
    }
    /** filter pencarian berdasarkan penerima pesan
     * 
     * @param string $val penerima pesan
     */
    public function filterByPenerima($val){
        // senitizer masukan
        $val = strtolower($val);

        $this->_resiver = $val;
        return $this;
    }
    /** filter pencarian berdasarkan jenis/category pesan
     * 
     * @param string $val jenis pesan
     */
    public function filterByType($val){
        // senitizer masukan
        $val = strtolower($val);

        $this->_type = $val;
        return $this;
    }
    /** filter pencarian berdasarkan isi pesan/content
     * 
     * @param string $val content pesan
     */
    public function filterByMrssage($val){
        // senitizer masukan
        $val = strtolower($val);

        $this->_message = $val;
        return $this;
    }
    /** filter pencarian berdasarkan tanggal pengriman
     * 
     * @param string $val tanggal pengriman pesan
     * @param string $sign tanggal lebih baru gunakan ">",
     * tanggal lebih lama gunakan "<"
     */
    public function filterByDate($val, $sign){
        // senitizer
        $sign = $sign == ">" || $sign == "<" ? $sign : ">";

        $this->_date = $val;
        $this->_sign = $sign;
        return $this;
    }
    
    /** menampilkan hasil sebanyak
     * 
     * bilangan bulat 1-100
     * @param integer $val batah data ditampilkan
     */
    public function limitView($val){
        // sinitizer
        if( is_numeric($val) ){
            $val = $val < 1 ? 1 : $val;
            $val = $val > 100 ? 100 : $val;

            $this->_limit = $val;
            return $this;
        }
    }

    /** membatasi data yang akan ditampilkan, resiver.
     * @param boolean $val true(default) data ditapilkan, false data disembunyokan
     */
    public function viewResiver($val){
        $this->_view_resiver = is_bool($val) ? $val : $this->_view_resiver;
        return $this;
    }

    /**
     * membuat sebuah query string dari filter dan configurasi lainya, menjadi query yang dibaca mesin.
     * 
     * @return string query pencarian sql database
     */
    private function query()
    {
        $sender = $this->queryBuilder('sender', $this->_sender);
        $resiver = $this->queryBuilder('resiver', $this->_resiver);
        $type = $this->queryBuilder('type', $this->_type);
        $msg = $this->queryBuilder('message', $this->_message);
        $date = $this->queryBuilder_diff('date', $this->_date, $this->_sign);
        $limit = $this->_limit;

        $all =  $sender . $resiver . $type . $msg . $date;
        // replace frist ' AND '
        $all = preg_replace('/^( AND )/', '', $all);
        $all = $all != '' ? " WHERE ($all)" : $all;

        // menampilkan data yang hanya diinginkan
        $v_sender = $this->colmBulider('sender', $this->_view_sender);
        $v_resiver = $this->colmBulider('resiver', $this->_view_resiver);
        $v_type= $this->colmBulider('type', $this->_view_type);
        $v_date = $this->colmBulider('date', $this->_view_date);
        $v_message = $this->colmBulider('message', $this->_view_message);
        $v_meta = $this->colmBulider('meta', $this->_view_meta);

        $select = $v_sender . $v_resiver . $v_type . $v_date . $v_message . $v_message . $v_meta;
        // replace end (, )
        $select = preg_replace('/(, )$/', '', $select);
        $select = $select == '' ? '*' : $select;
        
        return "SELECT $select FROM public_message$all LIMIT $limit";
    }

    /**
     * helper untuk mebuat sebuah parameter pada query, 
     * mengubah format input dari user ke input parameter query
     * 
     * @param string $key nama colom pada data base
     * @param string $val isi data dari colom yg akan digunakan
     * @return string format parameter query (eg: AND table_name = 'value')
     */
    private function queryBuilder($key, $val)
    {
        if ($val != '') {
            $bulider = " AND " ."$key" . " LIKE " . "'$val'";
            return $bulider;
        }
        return '';
    }

    /**
     * helper untuk mebuat sebuah parameter pada query, 
     * mengubah format input dari user ke input parameter query
     * 
     * dengan mmaksud mengambil selisih dari parameter yang dicari.
     * 
     * @param string $key nama colom pada data base
     * @param string $val isi data dari colom yg akan digunakan
     * @param string $sign tanda baca ntuk mencari data diatas atau dibawah parameter
     * @return string format parameter query (eg: AND table_name > 'value')
     */
    private function queryBuilder_diff($key, $val, $sign = ">")
    {
        $sign = $sign == ">" ? $sign : "<";
        if ($val != '') {
            $bulider = " AND " ."$key" . $sign . "'$val'";
            return $bulider;
        }
        return '';
    }

    /**
     * helper untuk mebuatt sebuah parameter pada query,
     * menampilkan data berdasarkan coloumn database yang dipilih
     * 
     * @param string $key nama column data base
     * @param boolean $val opsi untuk nenampilkan column pada result
     */
    private function colmBulider($key, $val)
    {
        if ($val) {
            $bulider = $key . ', ';
            return $bulider;
        }
        return '';
    }

    /**
     * menampilkan hasil pencarian setalah di filter terlebih dahulu, 
     * apabila blm di tentukan filter sebelumnya makan tidak ada hasil yang ditampilkan
     * 
     * @param boolean convert data dalam bentuk json
     * @return array hasil dari table data base
     */
    public function bacaPesan($convrt_to_json = false)
    {
        // koneksi dan membuat query
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        $query = $this->query();
        // echo $query;
        if ($query != '') {
            // mengambil data dari database
            $result = mysqli_query($link, $query);
            $data = [];
            while ($feedback = mysqli_fetch_assoc( $result)) {
                $data[] = $feedback;
            }
            // mengembalikan data dengan format array
            return $convrt_to_json ? json_encode( $data ) : $data;
        }
        // tidak bs memuat data karena tidak query yang akan dicari
        return $convrt_to_json ? json_encode( [] ) : [];
    }
    /**
     * menampilkan semua hasil yang ada di data base
     * 
     * @return array hasil dari table data base
     */
    public function bacaSemua(): array
    {
        // koneksi data base
        $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        $limit = $this->_limit;
        $query = "SELECT * FROM public_message LIMIT $limit";
        // mengambil data dari data base (mengambil semua tanpa di filter)
        $result = mysqli_query($link, $query);
        $data = array();
        while ($feedback = mysqli_fetch_assoc( $result)) {
            $data[] = $feedback;
        }
        // mengembailkan data dengan format array 
        return $data;
    }
}
