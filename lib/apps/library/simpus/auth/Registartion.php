<?php

namespace Simpus\Auth;
use Simpus\Database\MyPDO;

/**
 *  Class Registrasi fungsinya untuk menyimpan user baru ke database
 *  sekaligus menferifikasi user baru
 *  
 *  cara kerjanya:
 *  setelah user mengisi form, data aka dikirm ke kelas ini
 *  kemudain data dismpan ke databse virifikasi. 
 *  untuk menuggu Admin menferifikasi secara manul akun baru,
 *  baru setalah terferivikasi akan user dapat menggunakn acun-nya secara normal
 *  (data base dipindah dari penampungan ke --> data base yg benar)
 * 
 * @author Angger Pradana sonypradana@gmail.com
 */
class Registartion{
    /**
     * property
     * @var string User name, email, password, display name
     */
    private $_userName, $_email, $_password, $_disName;

    /**
     *  syartat $data : array asso
     *  - data['userName']  = user name
     *  - data['email'] = email
     *  - data['passwod'] = password yg sudah si endcrypt
     *  - data['dispName'] = nama yang ditampilkan
     * 
     * @param array $data Data dalam bentuk array assosiatif
     * @return Users new user
     */
    public function __construct($data = []){
        $this->_userName = strtolower( $data['userName'] );
        $this->_email = strtolower( $data['email'] );
        $this->_password = $data['password'];
        $this->_disName = $data['dispName'];   
    }

    /**
     * verifikasi ada tidak user yang terdafat dengan nama / email yg sama
     * 
     * result:
     *  - 1 : "user telah tedaftar"
     *  - 2 : "email telah terdaftar"
     *  - 3 : "user dan email telah tedaftar"
     *  - 4 : "user dan email boleh digunakan"
     *  - 0 : "terjadi kesalahan";
     * 
     * @param string $user_name Cek user terdaftar
     * @param string $email Cek email terdaftar
     * @return int 1-4 code error pengecekan
     */
    public function Verify($user_name, $email):int{
        # cek user name
        $newUser = new User($user_name);
        $veifyUser = $newUser->userVerify();

        # cek emai
        $newEmail = new EmailAuth($email);
        $verifyEmail =  $newEmail->userVerify();

        # return
        if( $veifyUser ){
            # user telah terdaftar
            return 1;
        }elseif( $verifyEmail ){
            # email telah terdaftar
            return 2;
        }elseif( $veifyUser AND $verifyEmail ){
            # user dan email terdaftar
            return 3;
        }else {
            # user dan email tersedia
            return 4;
        }
        #defult terjadi kesalahan
        return 0;
    }

    /**
     * simpan perimtaan ke database sementara
     *  - true -> user dan email blm terdaftar
     *  - false -> user dan email sudah terdaftar
     * 
     * @return boolean disimpan atau tidak
     */
    public function AddToArchive():bool{
        # koneksi data base
        $db = new MyPDO();
        # query data base
        $user = $this->_userName;
        $email = $this->_email;
        $pwd = $this->_password;
        $pwd = password_hash($pwd, PASSWORD_DEFAULT);
        $disp_name = $this->_disName;
        # simpan kedata base
        $db->query('INSERT INTO `registration` (`id`, `user`, `email`, `pwd`, `disp_name`, `stat`) VALUES (:id, :user, :email, :pwd, :disp_name, :stat)');
        $db->bind(':id', '');
        $db->bind(':user', $user);
        $db->bind(':email', $email);
        $db->bind(':pwd', $pwd);
        $db->bind(':disp_name', $disp_name);
        $db->bind(':stat', 1);
        $db->execute();
        # result
        $res = $db->rowCount();
        if( $res > 0){
            return true;
        }
        return false;
    }

}

