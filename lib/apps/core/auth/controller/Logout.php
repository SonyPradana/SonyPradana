<?php
/**
 * class logout adalah kelas untuk menghilangakn hak akser user 
 * dan mendisable / mematikan jwt
 * 
 * @author sonypradana@gmail.com
 */
class Logout{

    /**
     * logut dengan token
     * 
     * @param sting $token valid token
     * token yang di kirim harus token aktif dan memliliki token tersebut
     * @return boolean logout diterima atau tidak
     */
    public function __construct($token){
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
                return true;
            }

        }
        #defult nya adalah false
        return false;
    }
}

