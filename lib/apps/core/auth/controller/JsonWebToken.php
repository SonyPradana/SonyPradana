<?php
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
class JsonWebToken{
    /** @var string type data (JWT) */
    public $Type;
    /** @var string alogoritma encode yang digunkan */
    public $Algo;
    
    /** @var string User Name */
    public $User_Name;
    /** @var string User Id saat login */
    public $User_ID;
    /** @var string Waktu kadaluarsa (dalam stamptime) */
    public $expired;
    /** @var string Ip yang digunkan saat login */
    public $IP;
    /** @var string User Agent yang digunakan saat login */
    public $User_Agent;
}
