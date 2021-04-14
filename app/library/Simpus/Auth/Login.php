<?php namespace Simpus\Auth;

use System\Database\MyPDO;

/**
 * class login fungsinya untuk meniram permintaan logindari user
 * bila user name dan pasword ccok maka maka user dibeikan akses dan
 * user akan meniram token, token ini akan digunakn untuk login kembali
 * di kesempatan berikutnya
 *
 * @author Angger Pradana sonypradana@gmail.com
 */
class Login
{
  /** @var MyPDO Instant PDO */
  private $PDO;
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
   * secretKey adalah secreat key sebagai kunci diJWT signature.
   * secretkey sudah tersedikan untuk membatasi dan mengontrol key
   *
   * @var Array secret key
  */
  private $_secretKey = [
    'ungaran', 'sumbing', 'semeru', 'lawu', 'sindoro',
    'slamet', 'prau', 'merbabu', 'merapi', 'andong'
  ];

  /**
   * Diperoleh jika logiin berhasil
   *
   * @return string Hasil dr JWT
   */
  public function JWTResult(): string
  {
    return $this->_JWTResult;
  }

  /**
   * @var boolean user login status
   */
  public function VerifyLogin(): bool
  {
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
  public function __construct(string $user_name, string $password)
  {
    //  sanitalizier input
    // $user_name = StringSanitization::removeHtmlTags($user_name);

    // simpan paramater ke kelas
    $this->_userName = strtolower( $user_name );
    $this->_password = $password;
    // koneksi databse
    $this->PDO = MyPDO::getInstance();
    // query data base
    $this->PDO->query('SELECT `pwd`, `stat` FROM `users` WHERE `user`=:user');
    $this->PDO->bind(':user', $user_name);
    if ($this->PDO->single()) {
      $row = $this->PDO->single();
      // cek password
      if (password_verify($this->_password, $row['pwd'])) {
        // berhasil login
        // jwt dibuat
        $this->CreatJWT();
        $this->_verifyJWT = true;
        // user log
        $log = new Log($user_name);
        $log->set_event_type('auth');
        $log->save('success login');

      } else {
        // password salah kirm ke database
        $minusStat = $row['stat'] - 1;
        if ($minusStat === 0) {
          // jika salah > 8 x user dibane 8 jam
          $baneUntil = time() + (3600 * 8);
            $this->PDO
              ->query('UPDATE `users` SET `stat`=:stat, `bane`=:bane WHERE `user`=:user')
              ->bind(':stat', 0)
              ->bind(':bane', $baneUntil)
              ->bind(':user', $user_name);
        } else {
            $this->PDO
              ->query('UPDATE `users` SET `stat`=:stat WHERE `user`=:user')
              ->bind(':stat', $minusStat)
              ->bind(':user', $user_name);
        }
        // simpan query
        $this->PDO->execute();
      }
    }
  }

  /**
   * JWT hanya dibuat ketika user Valid,
   * jika tidak Token yg dikembalikan kosong
   *
   * @return JWT
   */
  private function CreatJWT()
  {
    // parameter yg disimpan diJWT:
    // secratcode
    $secretKey =  $this->_secretKey[ array_rand($this->_secretKey) ];
    // header
    $JWT_Header = [
      'typ' => 'JWT',
      'alg' => 'HS256',
    ];

    // payload
    $userId = $this->SaveJWTInfo($secretKey);
    $expt = time() + (3600 * 8); // kadaluarsa dalam 8 jam
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $JWT_PayLoad = [
      'uId'   => $userId,
      'uName' => $this->_userName,
      'exp'   => $expt,
      'ip'    => $ip,
      'uAgent'=> $userAgent,
    ];

    // buat JWT
    $JWT = new EncodeJWT($JWT_Header, $JWT_PayLoad);
    // hasil dari jwt
    $this->_JWTResult = $JWT->hashCode($secretKey);
  }
  /**
   * simpan keyJWT ke database server guna verivikasi token
   *
   * @param mixed $secrerKey secret kay yg akan digunakn dan disimpan
   */
  private function SaveJWTInfo(string $secretKey)
  {
    $this->PDO
      ->query('INSERT INTO `auths` (`id`, `user`, `stat`, `secret_key`) VALUES (:id, :user, :stat, :secretKey)')
      ->bind(':id', '')
      ->bind(':user', $this->_userName)
      ->bind(':stat', 1)
      ->bind(':secretKey', $secretKey)
      // simpan ke databe 'auts'
    ->execute();

    return $this->PDO->lastInsertId();
  }

  /**
   * cek user sedang dibane oleh service atau tidak
   * - true, user dan password sesuai
   * - false, user salah memasukan pwd > 8x, atau dlm masa tangguh ( 8jam )
   *
   * @param mixe $user_name user name
   * @return boolean user valid atau tidak
   */
  public static function BaneFase($user_name): bool
  {
    // koneksi data base
    $db = MyPDO::getInstance();
    $db->query('SELECT user, stat, bane FROM users WHERE user=:user');
    $db->bind(':user', $user_name);

    if ($row = $db->single()) {
      if ($row['stat'] > 0 AND $row['bane'] < time()) {
        //  jika jumlah kesalahan lebih dari 0 dan
        //  tidak dalam waktu bane (8 jam dri ksealahan terakhir)
        return false;
      }
    }
    // defult nya adalah dibane
    return true;
  }

  /**
   * RefreshBane, jika waktu bane sudah habis
   * dan stat/kesalah masih > 0
   *
   * @param string $user_name username
   */
  public static function RefreshBaneFase($user_name): void
  {
    // koneksi data base
    $db = MyPDO::getInstance();
    $db->query('SELECT `user`, `stat`, `bane` FROM `users` WHERE `user`=:user');
    $db->bind(':user', $user_name);
    if ($row = $db->single()) {
      if ($row['stat'] == 0 AND $row['bane'] < time()) {
          // set user ke defult / hapus bane
          $db
            ->query('UPDATE `users` SET `stat`:stat, `bane`:bane WHERE `user`=:user')
            ->bind(':stat', 25)
            ->bind(':bane', "time()")
            ->bind(':user', $user_name)
            ->execute();
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
  public static function PasswordVerify(string $user_name, string $password): bool
  {
    $db = MyPDO::getInstance();
    // query data base
    $db->query('SELECT `pwd`, `stat` FROM `users` WHERE `user`=:user');
    $db->bind(':user', $user_name);
    if ($row = $db->single()) {
      // cek password
      if (password_verify($password, $row['pwd'])) {
        return true;
      }
    }
    return false;
  }
}
