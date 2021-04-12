<?php namespace Simpus\Auth;

use System\Database\MyPDO;

/**
 * Class untuk melihat, mengedit dan mebuat privilege dari user (medel / crud)
 * privile user dibagi menjadi:
 * - r: read -> untuk tamu/nologin menampilkan data secara umum
 * - R: read -> user hanya dapat melihat data
 * - w: write -> user dapat membuat
 * - W: write(kapital) -> user dapat membuat dan mengedit
 * - A: adimin(kapital) -> melakukan manipulasi data dan user
 * dan target adalah file/class/folder/link yang akan diesekusi
 *
 */
class Privilege
{
  /** @var MyPDO Instant PDO */
  private $PDO;

  private $_userName;

  /**
   * Melihat dan mgedit privilege dari user yang terdaftar (CRUD)
   * @param Auth $user_auth Aunt/user yang akan di lihat
   */
  public function __construct(string $user_auth, MyPDO $PDO = null)
  {
    $this->PDO = $PDO ?? MyPDO::getInstance();
    $this->_userName = $user_auth;
  }

  /**
   * Mengambil privilege master dari setiap pages.
   * privilege ini bersifat readonly dari database
   * @param string $taget Target page/service
   * @return string previlage dari database
   */
  public function getMasterPrivilage($target): string
  {
      $this->PDO->query('SELECT `target`, `privilege` FROM `privilege_controler` WHERE `target`=:target');
      $this->PDO->bind(':target', $target);
      $row = $this->PDO->single();

      if (! empty($row)) {
        # mengambil nilai privilege di data base
        return $row['privilege'];
      }

      #nilai default bila tidak ada di databse
      return 'r'; #  r -> read / dafult dari user (hanya bisa melihat data secara umum)
  }

  /**
   * Melihat privilage user dari database
   * @param string $target_acces target page yang akan di cek
   * @return string privilege user
   */
  public function getUserPrivilage($target = "default"): string
  {
    $user_name = $this->_userName;
    $this->PDO->query('SELECT * FROM privilege WHERE `user`=:user AND target=:target');
    $this->PDO->bind(':user', $user_name);
    $this->PDO->bind(':target', $target);
    $row = $this->PDO->single();

    if (! empty($row)) {
      # mengambil nilai privilege di data base
      return $row['privilege'];
    }
    return 'r';
  }


  /**
   * Membandingkan user dan master privilage,
   * apabila satu syarat tidak tepenuhi maka Access ditolak
   * @param string $target Section page
   */
  public function getAccess(string $target): bool
  {
    $userPrevilage    = $this->getUserPrivilage($target);
    $masterPrevilage  = $this->getMasterPrivilage($target);

    if ($userPrevilage === "A") return true;

    $arr_user       = str_split($userPrevilage);
    $arr_master     = str_split($masterPrevilage);
    foreach ($arr_master as $allowPrevilage) {
      // jika satu saja syarat tidak dipenuhi maka permintaan ditolak
      if (! in_array($allowPrevilage, $arr_user)) {
        return false;
      }
    }

    return true;
  }
}
