<?php
/**
 * class ini berfungsi untuk mendaptakan informsai akun / profil dari user
 * 
 * @author sonypradana@gmail.com
 */
class User{
    /** @var boolean cek user ada atau tidak */
    private $_exisUser = false;
    /** @var string */
    private $_displayName, $_email;

    /** @return boolean getter */
    public function userVerify(){
        return $this->_exisUser;
    }
    /** @return string getter*/
    public function getDisplayName(){
        return $this->_displayName;
    }
    /** @return string getter */
    public function getEmail(){
        return $this->_email;
    }

    /**
     * mencari data denagn user name yang sesaui
     * 
     * @param string $user_name User Name yang digunakna
     */
    public function __construct($user_name){
        // senetalizer user_name
        $user_name = strtolower($user_name);

        # koneksi data base
        $conn = new DbConfig();
        $link  = $conn->StartConnection();
        $query = mysqli_query($link, "SELECT * FROM profiles WHERE user = '$user_name' ");
        if( mysqli_num_rows( $query ) == 1 )  {
            $row = mysqli_fetch_assoc( $query );
            # simpan paramter untuk ditampilkan
            $this->_exisUser = true;
            $this->_email = $row['email'];
            $this->_displayName = $row['display_name'];
        }
    }
}
