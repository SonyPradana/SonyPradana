<?php

use Simpus\Auth\User;
use Simpus\Auth\Login;
use Simpus\Auth\Logout;
use Simpus\Auth\EmailAuth;
use Simpus\Auth\Registartion;
use Simpus\Auth\ResetPassword;
use Simpus\Auth\ForgotPassword;
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
        // logout dari server    
        new Logout( $_SESSION['token'] ?? '' );   
        #logout seesion
        unset( $_SESSION["token"] );
        exit;
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
        // no login
        $this->useGuest();

        # identifikasi form input
        $user_name = $_POST['userName'] ?? '';
        $email     = $_POST['email'] ?? '';
        $password  = $_POST['password'] ?? '';
        $confirm_password = $_POST['password2'] ?? '';
        $display_name     = $_POST['dispName'] ?? '';

        if( isset( $_POST['submit'])){
            # verifikasi user input
            $verify_user_name = sv::UserValidation($user_name, 2, 32);
            $verify_email = sv::EmailValidation($email);
            $verify_password = sv::GoodPasswordValidation($password);
            $verify_display_name = sv::NoHtmlTagValidation($display_name);   

            # esekusi jika password sama, dan form input sudah benar
            if( $password === $confirm_password &&
                $verify_user_name && 
                $verify_email &&
                $verify_password &&
                $verify_display_name ){
                
                #buat user baru
                $newUser = new Registartion( $_POST );
                $veryNewUser = $newUser->Verify( $user_name, $email );
                #cek dan simpan
                if( $veryNewUser ==  4 ){ # emapt menujukan user dan email bemul digunkan
                    if( $newUser->AddToArchive() ) { # file disimpan dipenampungan semntara 
                        echo 'data berhasil disimpan, hubungi administator untuk melakukan validasi<br>';
                        $_POST = [];
                    }
                }
            }

            # message untuk user jika form input tidak tepat
            $msg_user = $verify_user_name  ? null : 'User Name tidak diperbolehkan';
            $msg_email = $verify_email  ? null : 'format Email tidak dizinkan';
            $msg_display_name = $verify_display_name  ? null : 'Display Name tidak diperbolehkan';
            $msg_password = $verify_password  ? null : 'password terlalu lemah';
            }
        return $this->view('auth/register', [
            "input"     => [
                "userName-input"    => $msg_user ?? null,
                "email-input"       => $msg_email ?? null,
                "dispName-input"    => $msg_display_name ?? null,
                "password-input"    => $msg_password ?? null
            ],
            "contents"  => [
                "user_name"         => $user_name,
                "email"             => $email,
                "display_name"      => $display_name,
                "veryNewUser"       => $veryNewUser ?? null
            ]
        ]);
    }

    public function reset()
    {
        $this->useAuth();
        # property
        $user_name      = $this->getMiddleware()['auth']['user_name'];
        $display_name   = $this->getMiddleware()['auth']['display_name'];


        $msg = '';
        if( isset( $_POST['reset']) ){
            $p1 = $_POST['password'];
            $p2 = $_POST['password2'];
            $p3 = $_POST['password3'];

            # validasi user input
            $verify_pass = sv::GoodPasswordValidation($p2);


            if( $p2 === $p3 && $verify_pass) {
                $new_pass = new ResetPassword($user_name, $p1);

                if( $new_pass->passwordVerify() ){ 
                    $new_pass->newPassword($p2);    

                    header("Location: /logout?url=/login");    
                    exit() ;
                }else{
                    # password salah
                    $msg = 'masukan kembali password Anda';
                }
            }else{     
                # konfirmasi password salah  
                $msg = $verify_pass ? 'konirmasi password salah' : 'password terlalu lemah';
            }
        }

        // result
        return $this->view('auth/reset', [
            "contents"      => [
                "display_name"      => $display_name,
                "message"           => $msg ?? null
            ]
        ]);
    }

    public function hardReset()
    {
        $this->useGuest();
        #page ini bekerja dengan membaca url -> $_GET
        #ambil token/key/link
        $key = ( isset( $_GET['id'] ) ) ? $_GET['id'] : '';
        $code = ( isset( $_POST['validate'] ) ) ? $_POST['validate'] : '';
        if( isset( $_GET['id'] ) ){
            #cek dari form
            if( isset($_POST['reset']) 
                && isset($_POST['password']) 
                && isset($_POST['password2'])  ){
                
                $p1 = $_POST['password'];
                $p2 = $_POST['password2'];

                # verifikasi code dan password
                $verify_code = sv::NumberValidation( $code, 6, 6 );
                # verifikasi password berkulitas
                $verify_psw = sv::GoodPasswordValidation( $p1 );

                if( $p1 === $p2 && $verify_code && $verify_psw){
                    $newPassword = new ForgotPassword($key, $code);
                    $newPassword->NewPassword( $p1 );

                    # self distruction link dan code
                    $newPassword->deleteSection();
        
                    #header ke login
                    header("Location: /login");   
                    exit();                        
                }           
            }
            #ganti password 

        }else{
            # hanya yg punya id yg bisa masuk          
            header('HTTP/1.1 400 Bad Request');
            exit();
        }
        return $this->view('auth/forgot', []);
    }

    public function send()
    {
        $this->useGuest();

        # validate user input
        # 1 format email benar
        $verify_email = (isset($_POST['email'])) ? sv::EmailValidation($_POST['email']) : false;

        if( isset( $_POST['submit'] ) && $verify_email ){
            $msg = true; #pesan untuk ditampilkan

            #verifikasi keapsahan email
            $verify =  new EmailAuth($_POST['email']);
            if( $verify->UserVerify() ){
                #header ke lokasi
                $link = $verify->KeyResult();
                header("Location: /forgot/reset?id=" . $link);   
                exit();
            }
        }

        return $this->view('auth/send', [
            "contents"  => [
                "message"   => $msg ?? false
            ]
        ]);
    }
}
