<?php
/**
 * Class untuk melihat, mengedit dan mebuat privilege dari user.
 * privile user dibagi menjadi:
 * - r: read -> untuk tamu/nologin menampilkan data ecara umum
 * - R: read -> user hanya dapat melihat data
 * - w: write -> user dapat membuat 
 * - W: write(kapital) -> user dapat membuat dan mengedit
 * - A: adimin(kapital) -> melakukan manipulasi data dan user
 * dan target adalah file/class/folder/link yang akan diesekusi
 * 
 */
class Privilege{

    private $_userName;

    /**
     * Melihat dan mgedit privilege dari user yang terdaftar
     * @param Auth $user_auth Aunt/user yang akan di lihat
     */
    public function __construct($user_auth){
        $this->_userName = $user_auth;
    }

    /**
     * Mengambil privilege master dari setiap pages.
     * privilege ini bersifat readonly dari database
     * @param string $taget Target page/service
     */
    public function MasterPrivilage($target){
        # buat koneksi
        $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        $query = mysqli_query($link, "SELECT target, privilege FROM privilege_controler WHERE target = '$target'");
        if( mysqli_num_rows( $query ) == 1 ){
            $row = mysqli_fetch_assoc( $query );
            # mengambil nilai privilege di data base
            return $row['privilege'];
        }
        #nilai default bila tidak ada di databse
        return 'r'; #  r -> read / dafult dari user (hanya bisa melihat data secara umum)

    }

    /**
     * Melihat privilage user dari database
     * @param string $target_acces target page yang akan di cek
     * @return privilege user
     */
    public function ReadAcces($target_acces = "default"){
        $user_name = $this->_userName;
        # buat koneksi
        $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
        $query = mysqli_query($link, "SELECT * FROM privilege WHERE user = '$user_name' AND target = '$target_acces'");
        if( mysqli_num_rows( $query ) == 1 ){
            $row = mysqli_fetch_assoc( $query );
            # mengambil nilai privilege di data base
            return $row['privilege'];
        }
        return 'r';
    }

    /**
     * Membuat privilage user 
     * @param string $target_acces target Page yang akan di simpan
     * @param string $privilege Nilai privilege yang akan disimpan
     */
    public function CreatAcces($target_acces = "default", $privilege){
        # koneksi dan simpan privile baru ke data base
        $query = "INSERT INTO privilege VALUES ('', '$this->_userName', '$target_acces', '$privilege')";

        #simpan ke databe 'auts'
        mysqli_query($this->conn, $query);
        # ambil id terahir bila berhasil disimpan
        return mysqli_insert_id($this->conn);
    }

    /**
     * mengupdate privilage user 
     * @param string $target_acces target Page yang akan di simpan
     * @param string $privilege Nilai privilege yang akan disimpan
     */
    public function ChangeAcces($target_acces = "default", $privilege){
        # koneksi dan simpan privile baru ke data base
        $query = "UPDATE privilege SET privilege = '$privilege' WHERE target = '$target_acces' ";
        #simpan ke databe 'auts'
        mysqli_query($this->conn, $query);        
    }

}
