<?php

use Simpus\Simpus\{MedicalRecord, MedicalRecords};
use Simpus\Auth\Log;
use Simpus\Apps\Controller;

class RekamMedisController extends Controller{

    public function __construct(){
        if( isset( $_GET['active_menu'] ) ){
            $_SESSION['active_menu'] = MENU_MEDREC;
        }
        //  WARNING:    fungsi ini adalah funsi authrization, wajib ada
        
        // call_user_func_array($this->getMiddleware()['before'], []);
        if( $this->getMiddleware()['auth']['login'] == false ){            
            header('HTTP/1.0 401 Unauthorized');   
            header("Location: /login?url=" . $_SERVER['REQUEST_URI']);  
            exit();
        }
    }

    public function index(){
        $rm = new MedicalRecords();

        // mengambil data jumlah rm bedasarkan desa
        $arr_desa = ['bandarjo', 'branjang', 'kalisidi', 'keji', 'lerep', 'nyatnyono'];
        $arr_data = [];
        $jumlah_rm = $rm->maxData();
        foreach( $arr_desa as $desa ){
            $rm->filterByAlamat($desa);
            $arr_data[$desa] = $rm->maxData();
        }

        // mengambil data jumlah rm berdasarkan range umur
        $rm->reset();
        $arr_umur  = ["0-5", "5-16", "17-25", "26-45", "46-65", "65-100"];
        $arr_data2 = [];
        foreach( $arr_umur as $umur ){        
            $min_max = explode("-", $umur);
            $min =  $min_max[0];
            $max =  $min_max[1];        
            $min  = date("Y-m-d", time() - ($min * 31536000) );
            $max  = date("Y-m-d", time() - ($max * 31536000) );
            $rm->filterRangeTanggalLahir($min, $max);
            $arr_data2[$umur] = $rm->maxData();
        }

        return $this->view('rekam-medis/index', [
            "auth"    => $this->getMiddleware()['auth'],
            "meta"     => [
                "title"         => "Dashbord Rekam Medis",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'home',
                "header_menu"   => MENU_MEDREC
            ],
            "contents" => [
                "jumlah_rm"     => (int) $jumlah_rm,
                "arr_data"      => (array) $arr_data,
                "arr_data2"     => (array) $arr_data2
            ]
        ]);
    }

    public function show($view_file){
        $view_file = $view_file == 'view' ? 'view_' : $view_file;  // alias untuk page 'view'
        call_user_func([$this, $view_file]);
    }

    /* view page di panggil secara manual, keculai index.php */
    

    public function edit()
    {
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];
        $error = array();
        
        // ambil id dari url jika tidak ada akes ditolak
        if (isset($_GET['document_id'])) {
            // ambil data rm menggunakn  id
            $id = $_GET['document_id'];
            // default property
            $status_kk = $status_double = false;    

            // validasi data
            $validation = new GUMP('id');
            $validation->validation_rules(array (
                'nomor_rm' => 'required|numeric|max_len,6',
                'nama' => 'required|alpha_space|min_len,4|max_len,32',
                'tgl_lahir' => 'date,Y-m-d',
                'alamat' => 'alpha_space|max_len,20',
                'nomor_rt' => 'numeric|max_len,2',
                'nomor_rw' => 'numeric|max_len,2',
                'nama_kk' => 'alpha_space|min_len,4|max_len,32',
                'nomor_rm_kk' => 'numeric|max_len,6',
            ));
            $validation->filter_rules(array (
                'nama' => 'trim|htmlencode',
                'alamat' => 'trim|htmlencode',
                'nama_kk' => 'trim|htmlencode'
            ));
            $validation->run($_POST);
            $error = $validation->get_errors_array();

            if (! $validation->errors()) { 
                //  menjegah duplikasi data saaat merefresh page
                $last_data = $_SESSION['last_data'] ?? [];
        
                // kita anggap semua field form sudah benar
                $new_rm = MedicalRecord::withId($id);
                $new_rm->setNomorRM( $_POST['nomor_rm'] );
                $new_rm->setDataDibuat( time() );
                $new_rm->setNama( $_POST['nama'] );
                $new_rm->setTanggalLahir( $_POST['tgl_lahir'] );
                $new_rm->setAlamat( $_POST['alamat'] );
                $new_rm->setNomorRt( $_POST['nomor_rt'] );
                $new_rm->setNomorRw( $_POST['nomor_rw'] );
                // opsonal
                $new_rm->setNamaKK( $_POST['nama_kk'] );
                $new_rm->setNomorRM_KK( $_POST['nomor_rm_kk'] );
        
                //simpan data
                $simpan = $new_rm->save();
                if( $simpan && $last_data != $_POST){                
                    $msg = ["show" => true, "type" => 'success', "content" => 'berhasil diupdate'];
                    $_SESSION['last_data'] = $_POST;
                } else{                
                    $msg = ["show" => true, "type" => 'danger', "content" => 'gagal disimpan'];
                }    
                
                // user log
                $log = new Log( $this->getMiddleware()['auth']['user_name'] );
                $log->set_event_type('med-rec');
                $log->save( $new_rm->getLastQuery() );
            
            } elseif (empty($_POST)) {
                $error = array();
            } 

            // memuat data dari data base
            $load_rm = MedicalRecord::withId($id);
            // persipan data untuk ditampilkan
            $nomorRM = $load_rm->getNomorRM();
            $nama = $load_rm->getNama();
            $tanggalLahir = $load_rm->getTangalLahir();
            $alamat = $load_rm->getAlamat();
            $nomorRt = $load_rm->getNomorRt();
            $nomorRw = $load_rm->getNomorRw();
            $namaKK = $load_rm->getNamaKK();
            $nomorRM_KK = $load_rm->getNomorRM_KK();
            // cek status kk
            if( $nama === $namaKK){
                $status_kk = true;
            }
            // cari rm yang sama
            $cari_rm = new MedicalRecords();
            $cari_rm->filterByNomorRm($nomorRM);
            $cari_rm->forceLimitView(2);
            if( $cari_rm->maxData() > 1){
                $status_double = true;
            }
            // cek data rm terdaftar atau tidak
            if( $load_rm->cekAxis() == false){                        
                echo 'acces deny!!!';
                header('HTTP/1.1 403 Forbidden');
                exit;
            }
        } else {
            echo 'acces deny!!!';
            exit;
        }

