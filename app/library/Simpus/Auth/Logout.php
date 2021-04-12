<?php namespace Simpus\Auth;

use System\Database\MyPDO;

/**
 * class logout adalah kelas untuk menghilangakn hak akser user
 * dan mendisable / mematikan jwt
 *
 * @author sonypradana@gmail.com
 */
class Logout
{
    /** @var MyPDO Instant PDO */
    private $PDO;
    /** @var bool User logout status */
    private $_success = false;
    /**
     * User logout status
     * @return bool True on success
     */
    public function isSuccess():bool{
        return $this->_success;
    }

    /**
     * logut dengan token
     *
     * @param sting $token valid token
     * token yang di kirim harus token aktif dan memliliki token tersebut
     */
    public function __construct(string $token)
    {
        $this->PDO = MyPDO::getInstance();
        #veifikasi token
        $verify = new Auth($token, 2);
        if ($verify->TrushClient()) {
            #decode token
            $tokenId = $verify->getId();
            # query data base
            $this->PDO->query('UPDATE `auths` SET `stat`=:stat WHERE `id`=:id');
            $this->PDO->bind(':stat', 0);
            $this->PDO->bind(':id', $tokenId);
            $this->PDO->execute();
            # bila berhasil return true
            $res = $this->PDO->rowCount();
            if ($res > 0) {
                // user log
                $log = new Log( $verify->getUserName() );
                $log->set_event_type('auth');
                $log->save('success logout');

                $this->_success = true;
            }

        }
    }
}

