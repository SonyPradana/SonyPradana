<?php

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
    
    // biodata 

    private function edit_biodata()
    {
        $msg = ["show" => false, "type" => 'info', "content" => 'oke'];
        function deny($msg = '')
        {
            header('HTTP/1.0 405 Method Not Allowed');
            echo '<h1>Acces deny!!!</h1>';
            echo '<p>' . $msg . '</p>';
            exit();
        }

        $success   = false;
        $code_hash = $_GET['document_id'] ?? false;
        if( $code_hash == false ) deny();
        $get_code_hash = Relation::where('id_hash', $code_hash);
        $id_hash     = $get_code_hash[0]['time_stamp'] ?? false;
        if( $id_hash == false ) deny();

        // ambil data lama
        $data_rm = new MedicalRecord();                             // data rm
        if($data_rm->refreshUsingIdHash( $id_hash ) == false) deny('document tidak ditemukan');

        $data_kia = new KIAAnakRecord();                            // data kia
        $data_kia->loadWithID( $code_hash );
        if( $data_kia->cekExist() == false ) deny('documnet tidak terdaftar');    

        // update data
        if( isset( $_POST['request'] ) ){
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
                $msg = ["show" => true, "type" => 'danger', "content" => 'gagal menyimpan disimpan'];
            }
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
        // simpan data
        if( isset( $_POST['request'] ) ){
            // default
            $id_hash = time();
            $success_stagingRM = false;
            $success_dataKIA = false;

            if( $_POST['target']  == 'staging' ){
                // request data rm baru
                $stagingRM = new MedicalRecord();
                $stagingRM->setNama( $_POST['nama_rm'] ?? 'bayi nyonya Y' );
                $stagingRM->setTanggalLahir( $_POST['tanggal_lahir'] ?? date("M/d/Y", time()) );
                $stagingRM->setAlamat( $_POST['alamat'] ?? '');
                $stagingRM->setNomorRt( $_POST['nomor_rt'] ?? 0 );
                $stagingRM->setNomorRw( $_POST['nomor_rw'] ?? 0 );
                $stagingRM->setNamaKK( $_POST['nama_kk'] ?? 'tuan x');
                $stagingRM->setStatus('{"grub":["posyandu", "kia-anak"]}');
                $stagingRM->setDataDibuat( $id_hash );
                // simpan data ke staging data rm
                $success_stagingRM = $stagingRM->insertNewOne('', 'staging_rm');

                // var_dump($stagingRM->getData());
            }elseif( $_POST['target'] == 'data_rm' && isset( $_POST['timestamp'] )){
                $success_stagingRM = true;
                $id_hash = $_POST['timestamp'];
            }

            // cek data rm ada atau tidak
            if( $success_stagingRM ){
                // request data posyandu baru
                $dataKIA = new KIAAnakRecord();
                $dataKIA->convertFromArray( $_POST );
                // table relation
                $table_relation = new Relation(CCode::ConvertToCode( $id_hash ), $id_hash);
                // simpan data biodata kia
                $success_dataKIA = $dataKIA->creat( CCode::ConvertToCode( $id_hash ) );

                if( $success_dataKIA && $table_relation->creat() ){
                    $success = true;
                    $msg = ["show" => true, "type" => 'success', "content" => 'Berhasil disimpan'];
                }
            }
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

        $document_id = $_GET['document_id'] ?? header_exit();
        $params      = explode('-', $document_id);                                // [0]: code_hash, [1]: id 
        //  
        $code_hash  = $params[0];                                                // validasi document id type, harus angka
        $id         = $params[1] ?? header_exit();
        // 
        $posyandu   = new PosyanduRecord($code_hash);
        $isValided  = $posyandu->IsValided();
        $read       = $posyandu->read( $id );
        
        if( isset( $_POST['request']) && $isValided && $read){
            // setter dari user input
            $posyandu->setTempatPemeriksaan( $_POST['tempat_pemeriksaan'] ?? $posyandu->getTempatPemeriksaan() )
                     ->setTenagaPemeriksaan( $this->getMiddleware()['auth']['user_name'] )
                     ->setTanggalPemeriksaan( $_POST['tanggal_pemeriksaan'] ?? $posyandu->getTanggalPemeriksaan() )
                     ->setTinggiBadan( $_POST['tinggi_badan'] ?? $posyandu->getTinggiBadan() )
                     ->setBeratBadan( $_POST['berat_badan'] ?? $posyandu->getBeratBadan() );
            $update = $posyandu->update( $id );
            // message
            if( $update ){
                $msg = [];
                $msg['message'] = 'Berhasil disimpan';
                $msg['type'] = 'success';        
            }
        }elseif( $isValided == false || $read == false){
            header_exit();
        }
        //  isi form dari data base, 
        $desa_posyandu          = GroupsPosyandu::getPosyanduDesa( $posyandu->getTempatPemeriksaan() );
        $groups_posyandu        = GroupsPosyandu::getPosyandu( $desa_posyandu );
        $tanggal_pemeriksaan    = $posyandu->getTanggalPemeriksaan();
        $tinggi_pemeriksaan     = $posyandu->getTinggiBadan();
        $berat_pemeriksaan      = $posyandu->getBeratBadan();
        
        // function helper
        function header_exit(){
            header('HTTP/1.0 405 Method Not Allowed');
            echo '<h1>Acces deny!!!</h1>';
            exit();
        }

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

        if( isset($_POST['request']) && isset( $_POST['code_hash'] )){
            if( $_POST['code_hash'] == '') return false;
    
            $posyandu = new PosyanduRecord($_POST['code_hash']);
            $posyandu->convertFromArray($_POST);
            if( $posyandu->creat() ){
                $msg = ["show" => true, "type" => 'success', "content" => 'berhasil menyimpan'];
            }else{
                $msg = ["show" => true, "type" => 'success', "content" => 'gagal menyimpan'];
            }
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
        $medrec = new MedicalRecord($pdo);    
        
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
