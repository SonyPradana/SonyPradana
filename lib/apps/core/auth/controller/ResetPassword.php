<?php
/**
 * class ini berfungsi untuk merest passawod / ganti password
 * untuk dapat mengaksesanya harus sudah login
 * user dan password lama hrs hafal
 * 
 * @author sonypradana@gamail.com
 */
class ResetPassword{
    /** @var boolean password beanr atau salah */
    private $password_veryfy = false;
    /** @var string user name */
    private $userName;

    /** @return boolean password atau user banar atau tidak */
    public function passwordVerify(){
        return $this->password_veryfy;
    }

    /**
     * cek user dan password banar atau tidak
     * 
     * @param string $user_name user name
     * @param string $password password
     * 
     * @return boolean password baru
    */
    public function __construct($user_name, $password){
        $this->password_veryfy = Login::PasswordVerify($user_name, $password);        
        $this->userName = $user_name;
    }

    /**
     * buat password baru
     * 
     * @param string password baru
     * @return boolean berhasil atau tidak
     */
    public function newPassword($new_Passsword){
        if( $this->password_veryfy){
            $link = mysqli_connect(DB_HOST, DB_USER, DB_PASS, "simpusle_simpus_lerep");
            #query data base
            $user_name = $this->userName;
            $time = time() - 1;
            $new_Passsword = password_hash($new_Passsword, PASSWORD_DEFAULT);
            $query = "UPDATE users SET pwd = '$new_Passsword' , stat = 25, bane = $time WHERE user = '$user_name'";
            mysqli_query($link, $query);
            return true;
        }
        return false;
    }
}
