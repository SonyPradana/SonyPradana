<?php namespace Simpus\Auth;

use System\Database\MyPDO;

/**
 * class ini berfungsi untuk membuat log atifity user,
 * tidak semua aktifitas di simpan, informasi penting yang bersifat umum.
 * 
 * @author sonypradana@gmail.com
 */
class Log
{
    /** @var MyPDO Instant PDO */
    private $PDO;
    /** @var string User name*/
    private $_user = '';
    /** @var string event nama*/
    private $_event_type = '';
    /**
     * me-set evebt type
     * @param string $val event set
     */
    public function set_event_type(string $val)
    {
        $this->_event_type = $val;
    }

    /**
     * membuat log aktifitas baru dari user tertentu
     */
    public function __construct(string $user)
    {
        $this->PDO = new MyPDO();
        $this->_user = $user;
    }

    /**
     * menyimpan log activity ke data base
     * @param string  $query query / kata kunci / informasi penting yang akan disimpan
     * @param boolean true jika data berhasil disimpan
     */
    public function save(string $query ): bool
    {
        $query = preg_replace('!\s+!', ' ', $query);
        $this->PDO->query('INSERT INTO `user_log` (`id`, `creat_time`, `user`, `event_type`, `query`) VALUES (:id, :creat_time, :user, :event_type, :query)');
        $this->PDO->bind(':id', '');
        $this->PDO->bind(':creat_time', time());
        $this->PDO->bind(':user', $this->_user);
        $this->PDO->bind(':event_type', $this->_event_type);
        $this->PDO->bind(':query', $query);
        $this->PDO->execute();
        return $this->PDO->rowCount() > 0 ? true : false;
    }

    /**
     * mendaptakan semua log aktifat dari user
     * @param array log aktifitas user
     */
    public function getLog(): array
    {
        $this->PDO->query("SELECT * FROM `user_log` WHERE `user`=:user");
        $this->PDO->bind(':user', $this->_user);
        return $this->PDO->resultset();
    }
}
