<?php

use Convert\Converter\ConvertCode;
use Model\Simpus\{MedicalRecord, MedicalRecords, PersonalRecord, Relation};
use Simpus\Auth\Log;
use Simpus\Apps\Controller;
use System\Database\MyPDO;

class RekamMedisController extends Controller
{
  private $PDO = null;
  private $validation_rule = array (
    'nomor_rm' => 'required|numeric|max_len,6',
    'nama' => 'required|alpha_space|min_len,4|max_len,32',
    'tgl_lahir' => 'date,Y-m-d',
    'alamat' => 'alpha_space|max_len,20',
    'nomor_rt' => 'numeric|max_len,2',
    'nomor_rw' => 'numeric|max_len,2',
    'nama_kk' => 'alpha_space|min_len,4|max_len,32',
    'nomor_rm_kk' => 'numeric|max_len,6',
    // personal data
    'nik' => 'numeric|min_len,16|max_len,16',
    'nomor_jaminan' => 'numeric|min_len,8|max_len,13',
  );
  private $filter_rule = array (
    'nama' => 'trim|htmlencode',
    'alamat' => 'trim|htmlencode',
    'nama_kk' => 'trim|htmlencode'
  );

  public function __construct()
  {
    if (isset($_GET['active_menu'])) {
      $_SESSION['active_menu'] = MENU_MEDREC;
    }

    $this->PDO = MyPDO::getInstance();
    //  WARNING:    fungsi ini adalah funsi authrization, wajib ada

    // call_user_func_array($this->getMiddleware()['before'], []);
    if ($this->getMiddleware()['auth']['login'] == false) {
      DefaultController::page_401(array (
        'links' => array (
          array('Home Page', '/'),
          array('Login',  '/login?url=' . $_SERVER['REQUEST_URI'])
        )
      ));
    }
  }

