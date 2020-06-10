<?php
/**
 * class ini berfungsi untuk mendaptakan informsai akun / profil dari user
 * 
 * @author sonypradana@gmail.com
 */
class User{
    /** @var string user name (tidak bisa dirubah)*/
    private $_user;
    /** @var boolean cek user ada atau tidak */
    private $_exisUser = false;
    /** @var string Nama tampilan user*/
    private $_displayName;
    /** @var string Email user*/
    private $_email;
    /** @var string unit kerja */
    private $_section;

    /** @return boolean Mengecek validitas user */
    public function userVerify(){
        return $this->_exisUser;
    }
    /** @return string Mendapatkan Nama tampilan user*/
    public function getDisplayName(){
        return $this->_displayName;
    }
    /** @return string Mendapatkam alamat email user*/
    public function getEmail(){
        return $this->_email;
    }
    /** @return string Unit kerja / bagian */
    public function getSection(){
        return $this->_section;
    }
    // setter
    /** 
     * menggati display name baru     * 
     * @param string $val nama tampilan baru
     */
    public function setDisplayName($val){
        // senitazer
        $val = htmlspecialchars($val);
        
        $this->_displayName = $val;
    }
    /** 
     * menggati unit kerja / bagian
     * @param string unit kerja baru
     */
    public function setSection($val){
        // senitazer
        $val = htmlspecialchars($val);

        $this->_section = $val;
    }

    /**
     * mencari data denagn user name yang sesaui
     * 
     * @param string $user_name User Name yang digunakna
     */
    public function __construct($user_name){
        // senetalizer user_name
        $user_name = strtolower($user_name);
        $this->_user = $user_name;

        # koneksi data base
        $db = new MyPDO();
        $db->query('SELECT * FROM `profiles` WHERE `user`=:user');
        $db->bind(':user', $user_name);
        if( $db->single() ){
            $this->_exisUser = true;
            $this->_email = $db->single()['email'];
            $this->_displayName = $db->single()['display_name'];
            $this->_section = $db->single()['section'];
        }
    }

    public function saveProfile(){
        if( $this->_exisUser == false ) return false;
        $user_name = $this->_user;
        $display_name = $this->_displayName;
        $section = $this->_section;

        $db = new MyPDO();
        $db->query('UPDATE `profiles` SET `display_name`=:dname, `section`=:section WHERE `user`=:user');
        $db->bind(':dname', $display_name);
        $db->bind(':section', $section);
        $db->bind(':user', $user_name);
        $db->execute();
        // user log
        $log = new Log( $user_name );
        $log->set_event_type('auth');
        $log->save('success profile changes');
        
    }
}
