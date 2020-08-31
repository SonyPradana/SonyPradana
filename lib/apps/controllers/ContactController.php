<?php

use Simpus\Database\MyPDO;
use Simpus\Apps\Controller;
use Simpus\Helper\MathCaptcha;
use Simpus\Message\ContactUs;

class ContactController extends Controller{
    public function contactUs(){
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];
        // previews captcha
        $cek_captcha = $_SESSION['MathCaptcha_ContactUs'] ?? null;
        
        $sender      = $_POST['mail'] ?? null;
        $message     = $_POST['message'] ?? null;
        $regarding   = $_POST['regarding'] ?? null;
        $captcha     = $_POST['ampcaptcha'] ?? null;

        if( $sender != null &&
        $message != null &&
        $regarding != null &&
        $captcha == $cek_captcha){
            // send message
            $send_message = new ContactUs($sender, $message, $regarding);
            if( $send_message->kirimPesan() ){
                $msg = ["show" => true, "type" => 'success', "content" => 'Trimakasih atas dukungan Anda :)'];
            }
        }elseif( $captcha != $cek_captcha && $captcha != null ){
            $msg = ["show" => true, "type" => 'danger', "content" => 'Captcha salah, silahkan ulangai kembali'];
        }
        // new captcha dibuat
        $new_captcha = new MathCaptcha();
        $_SESSION['MathCaptcha_ContactUs'] = $new_captcha->ChaptaResult();

        // result        
        return $this->view('contact/contactUs', [            
            "auth"    => $this->getMiddleware()['auth'],
            "meta"     => [
                "title"         => "Hubungi Kami",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, hubungi kami, contact us, kritik dan saran, masukan pasien, review pasien"
            ],
            "header"   => [
                "active_menu"   => 'home',
                "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
            ],
            "contents" => [
                "captcha_quest" => $new_captcha->ChaptaQuest()
            ],
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }
    public function ourTeam(){
        $db = new MyPDO();
        $db->query('SELECT `display_name`, `section`, `display_picture` FROM `profiles`');
        
        // result
        return $this->view('contact/ourTeam', [
            "auth"    => $this->getMiddleware()['auth'],
            "meta"     => [
                "title"         => "Tim Kami",
                "discription"   => "profile pegawai/admin simpus lerep",
                "keywords"      => "simpus lerep, Profile Pegawai, Profile, Author, Teams, Our Teams, Members, Admin, Pegawai"
            ],
            "header"   => [
                "active_menu"   => 'home',
                "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
            ],
            "contents" => [
                "profiles_card" => $db->resultset()
            ]
        ]);
    }
}
