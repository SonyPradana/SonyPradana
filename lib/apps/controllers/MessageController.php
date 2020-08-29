<?php

class MessageController extends Controller{
    public function __construct(){        
      call_user_func_array($this->getMiddleware()['before'], []);
    }
    
    public function public(){
        $portal = [
            "auth"    => $this->getMiddleware()['auth'],
            "meta"     => [
                "title"         => "Pesan Masuk",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, baca pesan, public message, kotak saran, pesan"
            ],
            "header"   => [
                "active_menu"   => 'home',
                "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
            ],
            "contents" => []            
        ];

        return $this->view('message/public', $portal);
    }
}
