<?php
/**
 * JWT: Jjon Web Token
 * addalh metode penimpnan data berupa json format
 * yang olah menjadi code string url yang dapat di verifikasi
 * oleh pembuat token tersebut,
 * dalam hal ini adalah pemilik kunci rahsia.
 * kunci ini nantinya dapat dijadikan auhofikasi saaat user dan user  membutuhkanya
 * 
 * JWt terdiri dari tiga koponen;
 * - Header : metode atau configure yg digunak dalam token 
 * - Payload : inforamsi yg disimpan, payload dapat saj dibaca oleh sipa saja
 * - siganture  : merupan gabunagn antara header dan payload berta kunci keamaan
 * 
 * @author sonypradan@gmail.com
 * 
 */
class JsonWebToken{
    /** @var array array assoc */
    private $_Header = [], $_PayLoad = [];

    /**
     * membuat jwt denagn paramater yang tealh ditentukan
     * - header
     * - payload
     * - signature
     * 
     * @param array $arrayHeader Array accos header
     *      - type : JWT
     *      - alg  : algoritma yg digunakna
     * @param array $arayPayload Array assoc payload
     *      - uId   : id dari user
     *      - uName : user name
     *      - expt  : expt jwt
     *      - ip    : ip
     *      - uagent : user agant
     * 
     * @return string header . payload . signature
     */
    public function __construct($arrayHeader = ['typ'=>'JWT','alg'=>'HS256'], $arrayPayload = ['uId'=> 0, 'uName'=> '','expt'=> '','ip'=> '','uagent'=> '']){
        $this->_Header = $arrayHeader;
        $this->_PayLoad = $arrayPayload;
    }

    /**
     * simpan jwt dalam bentuk base64code 
     * atau format yg dapat dibaca oleh url
     * 
     * @param string $secretKey kata kunci 
     * @return string JWt 
     */
    public function CreatJWT($secretKey){
         // Buat Array untuk header lalu convert menjadi JSON
         $header = json_encode($this->_Header);
         // Encode header menjadi Base64Url String
         $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
 
         // Buat Array payload lalu convert menjadi JSON
         $payload = json_encode($this->_PayLoad);
         // Encode Payload menjadi Base64Url String
         $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));	
 
         // Buat Signature dengan metode HMAC256
         $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secretKey, true);
         // Encode Signature menjadi Base64Url String
         $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
 
         // Gabungkan header, payload dan signature dengan tanda titik (.)
         $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
         return $jwt;
    }

    
    
}
