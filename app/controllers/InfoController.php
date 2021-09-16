<?php
use System\Router\Controller;
use Simpus\Auth\User;
use Model\JadwalKia\JadwalKia;
use Provider\Session\Session;

class InfoController extends Controller
{
  public function show(string $view_file)
  {
    // replace - to _
    $view_file = str_replace('-', '_', $view_file);
    call_user_func([$this, $view_file]);
  }

  public function Antrian_Online()
  {
    $msg = ['show' => false, 'type' => 'info', 'content' => 'oke'];
    // your code

    $author = new User("angger");
    return $this->view('info/antrian-online', [
      "auth" => Session::getSession()['auth'],
      "meta"  => [
        "title" => "Simulasi Antrian Online",
        "discription" => "Display Antrian Digital Puskesmas Lerep (Tahap Uji Coba)",
        "keywords" => "simpus lerep, puskesmas lerep, Antrian Online, BPJS, Display antrian, Nomor Urut, Poli Umum, Poli Lansia, Poli KIA Ibu dan Anak"
      ],
      "header"   => [
        "active_menu" => 'home',
        "header_menu" => $_SESSION['active_menu'] ?? MENU_MEDREC
      ],
      "contents" => [
        "article" => [
          "display_name" => $author->getDisplayName(),
          "display_picture_small" => $author->getSmallDisplayPicture(),
          'article_create' => '15 Oktober 2020',
          'title' => 'Simulasi Antrian online'
        ],
      ],
      'message' => [
        'show'      => $msg['show'],
        'type'      => $msg['type'],
        'content'   => $msg['content']
      ]
    ]);
  }

  public function Covid_Kabupaten_Semarang()
  {
    $author = new User("angger");
    return $this->view('/info/covid-kabupaten-semarang', [
      "auth"    => Session::getSession()['auth'],
      "meta"     => [
        "title"         => "Info Covid 19 Ungaran Barat",
        "discription"   => "Data Pasien Dalam Pengawasan dan Positif di Wilayah Kecamtan Ungaran Barat",
        "keywords"      => "info covid, covid kabupaten semarang, kawal covid, covid ungaran, covid branjang, wilayah ungaran, Suspek, Discharded, Meninggal, Symptomatik, Asymptomatik, Sembuh, Meninggal, Terkomfirmasi"
      ],
      "header"   => [
        "active_menu"   => 'home',
        "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
      ],
      "contents" => [
        "article"    => [
          "display_name"          => $author->getDisplayName(),
          "display_picture_small" => $author->getSmallDisplayPicture()
        ],
      ],
    ]);
  }

  public function Jadwal_Pelayanan()
  {
    $author = new User("angger");
    $imun   = new JadwalKia();

    return $this->view('/info/jadwal-pelayanan', [
      "auth"    => Session::getSession()['auth'],
      "meta"     => [
        "title"         => "Jadwal Pelayanan di Poli KIA - Simpus Lerep",
        "discription"   => "Jadwal pelayanan imunisasi anak di Poli KIA",
        "keywords"      => "simpus lerep, puskesmas lerep, jadwal imunisasi, imunisasi, kia anak, jadwal, BCG, Campak, Rubella (MR), Hib, HB, DPT, IPV"
      ],
      "header"   => [
        "active_menu"   => 'home',
        "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
      ],
      "contents" => [
        "article"    => [
          "display_name"          => $author->getDisplayName(),
          "display_picture_small" => $author->getSmallDisplayPicture()
      ],
        "raw_data"          => $imun->getData(date('m'), date('Y')),
        "avilable_month"    => $imun->getAvilabeMonth()
      ]
    ]);
  }

  public function Vaksin()
  {
    $author = new User("angger");

    return $this->view('/info/vaksin', [
      "auth"    => Session::getSession()['auth'],
      "meta"     => [
        "title"         => "Update Vaksinasi Covid-19 Di Puskesmas Lerep",
        "discription"   => "Update Info Vaksinasi Covid-19 Di Puskesmas Lerep",
        "keywords"      => "simpus lerep, puskesmas lerep, Info vaksin, jadwal vaksin, update vaksin"
      ],
      "header"   => [
        "active_menu"   => 'home',
        "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
      ],
      "contents" => array(
        "article"    => [
          "display_name"          => $author->getDisplayName(),
          "display_picture_small" => $author->getSmallDisplayPicture()
        ],
      )
    ]);
  }
}
