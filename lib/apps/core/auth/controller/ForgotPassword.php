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
        if( isset( $decodeKey['expt']) ){
            if( $decodeKey['expt'] > time()){
                # key tidak boleh kadaluarsa

                # koneksi data base
                $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");         
                $query = mysqli_query($link, "SELECT * FROM reset_pwd WHERE link = '$key' ");

                if( mysqli_num_rows( $query ) == 1 ){
                    $row = mysqli_fetch_assoc($query);
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
            $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
            #set property
            $user_name = $this->userName;
            $time = time() - 1;
            $newPasssword = password_hash($new_password, PASSWORD_DEFAULT);

            #password baru dibuat
            $query = "UPDATE users SET pwd = '$newPasssword', stat = 50, bane = $time WHERE user = '$user_name'";         
            mysqli_query($link, $query);

            #logout all user 
            $query = "UPDATE auths SET `stat` = 0 WHERE `user` = '$user_name'"; 
            mysqli_query($link, $query); 

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
            $link  = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");       
            $query = "DELETE FROM `reset_pwd` WHERE id = '$id'";
            mysqli_query($link, $query);
        }
    }

}
