<?php

use Simpus\Auth\User;
use Simpus\Auth\Login;
use Simpus\Auth\Logout;
use Simpus\Database\MyPDO;
use Simpus\Apps\Controller;
use Simpus\Helper\UploadFile;
use Simpus\Helper\StringValidation as sv;
use \Gumlet\ImageResize;

class AuthController extends Controller
{
    private function useAuth()
    {
        if( $this->getMiddleware()['auth']['login'] == false ){            
            header('HTTP/1.0 401 Unauthorized');   
            header("Location: /login?url=" . $_SERVER['REQUEST_URI']);  
            exit();
        }
    }

    private function useGuest()
    {
        if( $this->getMiddleware()['auth']['login'] ){
            header("Location: /");  
            exit();
        }
    }

    public function login()
    {
        // cek guest is require
        $this->useGuest();

        // cek request cession cek out
        $req_unset = $_GET['logout'] ?? false;
        if( $req_unset ){
            unset($_SESSION["token"]);
        }
        # cek session bane 
        $session_bane_fase = true;
        # melihat sisa bane di session max: 5. 0 atrinya sedang dibane
        $stat_bane = $_SESSION['na'] ?? 5;
        # melihat sisa waktu bane di session max: 2 menit
        $exp_bane  = $_SESSION['to'] ?? time() - 1;
        # reset timer & status bane, jika sudah melawati bane fase
        if( $exp_bane < time() && $stat_bane == 0){
            $stat_bane = 5;
            $exp_bane = time();
        }

        # cek sedang di in bane fase tidak
        if( $stat_bane == 0 || $exp_bane > time()){
            # bane fase
            $session_bane_fase = true;
        }else{
            # no in bane fase
            $session_bane_fase = false;    
        }
        # login
        $Verify_jwt = false; # default status login

        # form di isi    
        $user_name = $_POST['userName'] ?? '';
        $password  = $_POST['password'] ?? '';   

        if( isset( $_POST['login'] ) 
        && $user_name != '' 
        && $password != '' 
        ) {
            # verrifikasi user input
            // cek login menggunakan email atau user name
            if( sv::EmailValidation( $user_name ) ){
                $db = new MyPDO();
                $db->query('SELECT `user` FROM `profiles` WHERE `email`=:email');
                $db->bind(':email', $user_name);
                if( $db->single() ){
                    $user_name = $db->single()['user'];
                }
            }
            # 1. format username benar 
            $validate_user_name = sv::UserValidation($user_name, 2, 32);

            #cek dalam session bane tidak
            if( !$session_bane_fase && $validate_user_name ){
                Login::RefreshBaneFase($user_name);
                if( !Login::BaneFase($user_name) ){
                    #login sucess
                    $newLogin = new Login($user_name, $password);
                    #simpan jWT jika login benar
                    $_SESSION['token'] = $newLogin->JWTResult();
                    $Verify_jwt = $newLogin->VerifyLogin();
                }
            }
            
            # session bane logic
            if( $Verify_jwt ){
                #reset before closing
                $stat_bane = 5;
                $exp_bane = time();        
                #simpan session bane
                $_SESSION['na'] = $stat_bane;
                $_SESSION['to'] = $exp_bane;
                #redirect ke url yg dituju jika ada
                $url = $_GET['url'] ?? '/';
                header("Location: " .  $url );
                exit();
            }else{
                #refresh session bane
                #kurangin kesempaan login saat user salah password
                $stat_bane = $stat_bane < 1 ? 0 : $stat_bane - 1;
                if( $stat_bane < 1 && $exp_bane < time() ){
                    #kesempatan salah hanya ada 5x
                    $exp_bane = time() + 180;
                }
            }
        }
        #simpan session bane
        $_SESSION['na'] = $stat_bane;
        $_SESSION['to'] = $exp_bane;
        #Done
        return $this->view('auth/login', [
            "contents"   => [
                "session_bane_fase"     => $session_bane_fase,
                "stat_bane"             => $stat_bane,
                "user_name"             => $user_name,
                "validate_user_name"    => $validate_user_name ?? true,
                "exp_bane"              => $exp_bane
            ]
        ]);
    }

    public function logout()
    {
        $url = $_GET['url'] ?? '/';
        header("Location: " . $url );

        if( $this->getMiddleware()['auth']['login'] ){
            echo '<h1>logout diterima, user berhasil logout</h1>';
            #logout data base
            new Logout( $_SESSION['token'] );   
        }                
        #logout seesion
        unset($_SESSION["token"]);
    }

    public function profile()
    {
        // cek login is require
        $this->useAuth();

        // image resizer
        require_once BASEURL. '/vendor/gumlet/php-image-resize/lib/ImageResize.php';

        // logic:
        
        // mengambil data untuk input value(form)
        $user_name  = $this->getMiddleware()['auth']["user_name"];
        $user       = new User( $user_name );

        # cek form
        if( isset($_POST['submit'])){
            $url_picture    = $_POST['url-display-picture'] ?? '/data/img/display-picture/no-image.png';
            $request_upload = isset( $_FILES['display-picture']) ? true :false;

            // upload image
            $upload = new UploadFile($_FILES['display-picture']);
            $upload->setFileName( $user_name );
            $upload->setFolderLocation('/data/img/display-picture/user/');
            $upload->setMimeTypes(['image/jpg', 'image/jpeg', 'image/png']);
            $upload->setMaxFileSize( 562500 ); #450 Kb
            $upload_url = $upload->upload();
            $file_location = $request_upload && $upload->Success() ? $upload_url: $url_picture;

            # isi parameter baru
            $user->setDisplayName( $_POST['disp-name']);
            $user->setSection( $_POST['section']);
            $user->setDisplayPicture( $file_location );

            #simpan data
            $user->saveProfile();

            // delate old image
            if($upload->Success()){
                if( $upload_url != $url_picture){
                    $upload->delete($url_picture);
                }
                $small_image = str_replace($user_name, "small-" . $user_name, $url_picture);
                $upload->delete($small_image);
                $image = new ImageResize(BASEURL . $upload_url);
                $image->resizeToShortSide(24);
                $small_image = str_replace($user_name, "small-" . $user_name, $upload_url);
                $image->save(BASEURL . $small_image);
            }

            $input_img = $request_upload ? $upload->getError() : null;
        }

        // result
        return $this->view('auth/profile', [
            "validation"      => [
                "image-preview" => $input_img ?? null
            ],
            "contents"  => [
                "user_name"         => $user_name,
                "email"             => $user->getEmail(),
                "display_name"      => $user->getDisplayName(),
                "unit_kerja"        => $user->getSection(),
                "display_picture"   => $user->getDisplayPicture()
            ]
        ]);
    }

    public function register()
    {
        return $this->view('auth/register', []);
    }

    public function reset()
    {
        return $this->view('auth/reset', []);
    }

    public function hardReset()
    {
        return $this->view('auth/forgot', []);
    }

    public function send()
    {
        return $this->view('auth/send', []);
    }
}
