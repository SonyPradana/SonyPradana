<?php
/**
 * class ini berfungsi untuk meng decode token menjadi array,
 * class ini juga berfungsi untuk meng compare 2 token (token)
 * 
 * @author sonypradan@gmail.com
 */
class DecodeJWT{
    /** @var string JsonWebToken this session */
    private $_jwt;
    /** @var boolen Valid Token */
    private $allow = false;

    /** @var array header dari JsonWebToken */
    private $_algo = [
        'typ' => 'JWT',
        'alg' => 'HS256'
    ];

    /** @var  array payload dari JsonWebToken tell when done*/
    private $_paylaod = [
        'uName' => '',
        'uId' => '',
        'exp' => '',
        'ip' => '',
        'uAgent' => ''
    ];

    /** header array dari JsonWebToken
     * @return array header JsonWebToken
     */
    public function getAlgo(){
        return $this->_algo;
    }
    /** payload array dari JsonWebToken
     * @return array payload JsonWebToken
     */
    public function getPayload(){
        return $this->_paylaod;
    }

    /** decode atau konversi dari token ke JWT array
     * 
     * @param string $token token dalam bentuj JWT encode
     */
    public function __construct($token){
        // pastikan token formanya falid
        if( substr_count($token, ".") === 2){
            // explode token, dibagi menjadi dua
            $split_token = explode('.', $token);
            $algo = base64_decode( $split_token[0] );
            $payload = base64_decode( $split_token[1] );
            // pertama ambil algoritmanya
            $this->_algo = json_decode($algo, true);
            // kedua ambil payloadnya
            $this->_paylaod = json_decode($payload, true);
            // decoding success
            if( isset($this->_algo) && isset($this->_paylaod) ){
                // prevent algo dan payload kosong
                $this->_jwt = $token;
                $this->allow = true;
            }
        }
    }

    /** membuat ulang JWT dengan header dan payload dr token dengan secreat baru.
     * 
     * @param string $secreat_key secreat key
     * @return string JsonWebToken baru
     */
    public function hashCode($secreat_key){
        if( $this->allow ){
            $jwt = new EncodeJWT($this->_algo, $this->_paylaod);
            return $jwt->hashCode( $secreat_key );
        }
        return null;
    }

    /** meng-compare keaslian dari token dengan cara mem buat ulang dengan secrat kay yang sama
     * jika sama true, salah false
     * 
     * @param string @secreat_key Secreat key diambil manual dari data base
     * @return boolean valid atau tidak valid
     */
    public function validate( $secreat_key ):bool{
        if( $this->allow ){
            $new_jwt = new EncodeJWT($this->_algo, $this->_paylaod);
            $jwt = $new_jwt->hashCode( $secreat_key );
            if( $jwt === $this->_jwt ){
                return true;
            }
        }
        return false;
        
    }

    /**
     * merubah array header dan payload ke JsonWebToken object / class
     * 
     * @return JsonWebToken convert ke Obejct JWT
     * */ 
    public function JWT():JsonWebToken{
        $new_jwt = new JsonWebToken();
        $new_jwt->Type = $this->_algo['typ'];
        $new_jwt->Algo = $this->_algo['alg'];
        $new_jwt->User_Name = $this->_paylaod['uName'];
        $new_jwt->User_ID = $this->_paylaod['uId'];
        $new_jwt->expired = $this->_paylaod['exp'];
        $new_jwt->IP = $this->_paylaod['ip'];
        $new_jwt->User_Agent = $this->_paylaod['uAgent'];
        
        return $new_jwt;
    }
}
