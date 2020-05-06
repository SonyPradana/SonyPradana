<?php 
/**
 * class ini berfungsi untuk menferifikasi 
 * token user yg tersimpan, ke data server
 * satu token hanya bisa diugunakan satu browser/ip * 
 * 
 * @author sonypradana@gmail.com
 */
class Auth{
    /** @var string property */
    private $uName, $uId, $expt, $ip, $uAgent;
    /** @var boolean  */
    private $_trushClinet = false;

    /** @return boolean getter */
    public function TrushClient(){
        return $this->_trushClinet;
    }
    /** @return string getter user name */
    public function getUserName(){
        if( $this->_trushClinet){
            return $this->uName;
        }
    }
    /** @return int getter Id databe */
    public function getId(){
        if( $this->_trushClinet){
            return $this->uId;
        }
    }

    /**
     * verifikasi token menggunakan token yang setalah diberikan  seetalah login
     * token akan otomatis kadaluarsa jika 
     * - masa berlaku token habis 4 jam
     * - isi token sudah berubah
     * - di nonaktifkan oleh admin
     * - atau, ip dan user agent tidak sesuai
     * 
     * security level detai:
     * - 0 defult
     * - 1 ip atau user name
     * - 2 ip dan user name 
     * - 3 user name dan ip/user agent
     * 
     * @param string $token jwt token 
     * @param int $securityLevel
     * @return boolean 
     */
    public function __construct($token, $securityLevel = 0){
        # default token is false
        if( substr_count($token, ".") < 2) return; #prevent not string token
        # koneksi database
        $db = new MyPDO();
        # ambil secreatkey dr data  base dengan ifo yg tersedian di payloadnya
        $splitToken = explode('.', $token);
        $payLoad = $splitToken[1];
        $payLoad = base64_decode($payLoad);
        $payLoad = json_decode($payLoad);
        # muat ulang payloadnya
        if( !isset($payLoad) ) return; # prevent non json in token
        $this->uName = $payLoad->uName;
        $this->uId = $payLoad->uId;
        $this->expt = $payLoad->expt;
        $this->ip = $payLoad->ip;
        $this->uAgent = $payLoad->uAgent;
        # cek database
        $db->query('SELECT * FROM auths WHERE id=:id');
        $db->bind(':id', $this->uId);
        if( $db->single() )  {            
            # jika id terdafatar 
            $row = $db->single();  
            
            # Cek jika secretKey benar 
            # CeK Token aktif enggak
            # Cek masa expt udah lewat blm
            if( self::compireToken($token, $row['secret_key'])
             AND $row['stat'] == 1
             AND $this->expt > time()){
                 
                # cek ip dan user agent
                $ip = $_SERVER['REMOTE_ADDR'];             
                $userAgent = $_SERVER['HTTP_USER_AGENT'];   
                # securty level 1=match ip, 2 = match ip AND user agent, defult ip or useragent               
                
                switch ($securityLevel) {
                    case 1:
                        # salah satu user agent dan ip sama
                        if( $this->ip == $ip OR $this->uAgent == $userAgent ){
                            $this->_trushClinet = true;
                        }
                        break;
                    case 2:
                        # user agent dan ip sama
                        if( $this->ip == $ip AND $this->uAgent == $userAgent ){
                            $this->_trushClinet = true;
                        }
                        break;
                    case 3:
                        # user name hrs ada dan ip/user-agnet hrs sama
                        if( $this->uName == $row['user'] AND ($this->ip == $ip OR $this->uAgent == $userAgent) ){
                            $this->_trushClinet = true;
                        }
                        break;
                    default:
                        # ip sama
                        if( $this->ip == $ip ) {
                            $this->_trushClinet = true;
                        }
                        
                }
            } 
        }            
    }

    /**
     * membandingkan signauture denagan header dan payloadnya,
     * denagn mencocokna dengan secreat key nya
     * 
     * @param string $token JWT token yg duji
     * @param string $secretKey kunci utama untuk mengetes
     * @return boolean hasil dari kombinasi headaer payload dan signature
     */
    public static function compireToken($token, $secretKey){
        $spitToken = explode('.', $token);
 
         // Buat Signature dengan metode HMAC256
         $signature = hash_hmac('sha256', $spitToken[0] . "." . $spitToken[1], $secretKey, true);
         // Encode Signature menjadi Base64Url String
         $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
         $newJWT = $spitToken[0] . "." . $spitToken[1] . "." . $base64UrlSignature;

        if( $newJWT == $token) {
            return true;
        }
        return false;
    }

    /**
     * Mengecek privilege user sudah memenuhi syarat privilege Auth. 
     * Meng-compare privilege user dengan privilage auth page.
     * 
     * Warning: case-sensitive, harus lengkap
     * @param string $target 
     * target auth privilage (eg: med-rec, admin)
     * @return boolean 
     * sudah memnuhi atau belum
     */
    public function privilege($target){
        #login cek
        if( !$this->_trushClinet) return false;
        # tampilkan privilege
        $privilege = new Privilege($this->getUserName());
        # privilage user
        $privil = $privilege->ReadAcces($target);
        #privilage auth
        $allow_privilege =  $privilege->MasterPrivilage($target);

        $privil_arr = str_split($privil);
        $privil_arr_allow = str_split($allow_privilege);
        foreach ($privil_arr_allow as $value) {
            if( !in_array($value, $privil_arr)){
                return false;
            }
        }
        return true;
    }

    /**
     * Sigle line authentication, cek auth dan jika salah akan di redirect ke login
     * @param string $redirect target lokasi redirect
     * @return False->redirect ke link
     */
    public function authing($redirect = '/p/auth/login/'){
        if( !$this->_trushClinet){              
            header("Location: " . $redirect);   
            exit();
        }
    }
}

