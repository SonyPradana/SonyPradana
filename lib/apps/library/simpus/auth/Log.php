<?php 

namespace Simpus\Auth;
use Simpus\Database\MyPDO;

/**
 * class ini berfungsi untuk membuat log atifity user,
 * tidak semua aktifitas di simpan, informasi penting yang bersifat umum.
 * 
 * @author sonypradana@gmail.com
 */
class Log{
    /** @var string User name*/
    private $_user = '';
    /** @var string event nama*/
    private $_event_type = '';
    /**
     * me-set evebt type
     * @param string $val event set
     */
    public function set_event_type(string $val ){
        $this->_event_type = $val;
    }

    /**
     * membuat log aktifitas baru dari user tertentu
     */
    public function __construct(string $user ){
        $this->_user = $user;
    }

    /**
     * menyimpan log activity ke data base
     * @param string  $query query / kata kunci / informasi penting yang akan disimpan
     * @param boolean true jika data berhasil disimpan
     */
    public function save(string $query ):bool{
        $query = preg_replace('!\s+!', ' ', $query);
        $db = new MyPDO();
        $db->query('INSERT INTO `user_log` (`id`, `creat_time`, `user`, `event_type`, `query`) VALUES (:id, :creat_time, :user, :event_type, :query)');
        $db->bind(':id', '');
        $db->bind(':creat_time', time());
        $db->bind(':user', $this->_user);
        $db->bind(':event_type', $this->_event_type);
        $db->bind(':query', $query);
        $db->execute();
        return $db->rowCount() > 0 ? true : false;
    }

    /**
     * mendaptakan semua log aktifat dari user
     * @param array log aktifitas user
     */
    public function getLog():array{
        $db = new MyPDO();
        $db->query("SELECT * FROM `user_log` WHERE `user`=:user");
        $db->bind(':user', $this->_user);
        return $db->resultset();
    }
}
