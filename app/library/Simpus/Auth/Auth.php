<?php namespace Simpus\Auth;

use System\Database\MyPDO;

/**
 * class ini berfungsi untuk menferifikasi 
 * token user yg tersimpan, ke data server
 * satu token hanya bisa diugunakan satu browser/ip * 
 * 
 * @author sonypradana@gmail.com
 */
class Auth
{
    const IP_OLNY = 0;
    const USER_AGENT_OR_IP = 1;
    const USER_AGENT_AND_IP = 2;
    const USER_NAME_AND_USER_AGENT_OR_IP = 3;
    /** @var MyPDO Instant PDO */
    private $PDO;
    /** @var boolean  */
    private $_trushClinet = false;
    /** @var JsonWebToken this Json Web Token */
    private $_jwt;

    /** @return boolean getter */
    public function TrushClient(): bool
    {
        return (bool) $this->_trushClinet;
    }
    /** @return string getter user name */
    public function getUserName()
    {
        if ($this->_trushClinet) {
            return $this->_jwt->User_Name;
        }
    }
    /** @return int getter Id databe */
    public function getId()
    {
        if ($this->_trushClinet) {
            return $this->_jwt->User_ID;
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
     */
    public function __construct(string $token, int $securityLevel = AUTH::IP_OLNY)
    {
        if( $token == "" ) return;
        # koneksi database
        $this->PDO = new MyPDO();
        $new_jwt = new DecodeJWT($token);
        $JWT = $new_jwt->JWT();
        $this->_jwt = $JWT;
        # cek database
        $this->PDO->query('SELECT * FROM auths WHERE id=:id');
        $this->PDO->bind(':id', $JWT->User_ID);
        if ($this->PDO->single())  {
            # jika id terdafatar 
            $row = $this->PDO->single();  
            
            # Cek jika secretKey benar 
            # CeK Token aktif enggak
            # Cek masa expired udah lewat blm
            if ($new_jwt->validate( $row['secret_key'])
             && $row['stat'] == 1
             && $JWT->expired > time()) {
                 
                # cek ip dan user agent
                $ip = $_SERVER['REMOTE_ADDR'];             
                $userAgent = $_SERVER['HTTP_USER_AGENT'];   
                # securty level 1=match ip, 2 = match ip AND user agent, defult ip or useragent               
                
                switch ($securityLevel) {
                    case 1:
                        # salah satu user agent dan ip sama
                        if ($JWT->IP == $ip OR $JWT->User_Agent == $userAgent) {
                            $this->_trushClinet = true;
                        }
                        break;
                    case 2:
                        # user agent dan ip sama
                        if ($JWT->IP == $ip AND $JWT->User_Agent == $userAgent) {
                            $this->_trushClinet = true;
                        }
                        break;
                    case 3:
                        # user name hrs ada dan ip/user-agnet hrs sama
                        if ($JWT->User_Agent == $row['user'] AND ($JWT->IP == $ip OR $JWT->User_Agent == $userAgent)) {
                            $this->_trushClinet = true;
                        }
                        break;
                    default:
                        # ip sama
                        if ($JWT->IP == $ip) {
                            $this->_trushClinet = true;
                        }                        
                }
            } 
        }            
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
    public function privilege($target): bool
    {
        #login cek
        if (! $this->_trushClinet) return false;
        # tampilkan privilege
        $privilege = new Privilege($this->getUserName());
        # privilage user
        $privil = $privilege->ReadAcces($target);
        #privilage auth
        $allow_privilege =  $privilege->MasterPrivilage($target);

        $privil_arr = str_split($privil);
        $privil_arr_allow = str_split($allow_privilege);
        foreach ($privil_arr_allow as $value) {
            if (!in_array($value, $privil_arr)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Sigle line authentication, cek auth dan jika salah akan di redirect ke login
     * @param string $redirect target lokasi redirect
     */
    public function authing($redirect = '/login/')
    {
        if (! $this->_trushClinet) {
            header("Location: " . $redirect);   
            exit();
        }
    }
}

