<?php

namespace WebScrap\CovidKabSemarang;

use DOMDocument;
use GuzzleHttp\Client;

class CovidKabSemarang
{
  private $_positif_dirawat = 0;
  private $_positif_isolasi = 0;
  private $_positif_sembuh = 0;
  private $_positif_meninggal = 0;
  private $_suspek = 0;
  private $_suspek_discharded = 0;
  private $_suspek_meninggal = 0;
  private $_raw_data = [];

  /**
   * @return integer jumlah positif covid dirawat
   */
  public function positifDirawat(): int
  {
    return (int) $this->_positif_dirawat;
  }

  /**
   * @return integer Jumlah pasien positif yang diisolasi
   */
  public function positifIsolasi(): int
  {
    return (int) $this->_positif_isolasi;
  }

  /**
   * @return integer jumlah positif covid dirawat
  */
  public function positifSembuh(): int
  {
    return (int) $this->_positif_sembuh;
  }

  /**
   * @return integer jumlah positif covid dirawat
   */
  public function positifMeninggal(): int
  {
    return (int) $this->_positif_meninggal;
  }

  /**
   * @return integer jumlah suspek covid
   */
  public function suspek(): int
  {
    return (int) $this->_suspek;
  }

  /**
   * @return integer jumlah suspek discharded covid
   */
  public function suspekDischarded(): int
  {
    return (int) $this->_suspek_discharded;
  }

  /**
   * @return integer jumlah suspek covid meninggal
   */
  public function suspekMeninggal(): int
  {
    return (int) $this->_suspek_meninggal;
  }

  /**
   * @return integer Raw data (semua data)
   */
  public function data(): array
  {
    return $this->_raw_data;
  }

  /**
   * @var array array id kecamatan se kabupaten
   */
  public $Daftar_Kecamatan = [
    "getasan"       => 1,
    "tengaran"      => 2,
    "susukan"       => 3,
    "suruh"         => 4,
    "pabelan"       => 5,
    "tuntang"       => 6,
    "banyubiru"     => 7,
    "jambu"         => 8,
    "sumowono"      => 9,
    "ambarawa"      => 10,
    "bawen"         => 11,
    "bringin"       => 12,
    "bergas"        => 13,
    "pringapus"     => 14,
    "bancak"        => 15,
    "kaliwungu"     => 16,
    "ungaran-barat" => 17,
    "ungaran-timur" => 18,
    "bandungan"     => 19
  ];

  public static function instant()
  {
    return new self;
  }

  /**
   * Mengkonvert html table ke array php
   * @param string $nama_kecamatan Nama kecamatan terdaftar diwillayah kabupaten semarang
   * @return array|boolean Hasil array covid wilayah kab semarang.
   * False jika data gagal dimuat
   */
  public function getData($nama_kecamatan)
  {
    // parameter untukmendapatkan total data per request
    $positif_dirawat    = 0;
    $positif_isolasi    = 0;
    $positif_sembuh     = 0;
    $positif_meninggal  = 0;
    $suspek             = 0;
    $suspek_discharded  = 0;
    $suspek_meninggal   = 0;

    // mengkonvert nama wilayah kedalam id kecamatan
    $id = $this->Daftar_Kecamatan[$nama_kecamatan];

    // memuat data mentah dari web dinkes
    $client = new Client();
    $res = $client->get("https://corona.semarangkab.go.id/prona/covid/data_desa?id_kecamatan=$id");
    if ($res->getStatusCode() > 200) {
      return false;
    }

    // memuat data dalam bentuk DOM htlm -> mudah di parsing
    $dom = new DOMDocument();
    $dom->loadHTML($res->getBody());

    $desa = [];

    // loop data per row atau per wilayah
    foreach ($dom->getElementsByTagName('tbody') as $th) {

      // loop data per colomn (nilai dari wilayah)
      foreach ($th->getElementsByTagName('tr') as $row) { // satu desa

        // mengambil data satu persatu dari setiap colomn di baris (desa)
        $td = $row->getElementsByTagName('td');

        // grub array untuk data pdp
        $data_pdp = [];
        $data_pdp["dirawat"]    = $this->removeDoubleSpace($td->item(2)->nodeValue);
        $data_pdp["sembuh"]     = $this->removeDoubleSpace($td->item(3)->nodeValue);
        $data_pdp["meninggal"]  = $this->removeDoubleSpace($td->item(4)->nodeValue);
        $data_pdp["keterangan"] = $this->removeDoubleSpace($td->item(5)->nodeValue);

        // grup array untuk grup positif covid
        $positif_covid = [];
        $positif_covid["dirawat"]       = $this->removeDoubleSpace($td->item(6)->nodeValue);
        $positif_covid["isolasi"]       = $this->removeDoubleSpace($td->item(7)->nodeValue);
        $positif_covid["sembuh"]        = $this->removeDoubleSpace($td->item(8)->nodeValue);
        $positif_covid["meninggal"]     = $this->removeDoubleSpace($td->item(9)->nodeValue);
        $positif_covid["keterangan"]    = $this->removeDoubleSpace($td->item(10)->nodeValue);

        //  mengakumulasi jumlah pasien dalam satu kelurahan / request
        $suspek             += $data_pdp['dirawat'];
        $suspek_discharded  += $data_pdp["sembuh"];
        $suspek_meninggal   += $data_pdp["meninggal"];
        $positif_dirawat    += $positif_covid["dirawat"];
        $positif_isolasi    += $positif_covid["isolasi"];
        $positif_sembuh     += $positif_covid["sembuh"];
        $positif_meninggal  += $positif_covid["meninggal"];

        // memasukan data satu row / desa kedalam arra utama (array kebupaten)
        $satu_desa = [];
        $satu_desa["desa"]      = $this->removeDoubleSpace($td->item(1)->nodeValue);
        $satu_desa["pdp"]       = $data_pdp;
        $satu_desa["positif"]   = $positif_covid;

        // hasil data satu desa
        $desa[] = $satu_desa;
      }
    }

    // mengkelompok kan semua hasil menjadi satu
   return [
      "kecamatan"         => $nama_kecamatan,
      "kasus_posi"        => $this->_positif_dirawat    = $positif_dirawat,
      "kasus_isol"        => $this->_positif_isolasi    = $positif_isolasi,
      "kasus_semb"        => $this->_positif_sembuh     = $positif_sembuh,
      "kasus_meni"        => $this->_positif_meninggal  = $positif_meninggal,
      "suspek"            => $this->_suspek             = $suspek,
      "discharded"        => $this->_suspek_discharded  = $suspek_discharded,
      "suspek_meninggal"  => $this->_suspek_meninggal   = $suspek_meninggal,
      "data"              => $this->_raw_data           = $desa
    ];
  }

  /**
   * Helper -> mengilangakan whitespace pada string
   * @return string string tanpa ada doble space
   */
  private function removeDoubleSpace($text)
  {
    // menghilangkan multy space
    $text = preg_replace('/\s+/', ' ', $text);
    // menghilangkan space di depan
    $text = preg_replace('/^( )/', '', $text);
    // menghilangkan space dibelkang
    $text = preg_replace('/( )$/', '', $text);
    return $text;
  }
}