  public function index()
  {
    $rm = new MedicalRecords();

    // mengambil data jumlah rm bedasarkan desa
    $arr_desa = ['bandarjo', 'branjang', 'kalisidi', 'keji', 'lerep', 'nyatnyono'];
    $arr_data = [];
    $jumlah_rm = $rm->maxData();
    foreach ($arr_desa as $desa) {
      $rm->filterByAlamat($desa);
      $arr_data[$desa] = $rm->maxData();
    }

    // mengambil data jumlah rm berdasarkan range umur
    $rm->reset();
    $arr_umur  = ["0-5", "5-16", "17-25", "26-45", "46-65", "65-100"];
    $arr_data2 = [];
    foreach ($arr_umur as $umur) {
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

  public function show($view_file)
  {
    $view_file = $view_file == 'view' ? 'view_' : $view_file;  // alias untuk page 'view'
    call_user_func([$this, $view_file]);
  }

  /* view page di panggil secara manual, keculai index.php */


  public function edit()
  {
    $msg = ["show" => false, "type" => 'info', "content" => 'oke'];
    $error = array();

    // validasi data
    $validation = new GUMP('id');
    $validation->validation_rules($this->validation_rule);
    $validation->filter_rules($this->filter_rule);
    $validation->run($_POST);
    $error = $validation->get_errors_array();

    // ambil data rm menggunakn  id
    if (isset($_GET['document_id'])) {
      $id = $_GET['document_id'];
      // default property
      $status_kk = $status_double = false;
      // load data
      $edit_rm = MedicalRecord::withId($id, $this->PDO);

      //  menjegah duplikasi data saaat merefresh page
      $last_data = $_SESSION['last_data'] ?? ['fresh_array'];

      if (!$validation->errors() && $last_data != $_POST) {

        // update data rm
        $edit_rm->convertFromArray($_POST);

        //simpan data
        if ($edit_rm->save()) {
          $time = $edit_rm->getDataDibuat();
          $hash_id = ConvertCode::ConvertToCode($time);

          // cek punya table relation atau tidak
          if (Relation::has_timestamp($time)) {
            // update pernonal data
            $update_bio = PersonalRecord::whereHashId($hash_id, $this->PDO)
              ->convertFromArray($_POST)
              ->setDataDiupdate(time())
              ->filter();

            if ($update_bio->isValid()) {
              $update_bio->update();
            }

          } else {
            // create new personal data
            $new_bio = new PersonalRecord($this->PDO);
            $new_bio
              ->convertFromArray($_POST)
              ->setHashId($hash_id)
              ->setDataDibuat($time)
              ->filter();

            if ($new_bio->isValid()) {
              Relation::creat($hash_id, $time);
              $new_bio->create();
            }
          }

          $msg = ["show" => true, "type" => 'success', "content" => 'berhasil diupdate'];
          $_SESSION['last_data'] = $_POST;

          // user log
          $log = new Log( $this->getMiddleware()['auth']['user_name'] );
          $log->set_event_type('med-rec');
          $log->save( $edit_rm->getLastQuery() );
        } else {
          $msg = ["show" => true, "type" => 'danger', "content" => 'gagal disimpan'];
        }
      } elseif (empty($_POST)) {
        $error = array();
      } elseif (isset($_POST['submit'])) {
        $msg = ["show" => true, "type" => 'danger', "content" => 'tidak ada perubahan data'];
      }

      // memuat data dari data base
      $nomorRM      = $edit_rm->getNomorRM();
      $nama         = $edit_rm->getNama();
      $tanggalLahir = $edit_rm->getTangalLahir();
      $alamat       = $edit_rm->getAlamat();
      $nomorRt      = $edit_rm->getNomorRt();
      $nomorRw      = $edit_rm->getNomorRw();
      $namaKK       = $edit_rm->getNamaKK();
      $nomorRM_KK   = $edit_rm->getNomorRM_KK();

      // cek data rm terdaftar atau tidak
      if ($edit_rm->cekAxis() == false) {
        DefaultController::page_403();
      }

      // personal data
      $hash_id      = ConvertCode::ConvertToCode($edit_rm->getDataDibuat());
      $biodata      = PersonalRecord::whereHashId($hash_id, $this->PDO);

      // cek status kk
      $status_kk = $nama === $namaKK ? true : false;
      // cari rm yang sama
      $cari_rm = new MedicalRecords($this->PDO);
      $cari_rm
        ->filterByNomorRm($nomorRM)
        ->forceLimitView(2);
      $status_double = $cari_rm->maxData() > 1 ? true : false;

    } else {
      DefaultController::page_403();
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
        "nomor_rm_kk"       => $nomorRM_KK,
        "nik"               => $biodata->nik(),
        "nomor_jaminan"     => $biodata->nomor_jaminan(),
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
    // validasi data
    $validation = new GUMP('id');
    $validation->validation_rules($this->validation_rule);
    $validation->filter_rules($this->filter_rule);
    $validation->run($_POST);
    $error = $validation->get_errors_array();

    // menjegah duplikasi data saat form direfresh
    $last_data = $_SESSION['last_data'] ?? [];

    if (!$validation->errors() && $last_data != $_POST) {

      $time = time();
      $hash_id = ConvertCode::ConvertToCode($time);

      // simpan data_rm
      $new_rm = new MedicalRecord($this->PDO);
      $new_rm
        ->convertFromArray($_POST)
        ->setDataDibuat($time)
        ->filter();

      //simpan data
      if ($new_rm->insertNewOne()) {

        // simpan data_personal
        $new_bio = new PersonalRecord($this->PDO);
        $new_bio
          ->convertFromArray($_POST)
          ->setHashId($hash_id)
          ->setDataDibuat($time)
          ->filter();

        if ($new_bio->isValid()) {
          Relation::creat($hash_id, $time);
          $new_bio->create();
        }

        $msg = ["show" => true, "type" => 'success', "content" => 'berhasil disimpan'];
        $_SESSION['last_data'] = $_POST;
        $_POST = [];
      } else {
        $msg = ["show" => true, "type" => 'danger', "content" => 'Gagal disimpan'];
      }
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
        "nomor_rm"      => $_POST['nomor_rm'] ?? '',
        "nama"          => $_POST['nama'] ?? '',
        "tgl_lahir"     => $_POST['tgl_lahir'] ?? '',
        "alamat"        => $_POST['alamat'] ?? '',
        "nomor_rt"      => $_POST['nomor_rt'] ?? '',
        "nomor_rw"      => $_POST['nomor_rw'] ?? '',
        "nama_kk"       => $_POST['nama_kk'] ?? '',
        "nomor_rm_kk"   => $_POST['nomor_rm_kk'] ?? '',
        'nik'           => $_POST['nik'] ?? '',
        'nomor_jaminan' => $_POST['nomor_jaminan'] ?? '',
      ],
      'error' => $error,
      "message" => [
        "show"      => $msg['show'],
        "type"      => $msg['type'],
        "content"   => $msg['content']
      ]
    ]);
  }

  public function search()
  {
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
    $strict_search   = empty($_GET) ? true : $strict_search;
    $nik_jaminan     = $_GET['nik-jaminan'] ?? '';

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
        "strict"        => $strict_search,
        'nik_jaminan'   => $nik_jaminan,
      ],
      "message" => [
        "show"      => $msg['show'],
        "type"      => $msg['type'],
        "content"   => $msg['content']
      ]
    ]);
  }

  public function view_()
  {
    $msg = ["show" => false, "type" => 'info', "content" => 'oke'];

    // config
    $sort = 'nomor_rm';
    $order = 'DESC';
    $page = $_GET['page'] ?? 1;
    $page = is_numeric($page) ? $page : 1;

    // ambil data
    $data_rm = new MedicalRecords();
    $data_rm
      ->sortUsing($sort)
      ->orderUsing($order)
      ->limitView(25);

    $max_page = $data_rm->maxPage();
    $page = $page > $max_page ? $max_page : $page;
    $data_rm->currentPage($page);

    return $this->view('rekam-medis/view', [
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
        "data_rm"   => $data_rm->result()
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
