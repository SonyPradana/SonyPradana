<?php
/**
 * kelas ini berisi pembuatan password baru
 * passord baru dapat dibuat dengang syarat
 * user telah mendapatkan code untuk reset password
 * dari verifikasi email sebelumnya.
 * kode yg dinputkan user akan di cek kedatabase, jika sesuai user dapat membuat password baru
 * 
 * @author sonypradana@gmail.com 
 */
class ForgotPassword{
    /**
     * @var string user name
     */
    private $userName ;
    /**
     * @var boolean verikasi key
     */
    private $_verifyKey = false;
    /** id link di data base */
    private $_idKey;
    /**
     * cek kombinasi passwod dan code sudah benar atau belum
     * @return boolean verifikasi key
     */
    public function verifyKey(){
        return (boolean) $this->_verifyKey;
    }
    
    /**
     * mengecek kombinasi key dan code sudah sesuai atau belum.
     * 
     * code akan di cek langsung ke database
     * 
     * @param string $key long kay foramat
     * @param string $code code 
     * 6 digit angka
     */
    public function __construct($key, $code){
        #decode from 64 base url
        $decodeKey = base64_decode($key);
        $decodeKey = json_decode($decodeKey, true);
        # cek expt terdaftar atau tidak dan expt timenya (kurang dari 30 menit)
        if( isset( $decodeKey['exp']) ){
            if( $decodeKey['exp'] > time()){
                # key tidak boleh kadaluarsa

                # koneksi data base
                $db = new MyPDO();
                $db->query('SELECT * FROM reset_pwd WHERE link=:link');
                $db->bind(':link', $key);

                if( $db->single() ){
                    $row = $db->single();
                    # mencocokan code ke data base
                    if( $code == $row['code']){
                        $this->_verifyKey = true;
                        $this->userName = $decodeKey['user'];
                    }
                    # get id untuk self distruction
                    $this->_idKey = $row['id'];
                }
                
            }
        }
    }

    /**
     * buat baru password, kemudian
     * logout semua user yg akttif
     * 
     * @param string $new_password new password
     * @return boolean password baru tersimpan atau tidak
     */
    public function NewPassword($new_password){
        if( $this->_verifyKey ){
            #koneksi data base
            $db = new MyPDO();
            #set property
            $user_name = $this->userName;
            $time = time() - 1;
            $newPasssword = password_hash($new_password, PASSWORD_DEFAULT);

            #password baru dibuat
            $db->query('UPDATE `users` SET `pwd`=:pwd, `stat`=:stat, `bane`=:bane WHERE `user`=:user');
            $db->bind(':pwd', $newPasssword);
            $db->bind(':stat', 50);
            $db->bind(':bane', "$time");
            $db->bind(':user', $user_name);
            $db->execute();

            #logout all user 
            $db->query('UPDATE `auths` SET `stat`=:stat WHERE `user`=:user');
            $db->bind(':stat', 0);            
            $db->bind(':user', $user_name);
            $db->execute();

            // user log
            $log = new Log($user_name);
            $log->set_event_type('auth');
            $log->save('forgot password');

            #disable key setalah berhasil menyimpan 
            // if( $disable_key ){
            //     $query = "UPDATE reset_pwd SET `stat` = 0 WHERE `user` = '$user_name'"; 
            //     mysqli_query($link, $query); 
            // }
            #hasil/kembalian adalah true
            return true;
        }
        return false;
    }

    /**
     * mengahaous link dan code dari data base
     * setelah dihapus link dan kode verifikasi sudah tidak berlaku
     * 
     * pastikan mengahpus setelah mengganti password baru
     */
    public function deleteSection(){
        if( $this->_verifyKey){
            $id = $this->_idKey;
            # koneksi data base
            $db = new MyPDO();
            $db->query('DELETE FROM `reset_pwd` WHERE `id`=:id');
            $db->bind(':id', $id);
            $db->execute();
        }
    }

}
