<?php
/**
 * class ini berfung untuk membuat verifikasi ganti password
 * via email, bila terdafat code ferifikasi akan dikir ke email atau 
 * di kirim ke Admin
 */
class EmailAuth{
    /** @var string user name */
    private $_userName;
    /** @var string hasil key / link */
    private $_keyResult;    
    /** @var boolean user tedaftar atau tidak */
    private $_userVerify = false;

    /** @var boolean cek user tedaftar atau tidak */
    public function userVerify(){
        return $this-> _userVerify;
    }
    /** @var string hasil key / link */
    public function KeyResult(){
        return $this->_keyResult;
    }

    /** 
     * ***verifikasi email***
     * simpan permintaan ganti password ke database
     * result nya --> link gati password
     * 
     * @param string $email email pemulih
     */
    public function __construct($email){
        # koneksi data base
        $conn = new DbConfig();
        $link  = $conn->StartConnection();
        $query = mysqli_query($link, "SELECT user FROM profiles WHERE email = '$email' ");
        if( mysqli_num_rows( $query ) == 1) {
            $row = mysqli_fetch_assoc($query);
            #simpan parameter
            $this-> _userVerify = true;
            $this->_userName = $row['user'];

            #creat key
            $this->CreatKey();
        }   
    }
    
    /**
     * buat key / link dengan format base 64 code
     * agar terbaca oleh url
     * berisi username, dan time expt    * 
     * 
     */
    private function CreatKey(){
        # property
        $user = $this->_userName;
        $expt = time() + 1800; #30 menit
        # menyusun key        
        $key = ['user'=>$user,
                'expt'=>$expt];
        $key = json_encode($key);
        $key = base64_encode($key);
        # random int bwettwen 10.00.00 - 99.99.99.99
        $val = rand(100000, 999999);
        # simpaan ke data bsae
        $conn = new DbConfig();
        $link  = $conn->StartConnection();
        $query = mysqli_query($link, "INSERT INTO reset_pwd VALUE ('',  '$key', '$val')");
        mysqli_query($link, $query);
        # hasil key
        # key adalah alamat link untuk dikonfirmsi user menggukan kode yg telah dibuat
        $this->_keyResult = $key;        
    }
}