        return $this->view('rekam-medis/edit', [
            "auth"          => $this->getMiddleware()['auth'],
            "DNT"           => $this->getMiddleware()['DNT'],
            "redirect_to"   => $_GET['redirect_to'] ?? '/',
            "meta"          => [
                "title"         => "Edit Data Rekam Medis",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"        => [
                "active_menu"   => 'null',
                "header_menu"   => MENU_MEDREC
            ],
            "contents" => [
                "status_kk"         => $status_kk,
                "status_double"     => $status_double,
                "nomor_rm"          => $nomorRM,
                "nama"              => $nama,
                "tanggal_lahir"     => $tanggalLahir,
                "alamat"            => $alamat,
                "nomor_rt"          => $nomorRt,
                "nomor_rw"          => $nomorRw,
                "nama_kk"           => $namaKK,
                "nomor_rm_kk"       => $nomorRM_KK
            ],
            'error' => $error,
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }

    public function new()
    {
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];
        // property
        $nomor_rm    = $_POST['nomor_rm'] ?? '';
        $nama        = $_POST['nama'] ?? '';
        $tgl_lahir   = $_POST['tgl_lahir'] ?? '';
        $alamat      = $_POST['alamat'] ?? '';
        $nomor_rt    = $_POST['nomor_rt'] ?? '';
        $nomor_rw    = $_POST['nomor_rw'] ?? '';
        $nama_kk     = $_POST['nama_kk'] ?? '';
        $nomor_rm_kk = $_POST['nomor_rm_kk'] ?? '';

        // validasi data
        $validation = new GUMP('id');
        $validation->validation_rules(array (
            'nomor_rm' => 'required|numeric',
            'nama' => 'required|alpha_space|min_len,4|max_len,32',
            'tgl_lahir' => 'date,Y-m-d',
            'alamat' => 'alpha_space|max_len,20',
            'nomor_rt' => 'numeric|max_len,2',
            'nomor_rw' => 'numeric|max_len,2',
            'nama_kk' => 'alpha_space|min_len,4|max_len,32',
            'nomor_rm_kk' => 'numeric',
        ));
        $validation->filter_rules(array (
            'nama' => 'trim|htmlencode',
            'alamat' => 'trim|htmlencode',
            'nama_kk' => 'trim|htmlencode'
        ));
        $validation->run($_POST);
        $error = $validation->get_errors_array();


        // ambil nomor rm terakhir
        $data = new MedicalRecords();
        $data->limitView(1);
        $data->sortUsing('nomor_rm');
        $data->orderUsing("DESC");
        $last_nomor_rm = $data->resultAll()[0]['nomor_rm'];

        if (! $validation->errors()) {
            // menjegah duplikasi data saat form direfresh
            $last_data = $_SESSION['last_data'] ?? [];

            // kita anggap semua field form sudah benar
            $new_rm = new MedicalRecord();
            $new_rm ->setNomorRM( $nomor_rm )
                ->setDataDibuat( time() )
                ->setNama( $nama )
                ->setTanggalLahir( $tgl_lahir )
                ->setAlamat( $alamat )
                ->setNomorRt( $nomor_rt )
                ->setNomorRw( $nomor_rw )
                ->setNamaKK( $nama_kk )
                ->setNomorRM_KK( $nomor_rm_kk );

            //simpan data
            $simpan = $new_rm->insertNewOne();
            if ($simpan && $last_data != $_POST) {
                $msg = ["show" => true, "type" => 'success', "content" => 'berhasil disimpan'];
                $_SESSION['last_data'] = $_POST;
                $_POST = [];
                $nomor_rm = $_POST['nomor_rm'] ?? '';
                $nama = $tgl_lahir = $alamat = $nomor_rt = $nomor_rw = $nama_kk = $nomor_rm_kk = null;
            } else {
                $msg = ["show" => true, "type" => 'danger', "content" => 'Gagal disimpan'];
            }

            // merefrresh nomor rm terakhir saad form dikirim
            $data = new MedicalRecords( );
            $data->forceLimitView(1);
            $data->sortUsing('nomor_rm');
            $data->orderUsing("DESC");
            $last_nomor_rm = $data->resultAll()[0]['nomor_rm'];
        } elseif (empty($_POST)) {
            $error = array();
        }
        
        // result
        return $this->view('rekam-medis/new', [
            "auth"     => $this->getMiddleware()['auth'],
            "DNT"      => $this->getMiddleware()['DNT'],
            "meta"     => [
                "title"         => "Buat Rekam Medis Baru",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'Buat RM',
                "header_menu"   => MENU_MEDREC
            ],
            "contents" => [
                "nomor_rm"      => $nomor_rm,
                "nama"          => $nama,
                "tgl_lahir"     => $tgl_lahir,
                "alamat"        => $alamat,
                "nomor_rt"      => $nomor_rt,
                "nomor_rw"      => $nomor_rw,
                "nama_kk"       => $nama_kk,
                "nomor_rm_kk"   => $nomor_rm_kk,

                "last_nomor_rm" => $last_nomor_rm
            ],
            'error' => $error,
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }

    public function search(){
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];   
        // ambil parameter dari url
        $main_search     = $_GET['main-search'] ?? '';
        $nomor_rm_search = $_GET['nomor-rm-search'] ?? '';
        $alamat_search   = $_GET['alamat-search'] ?? '';
        $no_rt_search    = $_GET['no-rt-search'] ?? '';
        $no_rw_search    = $_GET['no-rw-search'] ?? '';
        $nama_kk_search  = $_GET['nama-kk-search'] ?? '';
        $no_rm_kk_search = $_GET['no-rm-kk-search'] ?? '';
        $strict_search   = isset( $_GET['strict-search'] ) ? true : false;   

        return $this->view('rekam-medis/search', [
            "auth"     => $this->getMiddleware()['auth'],
            "DNT"      => $this->getMiddleware()['DNT'],
            "meta"     => [
                "title"         => "Cari Data Rekam Medis",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'Cari RM',
                "header_menu"   => MENU_MEDREC
            ],
            "contents" => [
                "nama"          => $main_search,
                "nomor_rm"      => $nomor_rm_search,
                "alamat"        => $alamat_search,
                "nomor_rt"      => $no_rt_search,
                "nomor_rw"      => $no_rw_search,
                "nama_kk"       => $nama_kk_search,
                "nomor_rm_kk"   => $no_rm_kk_search,
                "strict"        => $strict_search

            ],
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }

    public function view_(){
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];
        
        // config
        $sort = 'nomor_rm';
        $order = 'ASC';
        $page = $_GET['page'] ?? 1;
        $page = is_numeric($page) ? $page : 1;

        // ambil data
        $show_data = new MedicalRecords();
        $show_data->sortUsing($sort);
        $show_data->orderUsing($order);
        $show_data->limitView(25);
        $max_page = $show_data->maxPage();
        $page = $page > $max_page ? $max_page : $page;
        $show_data->currentPage($page);
        $get_data = $show_data->resultAll();

        return $this->view('rekam-medis/view',[            
            "auth"      => $this->getMiddleware()['auth'],
            "DNT"       => $this->getMiddleware()['DNT'],
            "meta"      => [
                "title"         => "Lihat Data Rekam Medis",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"    => [
                "active_menu"   => 'Lihat RM',
                "header_menu"   => MENU_MEDREC
            ],
            "contents"  => [
                "page"      => (int) $page,
                "max_page"  => (int) $max_page,
                "data_rm"   => $get_data
            ],
            "message"   => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }

    public function profile(){
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];

        $profile_id = $_GET["useid"] ?? rand(4, 100);
        $profile    = MedicalRecord::withId( $profile_id );

        return $this->view('rekam-medis/profile',[            
            "auth"      => $this->getMiddleware()['auth'],
            "DNT"       => $this->getMiddleware()['DNT'],
            "meta"      => [
                "title"         => "Profile Pasien - SimpusLerep",
                "discription"   => "Profile data dan biodata pasien",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"    => [
                "active_menu"   => 'Profile',
                "header_menu"   => MENU_MEDREC
            ],
            "contents"  => [
                "profile_id"        => $profile_id,
                "nama"              => ucwords( $profile->getNama() ),
                "nomor_rm"          => $profile->getNomorRM(),
                "tanggal_lahir"     => $profile->getTangalLahir(),
                "alamat_lengkap"    => ucwords( $profile->getAlamatLengkap() ),
                "nama_kk"           => ucwords( $profile->getNamaKK() ),
                "grup"          => json_decode($profile->getStatus(), true)['grup']

            ],
            "message"   => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }
}
