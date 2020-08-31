<?php

namespace Simpus\Auth;
use Simpus\Database\MyPDO;

/**
 * class logout adalah kelas untuk menghilangakn hak akser user 
 * dan mendisable / mematikan jwt
 * 
 * @author sonypradana@gmail.com
 */
class Logout{
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
    public function __construct(string $token){
        #veifikasi token
        $verify = new Auth($token, 2);
        if( $verify->TrushClient() ){
            #decode token
            $tokenId = $verify->getId();
            # koneksi data base
            $db = new MyPDO();
            # query data base
            $db->query('UPDATE `auths` SET `stat`=:stat WHERE `id`=:id');
            $db->bind(':stat', 0);
            $db->bind(':id', $tokenId);
            $db->execute();
            # bila berhasil return true
            $res = $db->rowCount();
            if( $res > 0){
                // user log
                $log = new Log( $verify->getUserName() );
                $log->set_event_type('auth');
                $log->save('success logout');
                
                $this->_success = true;
                exit();
            }

        }
    }
}

