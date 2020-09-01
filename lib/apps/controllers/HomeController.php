<?php

use Simpus\Simpus\MedicalRecords;
use Simpus\Database\MyPDO;
use Simpus\Apps\Controller;

class HomeController extends Controller{
    public function index(){
        $data_rm = new MedicalRecords();    
        $jumlah_rm = $data_rm->maxData();
        
        # jadwal pelayana
        $jadwal = [
            1 => ["day" => "Senin", "time" => "08:00 AM-12:00 AM"],
            2 => ["day" => "Selasa", "time" => "08:00 AM-12:00 AM"],
            3 => ["day" => "Rabu", "time" => "08:00 AM-12:00 AM"],
            4 => ["day" => "Kamis", "time" => "08:00 AM-12:00 AM"],
            5 => ["day" => "Jumat", "time" => "08:00 AM-10:30 AM"],
            6 => ["day" => "Sabtu", "time" => "08:00 AM-11:00 AM"],
            7 => ["day" => "Minggu", "time" => "Tutup"],
        ];
        $n = date('N');
        for($i = $n; $i <= 7; $i++) { 
            $sort_day[$i] = $jadwal[$i];
        }
        for ($i = 1; $i < $n ; $i++) { 
            $sort_day[$i] = $jadwal[$i];
        }

        // result
        return $this->view('home/index', [
            "auth"    => $this->getMiddleware()['auth'],
            "meta"     => [
                "title"         => "SIMPUS Lerep",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'home',
                "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
            ],
            "contents" => [
                "jumlah_rm"     => (int) $jumlah_rm,
                "jadwal"        => $jadwal,
                "jadwal_sort"   => $sort_day
            ]
        ]);
    }

    public function about(){        
        $db = new MyPDO();
        $db->query('SELECT `id`, `date`, `note`, `ver` FROM `version`  ORDER BY `version`.`id` ASC');

        // result
        return $this->view('home/about', [
            "auth"    => $this->getMiddleware()['auth'],
            "meta"     => [
                "title"         => "Tentang Kami",
                "discription"   => "Tentang kami",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, about us, about"
            ],
            "header"   => [
                "active_menu"   => 'home',
                "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
            ],
            "contents" => [
                "time_line"     => $db->resultset()
            ]
        ]);
    }
}
