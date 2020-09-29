<?php

namespace Simpus\Auth;
use Simpus\Database\MyPDO;

/**
 * class ini berfungsi untuk merest passawod / ganti password
 * untuk dapat mengaksesanya harus sudah login
 * user dan password lama hrs hafal
 * 
 * @author sonypradana@gamail.com
 */
class ResetPassword
{
    /** @var MyPDO Instant PDO */
    private $PDO;
    /** @var boolean password beanr atau salah */
    private $password_veryfy = false;
    /** @var string user name */
    private $userName;

    /** @return boolean password atau user banar atau tidak */
    public function passwordVerify():bool{
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
    public function __construct($user_name, $password)
    {
        $this->PDO = new MyPDO();
        $this->password_veryfy = Login::PasswordVerify($user_name, $password);        
        $this->userName = $user_name;
    }

    /**
     * buat password baru
     * 
     * @param string password baru
     * @return boolean berhasil atau tidak
     */
    public function newPassword($new_Passsword): bool
    {
        if( $this->password_veryfy){
            #query data base
            $user_name = $this->userName;
            $time = time() - 1;
            $new_Passsword = password_hash($new_Passsword, PASSWORD_DEFAULT);
            $this->PDO->query('UPDATE `users` SET pwd=:pwd , stat=:stat, bane=:bane WHERE user=:user');
            $this->PDO->bind(':pwd', $new_Passsword);
            $this->PDO->bind(':stat', 25);
            $this->PDO->bind(':bane', "$time()");
            $this->PDO->bind(':user', $user_name);
            $this->PDO->execute();
            // user log
            $log = new Log($user_name);
            $log->set_event_type('auth');
            $log->save('reset password');

            if( $this->PDO->rowCount() > 0 ) return true;
        }
        return false;
    }
}
