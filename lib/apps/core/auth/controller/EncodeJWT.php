<?php
/**
 * class ini berfungsi untuk men encodo atau membuat token dari para meter yang ditentukan
 * 
 * @author sonypradan@gmail.com 
 */
class EncodeJWT{
    /** @var array array assoc */
    private $_Header = [], $_PayLoad = [];

    /**
     * membuat jwt dengan paramater yang tealh ditentukan
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
    public function __construct($arrayHeader = ['typ'=>'JWT','alg'=>'HS256'], $arrayPayload = ['uId'=> 0, 'uName'=> '','exp'=> '','ip'=> '','uagent'=> '']){
        $this->_Header = $arrayHeader;
        $this->_PayLoad = $arrayPayload;
    }

    /**
     * simpan jwt dalam bentuk base64code (token)
     * atau format yg dapat dibaca oleh url
     * 
     * @param string $secretKey kata kunci 
     * @return string JWt 
     */
    public function hashCode($secretKey):string{
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
