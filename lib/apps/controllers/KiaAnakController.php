<?php

use Simpus\Database\MyPDO;
use Simpus\Apps\Controller;
use Simpus\Helper\ConvertCode;
use Simpus\Simpus\{Relation, MedicalRecord};
use Simpus\Simpus\{KIAAnakRecord, KIAAnakRecords};
use Simpus\Simpus\{GroupsPosyandu, PosyanduRecord, PosyanduRecords};

class KiaAnakController extends Controller{

    public function __construct()
    {
        //  WARNING:    fungsi ini adalah funsi authrization, wajib ada
        
        // call_user_func_array($this->getMiddleware()['before'], []);
        if( $this->getMiddleware()['auth']['login'] == false ){            
            header('HTTP/1.0 401 Unauthorized');   
            header("Location: /login?url=" . $_SERVER['REQUEST_URI']);  
            exit();
        }        
    }

    public function show($action, $unit)
    {
        if( isset( $_GET['active_menu'] )) {
            $_SESSION['active_menu'] = $unit == 'biodata' ? MENU_KIA_ANAK : MENU_POSYANDU;
        }
        $method = $action . '_' . $unit;
        call_user_func([$this, $method], []);
    }

    // private function:

    private function deny_access(string $message = '')
    {
        header('HTTP/1.0 405 Method Not Allowed');
        echo '<h1>Acces deny!!!</h1>';
        echo '<p>' . $message . '</p>';
        exit();
    }
    
    // biodata 

    private function edit_biodata()
    {
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];

        // validaation
        $validation = new GUMP('id');
        $validation->validation_rules(array (
            'jenis_kelamin' => 'required|numeric',
            'bbl' => 'numeric|max_len,4',
            'pbl' => 'numeric|max_len,4',
            'kia' => 'numeric|max_len,4',
            'imd' => 'numeric|max_len,4',
            'grups_posyandu' => 'alpha_space'
        ));
        $validation->run($_POST);
        $error = $validation->get_errors_array();

        $success   = false;
        $code_hash = $_GET['document_id'] ?? false;
        if( $code_hash == false ) $this->deny_access();
        $get_code_hash = Relation::where('id_hash', $code_hash);
        $id_hash     = $get_code_hash[0]['time_stamp'] ?? false;
        if( $id_hash == false ) $this->deny_access();

        // ambil data lama
        $data_rm = new MedicalRecord();                             // data rm
        if($data_rm->refreshUsingIdHash( $id_hash ) == false) $this->deny_access('document tidak ditemukan');

        $data_kia = new KIAAnakRecord();                            // data kia
        $data_kia->loadWithID( $code_hash );
        if( $data_kia->cekExist() == false ) $this->deny_access('documnet tidak terdaftar');    

        // update data
        if (! $validation->errors()) {
            $undo_change = array_merge(
                $data_rm->getData(),
                $data_kia->convertToArray()
            );
            // simpan data kia
            $data_kia->convertFromArray($_POST);
            $success = $data_kia->update();

            // simpdan data rm
            $data_rm->convertFromArray($_POST);
            $status           = (array) json_decode( $data_rm->getStatus() );
            $status["update"] = time();
            $data_rm->setStatus( json_encode($status) );              

            if( $success ){
                // switch data disimpan dimana                      TODO cek asal table, langsung dr database-nya menggunakn id
                if( $data_rm->getNomorRM() == ''){
                    $success = $data_rm->save('staging_rm');
                }else{
                    $success = $data_rm->save();
                }
                $msg = ["show" => true, "type" => 'success', "content" => 'Berhasil disimpan'];
            }
            if( !$success ){
                // mengembalikan data apabila data tidak berhasil disimpan 
                $data_kia->convertFromArray($undo_change);
                $data_kia->update();                                // [database-kia]
                $data_rm->convertFromArray($undo_change);           // [user input]
            }
        } elseif (empty($_POST)) {
            $error = array();
        } else {
            $msg = ["show" => true, "type" => 'danger', "content" => 'Gagal disimpan'];
        }

