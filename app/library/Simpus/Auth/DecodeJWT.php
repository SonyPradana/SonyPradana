<?php

namespace Simpus\Auth;

/**
 * class ini berfungsi untuk meng decode token menjadi array,
 * class ini juga berfungsi untuk meng compare 2 token (token)
 *
 * @author sonypradan@gmail.com
 */
class DecodeJWT
{
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
  public function __construct($token)
  {
    // pastikan token formanya falid
    if (substr_count($token, ".") === 2) {
      // explode token, dibagi menjadi dua
      $split_token = explode('.', $token);

      // convert to string AND convert to array
      $algo    = json_decode(
        base64_decode($split_token[0]), true);
      $paylaod = json_decode(
        base64_decode($split_token[1]), true);

        // prevent algo dan payload kosong
      if (isset($algo)
      && isset($paylaod)) {
        // decoding success

        $this->_algo    = $algo;
        $this->_paylaod = $paylaod;
        $this->_jwt     = $token;
        $this->allow    = true;
      }
    }
  }

  /** meng-compare keaslian dari token dengan cara mem buat ulang dengan secrat kay yang sama
   * jika sama true, salah false
   *
   * @param string @secreat_key Secreat key diambil manual dari data base
   * @return boolean valid atau tidak valid
   */
  public function validate(string $secreat_key): bool
  {
    if ($this->allow) {
      $new_jwt = new EncodeJWT($this->_algo, $this->_paylaod);
      $jwt = $new_jwt->hashCode( $secreat_key );
      if ($jwt === $this->_jwt) {
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
  public function JWT(): JsonWebToken
  {
    $jwt = new JsonWebToken();
    $jwt->Type        = $this->_algo['typ'];
    $jwt->Algo        = $this->_algo['alg'];
    $jwt->User_Name   = $this->_paylaod['uName'];
    $jwt->User_ID     = $this->_paylaod['uId'];
    $jwt->expired     = $this->_paylaod['exp'];
    $jwt->IP          = $this->_paylaod['ip'];
    $jwt->User_Agent  = $this->_paylaod['uAgent'];
    $jwt->JWT         = $this->_jwt;

    return $jwt;
  }
}
