<?php namespace Simpus\Auth;

use System\Database\MyPDO;

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
class Privilege
{
    /** @var MyPDO Instant PDO */
    private $PDO;

    private $_userName;

    /**
     * Melihat dan mgedit privilege dari user yang terdaftar
     * @param Auth $user_auth Aunt/user yang akan di lihat
     */
    public function __construct($user_auth)
    {
        $this->PDO = new MyPDO();
        $this->_userName = $user_auth;
    }

    /**
     * Mengambil privilege master dari setiap pages.
     * privilege ini bersifat readonly dari database
     * @param string $taget Target page/service
     * @return string previlage dari database
     */
    public function MasterPrivilage($target): string
    {
        $this->PDO->query('SELECT `target`, `privilege` FROM `privilege_controler` WHERE `target`=:target');
        $this->PDO->bind(':target', $target);
        if( $this->PDO->single() ){
            $row = $this->PDO->single();
            # mengambil nilai privilege di data base
            return $row['privilege'];
        }
        #nilai default bila tidak ada di databse
        return 'r'; #  r -> read / dafult dari user (hanya bisa melihat data secara umum)

    }

    /**
     * Melihat privilage user dari database
     * @param string $target_acces target page yang akan di cek
     * @return string privilege user
     */
    public function ReadAcces($target_acces = "default"): string
    {
        $user_name = $this->_userName;
        $this->PDO->query('SELECT * FROM privilege WHERE `user`=:user AND target=:target');
        $this->PDO->bind(':user', $user_name);
        $this->PDO->bind(':target', $target_acces);
        if( $this->PDO->single() ){
            $row = $this->PDO->single();
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
    public function CreatAcces($target_acces = "default", $privilege): string
    {
        # koneksi dan simpan privile baru ke data base
         $this->PDO->query('INSERT INTO `privilege` (`id`, `user`, `target`, `privilege`) VALUES (:id, :user, :target, :privilege)');
         $this->PDO->bind(':id', '');
         $this->PDO->bind(':user', $this->_userName);
         $this->PDO->bind(':target', $target_acces);
         $this->PDO->bind(':privilege', $privilege);

        #simpan ke databe 'auts'
        $this->PDO->execute();
        # ambil id terahir bila berhasil disimpan
        return $this->PDO->lastInsertId();
    }

    /**
     * mengupdate privilage user
     * @param string $target_acces target Page yang akan di simpan
     * @param string $privilege Nilai privilege yang akan disimpan
     */
    public function ChangeAcces($target_acces = "default", $privilege)
    {
        # koneksi dan simpan privile baru ke data base
        $query = "UPDATE privilege SET privilege = '$privilege' WHERE target = '$target_acces' ";
        #simpan ke databe 'auts'
        mysqli_query($this->conn, $query);
    }

}