        return $this->view('kia-anak/biodata/edit', [
            "auth"    => $this->getMiddleware()['auth'],
            "meta"     => [
                "title"         => "Edit Data KIA Anak",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'Edit Biodata',
                "header_menu"   => MENU_KIA_ANAK
            ],
            "contents" => [
                "success"           => $success,       
                "nama"              => $data_rm->getNama(),
                "tanggal_lahir"     => $data_rm->getTangalLahir(),
                "alamat"            => $data_rm->getAlamat(),
                "nomor_rt"          => $data_rm->getNomorRt(),
                "nomor_rw"          => $data_rm->getNomorRw(),
                "nama_kk"           => $data_rm->getNamaKK(),
                "jenis_kelamin"     => $data_kia->getJenisKelamin(),
                "bbl"               => $data_kia->getBeratBayiLahir(),
                "pbl"               => $data_kia->getPanjangBayiLahir(),
                "kia"               => $data_kia->getKIA(),
                "imt"                => $data_kia->getIndeksMasaTubuh(),
                "asi"               => $data_kia->getAsiEks(),
            ],
            'error' => $error,
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }

    private function new_biodata()
    {
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];
        $success = false;

        // validation
        $validation = new GUMP('id');
        $validation->validation_rules(array (
            'target' => 'required',
            'nama_rm' => 'required|alpha_space|min_len,4|max_len,32',
            'tanggal_lahir' => 'date,Y-m-d',
            'alamat' => 'alpha_space|max_len,20',
            'nomor_rt' => 'numeric|max_len,2',
            'nomor_rw' => 'numeric|max_len,2',
            'nama_kk' => 'alpha_space|min_len,4|max_len,32',
            'jenis_kelamin' => 'required|numeric|max_len,1',
            'bbl' => 'numeric|max_len,4',
            'pbl' => 'numeric|max_len,4',
            'kia' => 'numeric|max_len,4',
            'imd' => 'numeric|max_len,4',
            'grups_posyandu' => 'alpha_space'
        ));
        $validation->filter_rules(array (
            'nama_rm' => 'trim|htmlencode',
            'alamat' => 'trim|htmlencode',
            'nama_kk' => 'trim|htmlencode'
        ));
        $validation->run($_POST);
        $error = $validation->get_errors_array();

        // simpan data
        if (! $validation->errors()) {
            // default
            $id_hash = time();
            $success_stagingRM = false;
            $success_dataKIA = false;

            if( $_POST['target']  == 'staging' ){
                // request data rm baru
                $stagingRM = new MedicalRecord();
                $stagingRM->setNama( $_POST['nama_rm'] ?? 'bayi nyonya Y' )
                    ->setTanggalLahir( $_POST['tanggal_lahir'] ?? date("M/d/Y", time()) )
                    ->setAlamat( $_POST['alamat'] ?? '')
                    ->setNomorRt( $_POST['nomor_rt'] ?? 0 )
                    ->setNomorRw( $_POST['nomor_rw'] ?? 0 )
                    ->setNamaKK( $_POST['nama_kk'] ?? 'tuan x')
                    ->setStatus('{"grub":["posyandu", "kia-anak"]}')
                    ->setDataDibuat( $id_hash );
                // simpan data ke staging data rm
                $success_stagingRM = $stagingRM->insertNewOne('', 'staging_rm');

                // var_dump($stagingRM->getData());
            } elseif( $_POST['target'] == 'data_rm' && isset($_POST['timestamp'])) {
                $success_stagingRM = true;
                $id_hash = $_POST['timestamp'];
            }

            // cek data rm ada atau tidak
            if ($success_stagingRM) {
                // request data posyandu baru
                $dataKIA = new KIAAnakRecord();
                $dataKIA->convertFromArray( $_POST );
                // table relation
                $table_relation = new Relation(ConvertCode::ConvertToCode( $id_hash ), $id_hash);
                // simpan data biodata kia
                $success_dataKIA = $dataKIA->creat( ConvertCode::ConvertToCode( $id_hash ) );

                if ($success_dataKIA && $table_relation->creat()) {
                    $success = true;
                    $msg = ["show" => true, "type" => 'success', "content" => 'Berhasil disimpan'];
                }
            }
        } elseif (empty($_POST)) {
            $error = array();
        } else {
            $msg = ["show" => true, "type" => 'success', "content" => 'Gagal disimpan'];            
        }

        return $this->view('kia-anak/biodata/new',[
            "auth"    => $this->getMiddleware()['auth'],
            "meta"     => [
                "title"         => "Biodata Baru - KIA Anak",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'Buat Data KIA',
                "header_menu"   => MENU_KIA_ANAK
            ],
            "contents" => [
                "success"           => $success
            ],
            'error' => $error,
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }

    private function search_biodata()
    {
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];
        
        # ambil parameter dari url
        $main_search     = $_GET['main-search'] ?? '';
        $alamat_search   = $_GET['alamat-search'] ?? '';
        $no_rt_search    = $_GET['no-rt-search'] ?? '';
        $no_rw_search    = $_GET['no-rw-search'] ?? '';
        $nama_kk_search  = $_GET['nama-kk-search'] ?? '';
        $strict_search   = isset( $_GET['strict-search'] ) ? true : false;
        $desa            = $_GET['desa'] ?? null;
        $id_posyandu     = $_GET['tempat_pemeriksaan'] ?? null;

        if( $desa != null){            
            $groups_posyandu = GroupsPosyandu::getPosyandu($desa);
        }

        if( $id_posyandu != null && is_numeric($id_posyandu)){
            $posyandu = GroupsPosyandu::getPosyanduName($id_posyandu);        
        }

        // result
        return $this->view('kia-anak/biodata/search',[
            "auth"    => $this->getMiddleware()['auth'],
            "DNT"      => $this->getMiddleware()['DNT'],
            "meta"     => [
                "title"         => "Cari Data KIA Anak",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'Cari Data KIA',
                "header_menu"   => MENU_KIA_ANAK
            ],
            "contents" => [
                "nama"              => $main_search,
                "alamat"            => $alamat_search,
                "nomor_rt"          => $no_rt_search,
                "nomor_rw"          => $no_rw_search,
                "nama_kk"           => $nama_kk_search,
                "strict"            => $strict_search,
                "desa"              => $desa,
                "id_posyandu"       => $id_posyandu,
                "groups_posyandu"   => $groups_posyandu ?? null,
                "posyandu"          => $posyandu ?? null,
                "posyandu_exist"    => isset( $posyandu ) && isset( $groups_posyandu ) ? true : false
            ],
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }

    private function view_biodata()
    {
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];
        
        // load data rm dan staging yang terdaftar kia anak
        $data_kia = new KIAAnakRecords();
        $datas = $data_kia->resultAll();
        
        // result
        return $this->view('kia-anak/biodata/view',[
            "auth"    => $this->getMiddleware()['auth'],
            "DNT"      => $this->getMiddleware()['DNT'],
            "meta"     => [
                "title"         => "Lihat Data KIA Anak",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'Lihat Data KIA',
                "header_menu"   => MENU_KIA_ANAK
            ],
            "contents" => [
                "data_kia"      => $datas
            ],
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }

    // posyandu

    private function edit_posyandu()
    {
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];

        // validation
        $validation = new GUMP('id');
        $validation->validation_rules(array (
            'tempat_pemeriksaan' => 'required|numeric|max_len,2',
            'tanggal_pemeriksaan' => 'required|date,Y-m-d',
            'tinggi_badan' => 'required|numeric|max_len,4',
            'berat_badan' => 'required|numeric|max_len,4',
        ));
        $validation->run($_POST);
        $error = $validation->get_errors_array();

        $document_id = $_GET['document_id'] ?? $this->deny_access();
        $params      = explode('-', $document_id);                                // [0]: code_hash, [1]: id 
        //  
        $code_hash  = $params[0];                                                // validasi document id type, harus angka
        $id         = $params[1] ?? $this->deny_access();
        // 
        $posyandu   = new PosyanduRecord($code_hash);
        $isValided  = $posyandu->IsValided();
        $read       = $posyandu->read( $id );
        
        if (! $validation->errors() && $isValided && $read) {
            // setter dari user inputkz
            $posyandu->setTempatPemeriksaan( $_POST['tempat_pemeriksaan'] ?? $posyandu->getTempatPemeriksaan() )
                     ->setTenagaPemeriksaan( $this->getMiddleware()['auth']['user_name'] )
                     ->setTanggalPemeriksaan( $_POST['tanggal_pemeriksaan'] ?? $posyandu->getTanggalPemeriksaan() )
                     ->setTinggiBadan( $_POST['tinggi_badan'] ?? $posyandu->getTinggiBadan() )
                     ->setBeratBadan( $_POST['berat_badan'] ?? $posyandu->getBeratBadan() );
            $update = $posyandu->update( $id );
            // message
            if( $update ){
                $msg = ["show" => true, "type" => 'success', "content" => 'Berhasil di ubah'];       
            }
        } elseif ($isValided == false || $read == false) {
            $error = array('valid_id' => "id tidak valid atau tidak berlaku");
            $this->deny_access();
        } elseif (empty($_POST)) {
            $error = array();
        }

        //  isi form dari data base, 
        $desa_posyandu          = GroupsPosyandu::getPosyanduDesa( $posyandu->getTempatPemeriksaan() );
        $groups_posyandu        = GroupsPosyandu::getPosyandu( $desa_posyandu );
        $tanggal_pemeriksaan    = $posyandu->getTanggalPemeriksaan();
        $tinggi_pemeriksaan     = $posyandu->getTinggiBadan();
        $berat_pemeriksaan      = $posyandu->getBeratBadan();
        
        return $this->view('kia-anak/posyandu/edit', [
            "auth"    => $this->getMiddleware()['auth'],
            "meta"     => [
                "title"         => "Edit Data KIA Anak",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'Edit Biodata',
                "header_menu"   => MENU_POSYANDU
            ],
            "contents" => [
                "desa_posyandu"         => $desa_posyandu,
                "groups_posyandu"       => $groups_posyandu,
                "tanggal_pemeriksaan"   => $tanggal_pemeriksaan,
                "tinggi_pemeriksaan"    => $tinggi_pemeriksaan,
                "berat_pemeriksaan"     => $berat_pemeriksaan
            ],
            'error' => $error,
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }

    private function new_posyandu()
    {
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];
        
        // validation
        $validation = new GUMP('id');
        $validation->validation_rules(array (
            'code_hash' => 'required|alpha_numeric|exact_len,6',
            'desa' => 'required|contains,bandarjo;branjang;kalisidi;keji;lerep;nyatnyono',
            'tempat_pemeriksaan' => 'required|numeric|max_len,2',
            'tanggal_pemeriksaan' => 'required|date,Y-m-d',
            'tinggi_badan' => 'required|numeric|max_len,4',
            'berat_badan' => 'required|numeric|max_len,4',
        ));
        $validation->run($_POST);
        $error = $validation->get_errors_array();

        if (! $validation->errors()) {    
            $posyandu = new PosyanduRecord($_POST['code_hash']);
            $posyandu->convertFromArray($_POST);
            if ($posyandu->creat()) {
                $msg = ["show" => true, "type" => 'success', "content" => 'berhasil menyimpan'];
            } else {
                $msg = ["show" => true, "type" => 'success', "content" => 'gagal menyimpan'];
                $error = array('database' => 'tidak bisa menyimpan data');
            }
        } elseif (empty($_POST)) {
            $error = array();
        }

        // result
        return $this->view('kia-anak/posyandu/new', [
            "auth"    => $this->getMiddleware()['auth'],
            "meta"     => [
                "title"         => "Buat Tambah Posyandu Baru",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'Buat Data Posyandu',
                "header_menu"   => MENU_POSYANDU
            ],
            "contents" => [],
            'error' => $error,
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }

    private function search_posyandu()
    {
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];   

        # ambil parameter dari url
        $main_search     = $_GET['main-search'] ?? '';
        $alamat_search   = $_GET['alamat-search'] ?? '';
        $no_rt_search    = $_GET['no-rt-search'] ?? '';
        $no_rw_search    = $_GET['no-rw-search'] ?? '';
        $nama_kk_search  = $_GET['nama-kk-search'] ?? '';
        $strict_search   = isset( $_GET['strict-search'] ) ? true : false;
        $desa            = $_GET['desa'] ?? null;
        $id_posyandu     = $_GET['tempat_pemeriksaan'] ?? null;

        if( $desa != null){            
            $groups_posyandu = GroupsPosyandu::getPosyandu($desa);
        }

        if( $id_posyandu != null && is_numeric($id_posyandu)){
            $posyandu = GroupsPosyandu::getPosyanduName($id_posyandu);        
        }

        // result
        return $this->view("kia-anak/biodata/search", [
            "auth"    => $this->getMiddleware()['auth'],
            "DNT"      => $this->getMiddleware()['DNT'],
            "meta"     => [
                "title"         => "Cari data Posyandu",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'Cari Data Posyandu',
                "header_menu"   => MENU_POSYANDU
            ],
            "contents" => [
                "nama"              => $main_search,
                "alamat"            => $alamat_search,
                "nomor_rt"          => $no_rt_search,
                "nomor_rw"          => $no_rw_search,
                "nama_kk"           => $nama_kk_search,
                "strict"            => $strict_search,
                "desa"              => $desa,
                "id_posyandu"       => $id_posyandu,
                "groups_posyandu"   => $groups_posyandu ?? null,
                "posyandu"          => $posyandu ?? null,
                "posyandu_exist"    => isset( $posyandu ) && isset( $groups_posyandu ) ? true : false
            ],
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }

    private function view_posyandu()
    {
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];
        
        $pdo = new MyPDO();
        // data posyandu raw
        $posyandu = new PosyanduRecords( $pdo );
        // filter data
        $posyandu->setStrictSearch(true)
                ->filtterByAlamat(10);
        // hasil filter
        $raw = $posyandu->result();
        
        // generate id_hash
        $count = $posyandu->CountID();
        // ambil count by hash_id
        $kolom_terbanyak   = max( array_column($count, 'jumlah_kunjungan') );


        // cari data rm dan alamat
        $medrec = new MedicalRecord();    
        
        for ($i=0; $i < count($count); $i++) {      
            $filrer_by = $count[$i]['id_hash'];
            $count[$i]['data'] = array_filter($raw, function($e) use ($filrer_by){
                return $e['id_hash'] == $filrer_by;
            });
            sort( $count[$i]['data']);
            $relation = Relation::where('id_hash', $count[$i]['id_hash'], $pdo)[0];
            $medrec->refreshUsingIdHash($relation['time_stamp']);
            $count[$i]['nama'] = $medrec->getNama();
            $count[$i]['alamat'] = $medrec->getAlamatLengkap();
        }

        // result
        return $this->view("kia-anak/posyandu/view", [
            "auth"    => $this->getMiddleware()['auth'],
            "DNT"      => $this->getMiddleware()['DNT'],
            "meta"     => [
                "title"         => "Cari data Posyandu",
                "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
                "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
            ],
            "header"   => [
                "active_menu"   => 'Lihat Data Posyandu',
                "header_menu"   => MENU_POSYANDU
            ],
            "contents" => [
                "kolom_terbanyak"       => $kolom_terbanyak,
                "data_posyandu"         => $count
            ],
            "message" => [
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            ]
        ]);
    }
}
