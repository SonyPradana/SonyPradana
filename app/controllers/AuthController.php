<?php

use Simpus\Auth\{User, Login, Logout, EmailAuth, Registartion, ResetPassword, ForgotPassword};
use System\Database\MyPDO;
use System\File\UploadFile;
use Simpus\Apps\Controller;
use Helper\String\StringValidation as sv;
use \Gumlet\ImageResize;

class AuthController extends Controller
{
    private function useAuth()
    {
        if ($this->getMiddleware()['auth']['login'] == false) {
            DefaultController::page_401(array (
                'links' => array (
                    array('Login',  '/login?url=' . $_SERVER['REQUEST_URI'])
                )
            ));
        }
    }

    private function useGuest()
    {
        if ($this->getMiddleware()['auth']['login']) {
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
                $db = MyPDO::getInstance();
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

        // logic:

        // mengambil data untuk input value(form)
        $user_name  = $this->getMiddleware()['auth']["user_name"];
        $user       = new User( $user_name );

        // valdiaation
        $validation = new GUMP('id');
        $validation->validation_rules(array (
            'disp-name' => 'required|alpha_space|min_len,4|max_len,32',
            'section' => 'required|alpha_space|between_len,2;32',
        ));
        $validation->set_fields_error_messages(array (
            'disp-name' => array (
                'required' => 'Display Name harus diisi',
                'alpha_space' => 'Tidak boleh menggunakan karakter',
                'min_len' => 'Dislpay Name terlalu pendek',
                'max_len' => 'Dislpay Name terlalu panjang',
            ),
            'section' => array (
                'require' => 'Unit kerja harus diisi',
                'alpha_space' => 'Tidak boleh menngunakn karakter',
                'between_len' => 'Unit kerja tidak tersedia'
            )
        ));
        $validation->run($_POST);
        $error = $validation->get_errors_array();

        # cek form
        if (! $validation->errors()) {
            $url_picture    = $_POST['url-display-picture'] ?? '/public/data/img/display-picture/no-image.png';
            $request_upload = isset( $_FILES['display-picture']) ? true :false;

            // upload image
            $upload = new UploadFile($_FILES['display-picture']);
            $upload->setFileName( $user_name )
                ->setFolderLocation('/public/data/img/display-picture/user/')
                ->setMimeTypes(array('image/jpg', 'image/jpeg', 'image/png'))
                ->setMaxFileSize( 562500 ); #450 Kb
            $upload_url = $upload->upload();
            $file_location = $request_upload && $upload->Success() ? $upload_url: $url_picture;

            # isi parameter baru
            $user->setDisplayName( $_POST['disp-name']);
            $user->setSection( $_POST['section']);
            $user->setDisplayPicture( $file_location );

            #simpan data
            $user->saveProfile();

            // delate old image
            if ($upload->Success()) {
                if( $upload_url != $url_picture){
                    $upload->delete($url_picture);
                }
                $small_image = str_replace($user_name, "small-" . $user_name, $url_picture);
                $upload->delete($small_image);
                $image = new ImageResize(BASEURL . $upload_url);
                $image->resizeToShortSide(24);
                $small_image = str_replace($user_name, "small-" . $user_name, $upload_url);
                $image->save(BASEURL . $small_image);
            } elseif (empty($_POST)) {
                $error = array();
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
            ],
            'error' => $error
        ]);
    }

