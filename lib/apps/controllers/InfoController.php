<?php
use Simpus\Apps\Controller;
use Simpus\Auth\User;
use Simpus\Services\JadwalKia;

class InfoController extends Controller
{
  public function show(string $view_file)
  {
    // replace - to _
    $view_file =str_replace('-', '_', $view_file);
    call_user_func([$this, $view_file]);
  }

  public function Antrian_Online()
  {
    $msg = ['show' => true, 'type' => 'info', 'content' => 'oke'];
    // your code
    
    $author = new User("angger");
    return $this->view('info/antrian-online', [
      "auth" => $this->getMiddleware()['auth'],
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
    $data_covid = new CovidKabSemarangService();

    $track_record   = $data_covid->track_record(["toString" => true])['data'];
    $data_record    = $data_covid->tracker(['range_waktu' => $track_record]);
    krsort($data_record);
    // data: konirmasi covid
    $date_record    = json_encode( array_values(array_column($data_record, "time")) );
    $posi_record    = json_encode( array_values(array_column($data_record, "kasus_posi")) );
    $meni_record    = json_encode( array_values(array_column($data_record, "kasus_meni")) );
    // data: suspek covid
    $suspek                = json_encode( array_values(array_column($data_record, "suspek")) );
    $suspek_disc_record    = json_encode( array_values(array_column($data_record, "suspek_discharded")) );
    $suspek_meni_record    = json_encode( array_values(array_column($data_record, "suspek_meninggal")) );

    $author = new User("angger");
    return $this->view('/info/covid-kabupaten-semarang', [
      "auth"    => $this->getMiddleware()['auth'],
      "meta"     => [
        "title"         => "Info Covid 19 Ungaran Barat",
        "discription"   => "Data Pasien Dalam Pengawasan dan Positif di Wilayah Kecamtan Ungaran Barat",
        "keywords"      => "simpus lerep, info covid, kawal covid, covid ungaran, covid branjang, wilyah ungran, Suspek, Discharded, Meninggal, Symptomatik, Asymptomatik, Sembuh, Meninggal, Terkomfirmasi"
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
        "last_index"                => 1,
        "date_record"               => $date_record ?? [],
        "kasus_posi"                => $posi_record ?? [],
        "kasus_meni"                => $meni_record ?? [],
        "suspek"                    => $suspek ?? [],
        "suspek_disc"               => $suspek_disc_record ?? [],
        "suspek_meni"               => $suspek_meni_record ?? [],
        ]
    ]);
  }

  public function Jadwal_Pelayanan()
  {
    $author = new User("angger");
    $imun   = new JadwalKia(date('m'), date('Y'));

    return $this->view('/info/jadwal-pelayanan', [
      "auth"    => $this->getMiddleware()['auth'],
      "meta"     => [
        "title"         => "Jadwal Pelayanan di Poli KIA - Simpus Lerep",
        "discription"   => "Jadwal pelayanan imunisasi anak di Poli KIA",
        "keywords"      => "simpus lerep, puskesmas lerep,jadwal imunisasi, imunusasi, kia anak, jadwal, BCG, Campak, Rubella (MR), Hib, HB, DPT, IPV"
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
        "raw_data"          => $imun->getData(),
        "avilable_month"    => $imun->getAvilabeMonth()
      ]
    ]);
  }
}
