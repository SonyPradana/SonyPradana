<?php namespace Simpus\Auth;

use System\Database\MyPDO;
use System\Database\MyQuery;

/**
 * class ini berfungsi untuk mendaptakan informsai akun / profil dari user
 *
 * @author sonypradana@gmail.com
 */
class User
{
  /** @var MyPDO Instant PDO */
  private $PDO;
  /** @var string user name (tidak bisa dirubah)*/
  private $_user;
  /** @var boolean cek user ada atau tidak */
  private $_exisUser = false;
  /** @var string Nama tampilan user*/
  private $_displayName = null;
  /** @var string Email user*/
  private $_email = null;
  /** @var string unit kerja */
  private $_section = null;
  /** @var string Display picture */
  private $_displayPicture = null;

  /** @return boolean Mengecek validitas user */
  public function userVerify(): bool
  {
    return $this->_exisUser;
  }

  /** @return string Mendapatkan Nama tampilan user*/
  public function getDisplayName()
  {
    return $this->_displayName;
  }

  /** @return string Mendapatkam alamat email user*/
  public function getEmail()
  {
    return $this->_email;
  }

  /** @return string Unit kerja / bagian */
  public function getSection()
  {
    return $this->_section;
  }

  /** @return string Url image display picture */
  public function getDisplayPicture()
  {
    return $this->_displayPicture;
  }

  /** @return string Url small-image display picture */
  public function getSmallDisplayPicture()
  {
    $file_name = explode('/', $this->_displayPicture);
    $file_name = end( $file_name );
    $small_image = str_replace($file_name, "small-" . $file_name, $this->_displayPicture);
    $return = file_exists( $_SERVER['DOCUMENT_ROOT'] . $small_image) ? $small_image : '/public/data/img/display-picture/user/small-no-image.png';
    return $return;
  }

  // setter
  /**
   * menggati display name baru     *
   * @param string $val nama tampilan baru
   */
  public function setDisplayName(string $val)
  {
    // senitazer
    $val = htmlspecialchars($val);

    $this->_displayName = $val;
  }

  /**
   * menggati unit kerja / bagian
   * @param string unit kerja baru
   */
  public function setSection(string $val)
  {
    // senitazer
    $val = htmlspecialchars($val);

    $this->_section = $val;
  }

  /**
   * mengganti alamat display picture
   * @param string url display picture
   */
  public function setDisplayPicture(string $val)
  {
    $this->_displayPicture = $val;
  }

  /**
   * mencari data denagn user name yang sesaui
   *
   * @param string $user_name User Name yang digunakna
   */
  public function __construct($user_name)
  {
    if( is_null($user_name) ) return;
    // senetalizer user_name
    $user_name = strtolower($user_name);
    $this->_user = $user_name;

    # koneksi data base
    $this->PDO = MyPDO::getInstance();
    $this->PDO->query('SELECT * FROM `profiles` WHERE `user`=:user');
    $this->PDO->bind(':user', $user_name);
    if ($row = $this->PDO->single()) {
      $this->_exisUser      = true;
      $this->_email         = $row['email'];
      $this->_displayName   = $row['display_name'];
      $this->_section       = $row['section'];
      $this->_displayPicture = $row['display_picture'];
    }
  }

  /**
   * menyimpan perubahan profile ke dalam data base
   */
  public function saveProfile()
  {
    if ($this->_exisUser == false) return false;

    $this->PDO
      ->query('UPDATE `profiles` SET `display_name`=:dname, `section`=:section, `display_picture`=:dp WHERE `user`=:user')
      ->bind(':dname', $this->_displayName)
      ->bind(':section', $this->_section)
      ->bind(':user', $this->_user)
      ->bind(':dp', $this->_displayPicture)
      ->execute();
    // user log
    $log = new Log($this->_user);
    $log->set_event_type('auth');
    $log->save('success profile changes');
  }
}
