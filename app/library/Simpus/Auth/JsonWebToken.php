<?php

namespace Simpus\Auth;

/**
 * JWT: Json Web Token
 * adalah metode penyimpanan data berupa json format
 * yang diubah menjadi code string url yang dapat di verifikasi oleh pembuat token tersebut,
 * dalam hal ini adalah pemilik seacret_key.
 * kunci ini nantinya dapat dijadikan  sebagai authorization saat user dan user mengakses
 *
 * JWt terdiri dari tiga komponen;
 * - Header : metode atau configure yg digunak dalam token
 * - Payload : inforamsi yg disimpan, payload dapat saj dibaca oleh sipa saja
 * - siganture  : merupan gabunagn antara header dan payload berta kunci keamaan
 *
 * @author sonypradan@gmail.com
 *
 */
class JsonWebToken
{
  /** @var string type data (JWT) */
  public string $Type;
  /** @var string alogoritma encode yang digunkan */
  public string $Algo;

  /** @var string User Name */
  public string $User_Name;
  /** @var string User Id saat login */
  public string $User_ID;
  /** @var string Waktu kadaluarsa (dalam stamptime) */
  public string $expired;
  /** @var string Ip yang digunkan saat login */
  public string $IP;
  /** @var string User Agent yang digunakan saat login */
  public string $User_Agent;

  /** @var string Result JWT */
  public string $JWT;

}
