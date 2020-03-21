<?php
/**
 * class login fungsinya untuk meniram permintaan logindari user
 * bila user name dan pasword ccok maka maka user dibeikan akses dan 
 * user akan meniram token, token ini akan digunakn untuk login kembali
 * di kesempatan berikutnya
 * 
 * @author Angger Pradana sonypradana@gmail.com
 */
class Login{
    /**
     * @var string user name
    */
    private $_userName = 'guest';
    /**
     * @var string password
     */
    private $_password = 'password';
    /**
     * @var string Hasil dr JWT
     */
    private $_JWTResult = ''; 
    /**
     * @var boolean ke aslian jwt
     */
    private $_verifyJWT = false;  
    /**
     * @var mysqli_conection koneksi ke data base
     */
    private $conn ;
    
    /**
     * secretKey adalah secreat key sebagai kunci diJWT signature. 
     * secretkey sudah tersedikan untuk membatasi dan mengontrol key
     * 
     * @var Array secret key
    */
    private $_secretKey = ['ungaran', 'sumbing', 'semeru', 'lawu', 'sindoro',
                            'slamet', 'prau', 'merbabu', 'merapi', 'andong'];

    /**
     * Diperoleh jika logiin berhasil
     * 
     * @return string Hasil dr JWT
     */
    public function JWTResult(){
        return $this->_JWTResult;
    }
    
    /**
     * @var boolean user login status
     */
    public function VerifyLogin(){
        return (boolean) $this->_verifyJWT;;
    }

    /**
     * Dalam satu pemanggilan hanya ada satu kali login
     * JWT hanya dibuat jika user dan password sesuai
     * batas kesalahan login ke sever sebanyak 25 X
     * jika lebih dari itu di bane selama 4 jam
     * 
     * @param string $user_name user name 
     * @param string $password password
     * @return JWT Untuk Verifikasi login berikutnya
     */
    public function __construct($user_name, $password){
        # sanitalizier input
        // $user_name = StringSanitization::removeHtmlTags($user_name);

        #simpan paramater ke kelas
        $this->_userName = strtolower( $user_name );
        $this->_password = $password;
        #koneksi databse
        $this->conn = new DbConfig();
        $this->conn = $this->conn->StartConnection();
        #query data base
        $query = mysqli_query($this->conn, "SELECT pwd, stat FROM users WHERE user = '$user_name'");
        if( mysqli_num_rows( $query ) === 1 )  {
            $row = mysqli_fetch_assoc( $query );
            #cek password 
            if( password_verify($this->_password, $row['pwd'])){
                #berhasil login
                #jwt dibuat
                $this->CreatJWT();
                $this->_verifyJWT = true;
            }else{
                #password salah kirm ke database
                $minusStat = $row['stat'] - 1;
                if( $minusStat === 0 ){
                    #jika salah > 8 x user dabe 8 jam
                    $baneUntil = time() + (3600 * 8);
                    $query = "UPDATE users SET `stat` = 0, `bane` = '$baneUntil' WHERE `user` = '$user_name'";
                }else{
                    $query = "UPDATE users SET `stat` = '$minusStat' WHERE `user` = '$user_name'";
                }
                #simpan query
                mysqli_query($this->conn, $query);
            }
        }
    }

    /** 
     * JWT hanya dibuat ketika user Valid,
     * jika tidak Token yg dikembalikan kosong
     *
     * @return JWT 
     */
    private function CreatJWT(){
        #parameter yg disimpan diJWT:
        #secratcode
        $secretKey =  $this->_secretKey[ array_rand($this->_secretKey) ];
        #header
        $JWT_Header = ['typ'=>'JWT','alg'=>'HS256'];
            #algoritma
            
        #payload
        $userId = $this->SaveJWTInfo($secretKey);
        $expt = time() + (3600 * 8);
        $ip = $_SERVER['REMOTE_ADDR'];
        $userAgent = $_SERVER['HTTP_USER_AGENT'];      
        $JWT_PayLoad = ['uId'=> $userId, 
                        'uName'=> $this->_userName,
                        'expt'=> $expt,
                        'ip'=> $ip,
                        'uAgent'=> $userAgent];
        
        #buat JWT
        $JWT = new JsonWebToken($JWT_Header, $JWT_PayLoad);
        #hasil dari jwt 
        $this->_JWTResult = $JWT->CreatJWT($secretKey);
    }
    /**
     * simpan keyJWT ke database server guna verivikasi token
     * 
     * @param mixed $secrerKey secret kay yg akan digunakn dan disimpan
     */
    private function SaveJWTInfo($secretKey){
        $query = "INSERT INTO auths VALUES ('', '$this->_userName', 1, '$secretKey')";

        #simpan ke databe 'auts'
        mysqli_query($this->conn, $query);
        return mysqli_insert_id($this->conn);
    }    

    /**
     * cek user sedang dibane oleh service atau tidak
     * - true, user dan password sesuai
     * - false, user salah memasukan pwd > 8x, atau dlm masa tangguh ( 8jam )
     * 
     * @param mixe $user_name user name
     * @return boolean user valid atau tidak
     */
    public static function BaneFase($user_name){       
        #koneksi data base
        $_conn = new DbConfig();
        $query = mysqli_query($_conn->StartConnection(), "SELECT user, stat, bane FROM users WHERE user ='$user_name'");
        if( mysqli_num_rows( $query ) === 1 )  {           
            $row = mysqli_fetch_assoc( $query );
            if( $row['stat'] > 0 AND $row['bane'] < time()) {
                # jika jumlah kesalahan lebih dari 0 dan
                # tidak dalam waktu bane (8 jam dri ksealahan terakhir)
                return false;
            }
        }
        #defult nya adalah dibane
        return true;
    }
    
    /**
     * RefreshBane, jika waktu bane sudah habis 
     * dan stat/kesalah masih > 0    
     * 
     * @param string $user_name username
     */
    public static function RefreshBaneFase($user_name){
        #koneksi data base
        $_conn = new DbConfig();
        $query = mysqli_query($_conn->StartConnection(), "SELECT user, stat, bane FROM users WHERE user ='$user_name'");
        if( mysqli_num_rows( $query ) === 1 )  {           
            $row = mysqli_fetch_assoc( $query );
            if( $row['stat'] == 0 AND $row['bane'] < time()) {
                #set user ke defult / hapus bane
                $query = "UPDATE users SET `stat` = 25, `bane` = time() WHERE `user` = '$user_name'";
                mysqli_query($_conn->StartConnection(), $query);
            }
        }
    }

    /**
     * cek login tanda menyimpan kesalahan data base
     * WARNING : proses pengecekan tidak terbatas, gunakan dengan hati-hati
     * 
     * @param string $user_name user name 
     * @param string $password password
     * @return boolean user dan password banar atau tidak
     */
    public static function PasswordVerify($user_name, $password){
        $conn = new DbConfig();
        $link = $conn->StartConnection();
        #query data base
        $query = mysqli_query($link, "SELECT pwd, stat FROM users WHERE user ='$user_name'");
        if( mysqli_num_rows( $query ) === 1 )  {
            $row = mysqli_fetch_assoc( $query );
            #cek password 
            if( password_verify($password, $row['pwd'])){
                return true;
            }
        }
        return false;
    }
}
