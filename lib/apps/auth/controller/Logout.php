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
            $conn = new DbConfig();
            $link = $conn->StartConnection();
            # query data base
            $query = "UPDATE auths SET `stat` = 0 WHERE `id` = '$tokenId'"; 
            mysqli_query($link, $query); 
            # bila berhasil return true
            $res = mysqli_affected_rows($link);
            if( $res > 0){
                return true;
            }

        }
        #defult nya adalah false
        return false;
    }
}

