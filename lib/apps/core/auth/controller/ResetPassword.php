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
            $db = new MyPDO();
            #query data base
            $user_name = $this->userName;
            $time = time() - 1;
            $new_Passsword = password_hash($new_Passsword, PASSWORD_DEFAULT);
            $db->query('UPDATE `users` SET pwd=:pwd , stat=:stat, bane=:bane WHERE user=:user');
            $db->bind(':pwd', $new_Passsword);
            $db->bind(':stat', 25);
            $db->bind(':bane', "$time()");
            $db->bind(':user', $user_name);
            $db->execute();
            if( $db->rowCount() > 0 ) return true;
        }
        return false;
    }
}