    public function register()
    {
        // no login
        $this->useGuest();

        //  identifikasi form input
        $user_name = $_POST['userName'] ?? '';
        $email = $_POST['email'] ?? '';
        $display_name = $_POST['dispName'] ?? '';

        // validation
        $validation = new GUMP('id');
        $validation->validation_rules(
            array (
                'userName' => "required|min_len,4|max_len,16|regex,/^[A-Za-z]{1}[A-Za-z0-9]/",
                'email' => 'required|valid_email',
                'password' => 'required|min_len,8|max_len,100',
                'password2' => 'required|equalsfield,password',
                'dispName' => 'required|alpha_space|min_len,2|max_len,32'
            )
        );
        $validation->set_fields_error_messages(
            array (
                'userName' => array (
                    'min_len' => 'Username terlalu pendek',
                    'max_len' => 'Username terlalu panjang',
                    'regex' => 'Username tidak tersedia'
                ),
                'email' => array (
                    'valid_email' => 'Alamat email tidak didukung'
                ),
                'password' => array (
                    'min_len' => 'Password terlalu lemah',
                    'max_len' => 'gunakan password lain'
                ),
                'password2' => array (
                    'required' => 'Password harus diisi',
                    'equalsfield' => 'Konfirmasi password salah'
                ),
                'dispName' => array (
                    'required' => 'Display Name harus diisi',
                    'alpha_space' => 'Nama tidak boleh mengandung karakter selain huruf',
                    'min_len' => 'Display name terlalu pendek',
                    'max_len' => 'Display Name terlalu panjang'
                )
            )
        );
        $validation->run($_POST);
        $error = $validation->get_errors_array();

        //  logic
        if (! $validation->errors()) {
            // buat user baru
            $newUser = new Registartion( $_POST );
            $veryNewUser = $newUser->Verify($user_name, $email);
            // cek dan simpan (4 = username & email blm digunakan)
            if( $veryNewUser ==  4 ){
                if( $newUser->AddToArchive() ) {
                    // file disimpan dipenampungan semntara
                    echo 'data berhasil disimpan, hubungi administator untuk melakukan validasi<br>';
                    // reset data
                    $_POST = [];
                    $user_name = $email = $display_name = null;
                }
            }
        } elseif (empty($_POST)) {
            $error = array();
        }

        $veryNewUser = $veryNewUser ?? 4;
        $veryNewUser = $veryNewUser == 4 ? null : $veryNewUser;

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
                "veryNewUser"       => $veryNewUser
            ],
            'error' => $error
        ]);
    }

    public function reset()
    {
        $this->useAuth();
        # property
        $user_name      = $this->getMiddleware()['auth']['user_name'];
        $display_name   = $this->getMiddleware()['auth']['display_name'];

        // validation
        $validation = new GUMP('id');
        $validation->validation_rules(
            array (
                'password' => 'required|min_len,8|max_len,100',
                'password2' => 'required|min_len,8|max_len,100',
                'password3' => 'required|equalsfield,password2',
            )
        );
        $validation->set_fields_error_messages(
            array (
                'password' => array (
                    'between_len' => 'Password tidak tepat'
                ),
                'password2' => array (
                    'required' => 'Password baru  wajib terisi',
                    'min_len' => 'Password terlalu lemah',
                    'max_len' => 'gunakan password lain'
                ),
                'password3' => array (
                    'required' => 'Password baru harus terisi',
                    'equalsfield' => 'Konfirmasi password salah'
                )
            )
        );
        $validation->run($_POST);
        $error = $validation->get_errors_array();

        $msg = '';

        if (! $validation->errors()) {
            $p1 = $_POST['password'];
            $p2 = $_POST['password2'];
            $new_pass = new ResetPassword($user_name, $p1);

            if ($new_pass->passwordVerify()) {
                $new_pass->newPassword($p2);

                header("Location: /logout?url=/login");
                exit() ;
            } else {
                # password salah
                $msg = 'masukan kembali password Anda';
                $error = array('password' => 'Password Anda salah');
            }
        } elseif (empty($_POST)) {
            $error = array();
        } else {
            # konfirmasi password salah
            $msg = 'konirmasi password salah / password terlalu lemah';
        }

        // result
        return $this->view('auth/reset', [
            "contents"      => [
                "display_name"      => $display_name,
                "message"           => $msg ?? null
            ],
            'error' => $error
        ]);
    }

    public function hardReset()
    {
        $this->useGuest();

        // validation
        $validation = new GUMP('id');
        $validation->validation_rules(array (
            'id' => 'required',
            'validate' => 'required|exact_len,6|numeric',
            'password' => 'required|between_len,8;100',
            'password2' => 'required|equalsfield,password'
        ));
        $validation->set_fields_error_messages(
            array (
                'id' => array('required' => 'Link tidak berlaku'),
                'validate' => array (
                    'required' => 'Validasi code harus diisi',
                    'exact_len' => 'Validasi code tidak valid',
                    'numeric' => 'Validasi code tidak valid'
                ),
                'password' => array (
                    'between_len' => 'password terlalu lemah'
                ),
                'password2' => array (
                    'required' => 'Password baru harus diisi',
                    'equalsfield' => 'Konfirmasi password salah'
                )
            )
        );
        $validation->run(array_merge($_POST, $_GET));
        $error = $validation->get_errors_array();

        if (isset($_GET['id'])) {
            if (! $validation->errors()) {
                $key = $_GET['id'];
                $code = $_POST['validate'];

                $newPassword = new ForgotPassword($key, $code);
                $newPassword->NewPassword($_POST['password']);

                # self distruction link dan code
                $newPassword->deleteSection();

                #header ke login
                header("Location: /login");
                exit();
            } elseif (empty($_POST)) {
                $error = array();
            }
        } else {
            # hanya yg punya id yg bisa masuk
            header('HTTP/1.1 400 Bad Request');
            echo '<h1>Halaman tidak diizinkan</h1>';
            exit();
        }
        return $this->view('auth/forgot', array (
            'error' => $error
        ));
    }

    public function send()
    {
        $this->useGuest();

        // validation
        $validation = new GUMP('id');
        $validation->validation_rules(array (
            'email' => 'required|valid_email'
        ));
        $validation->run($_POST);
        $error = $validation->errors();

        if (! $validation->errors()) {
            $msg = true; #pesan untuk ditampilkan

            #verifikasi keapsahan email
            $verify =  new EmailAuth($_POST['email']);
            if ($verify->UserVerify()) {
                #header ke lokasi
                $link = $verify->KeyResult();
                header("Location: /forgot/reset?id=" . $link);
                exit();
            }
        } elseif (empty($_POST)) {
            $error = array();
        }

        return $this->view('auth/send', [
            "contents"  => [
                "message"   => $msg ?? false
            ],
            'error' => $error
        ]);
    }
}
