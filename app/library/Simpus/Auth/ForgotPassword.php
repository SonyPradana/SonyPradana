<?php namespace Simpus\Auth;

use System\Database\MyPDO;

/**
 * kelas ini berisi pembuatan password baru
 * passord baru dapat dibuat dengang syarat
 * user telah mendapatkan code untuk reset password
 * dari verifikasi email sebelumnya.
 * kode yg dinputkan user akan di cek kedatabase, jika sesuai user dapat membuat password baru
 *
 * @author sonypradana@gmail.com
 */
class ForgotPassword
{
    /** @var MyPDO Instant PDO */
    private $PDO;
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
    public function verifyKey(): bool
    {
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
    public function __construct(string $key, int $code)
    {
        $this->PDO = MyPDO::getInstance();
        #decode from 64 base url
        $decodeKey = base64_decode($key);
        $decodeKey = json_decode($decodeKey, true);
        # cek expt terdaftar atau tidak dan expt timenya (kurang dari 30 menit)
        if (isset( $decodeKey['exp'])) {
            if ($decodeKey['exp'] > time()) {
                # key tidak boleh kadaluarsa

                # koneksi data base
                $this->PDO->query('SELECT * FROM reset_pwd WHERE link=:link');
                $this->PDO->bind(':link', $key);

                if ($this->PDO->single()) {
                    $row = $this->PDO->single();
                    # mencocokan code ke data base
                    if ($code == $row['code']) {
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
    public function NewPassword($new_password): bool
    {
        if ($this->_verifyKey) {
            #koneksi data base
            #set property
            $user_name = $this->userName;
            $time = time() - 1;
            $newPasssword = password_hash($new_password, PASSWORD_DEFAULT);

            #password baru dibuat
            $this->PDO->query('UPDATE `users` SET `pwd`=:pwd, `stat`=:stat, `bane`=:bane WHERE `user`=:user');
            $this->PDO->bind(':pwd', $newPasssword);
            $this->PDO->bind(':stat', 50);
            $this->PDO->bind(':bane', "$time");
            $this->PDO->bind(':user', $user_name);
            $this->PDO->execute();

            #logout all user
            $this->PDO->query('UPDATE `auths` SET `stat`=:stat WHERE `user`=:user');
            $this->PDO->bind(':stat', 0);
            $this->PDO->bind(':user', $user_name);
            $this->PDO->execute();

            // user log
            $log = new Log($user_name);
            $log->set_event_type('auth');
            $log->save('forgot password');

            return true;
        }
        return false;
    }

    /**
     * mengahapus link dan code dari data base
     * setelah dihapus link dan kode verifikasi sudah tidak berlaku
     *
     * pastikan mengahpus setelah mengganti password baru
     */
    public function deleteSection()
    {
        if ($this->_verifyKey) {
            $id = $this->_idKey;
            # koneksi data base
            $this->PDO->query('DELETE FROM `reset_pwd` WHERE `id`=:id');
            $this->PDO->bind(':id', $id);
            $this->PDO->execute();
        }
    }

}
