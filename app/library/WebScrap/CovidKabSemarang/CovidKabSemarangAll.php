<?php

namespace WebScrap\CovidKabSemarang;

use DOMDocument;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

class CovidKabSemarangAll
{
  public static function instant()
  {
    return new self;
  }

  private $Daftar_Kecamatan = [
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

  /**
   * Catch all data from 19 api (kecamtan sekabupaten)
   */
  public function catchAll()
  {
    $client = new Client(['base_uri' => 'https://corona.semarangkab.go.id/prona/covid/data_desa']);

    // Initiate each request but do not block
    $promises = [];

    foreach ($this->Daftar_Kecamatan as $key => $val)  {
      $promises[$key] = $client->getAsync('?id_kecamatan=' . $val);
    }

    $responses = Promise\Utils::unwrap($promises);

    $res                = [];
    $kasus_positif      = 0;
    $kasus_isolasi      = 0;
    $kasus_sembuh       = 0;
    $kasus_meninggal    = 0;
    $suspek             = 0;
    $suspek_discharded  = 0;
    $suspek_meninggal   = 0;

    foreach ($responses as $respone_name => $respone) {
      $data = $this->getData($respone_name, $respone->getBody());

      $res[]              = $data;
      $kasus_positif      += $data["kasus_posi"];
      $kasus_isolasi      += $data["kasus_isol"];
      $kasus_sembuh       += $data["kasus_semb"];
      $kasus_meninggal    += $data["kasus_meni"];
      $suspek             += $data["suspek"];
      $suspek_discharded  += $data["discharded"];
      $suspek_meninggal   += $data["suspek_meninggal"];
    }

    return array(
      "kabupaten"         => "semarang",
      "kasus_posi"        => $kasus_positif,
      "kasus_isol"        => $kasus_isolasi,
      "kasus_semb"        => $kasus_sembuh,
      "kasus_meni"        => $kasus_meninggal,
      "suspek"            => $suspek,
      "suspek_discharded" => $suspek_discharded,
      "suspek_meninggal"  => $suspek_meninggal,
      "data"              => $res,
    );
  }

  /**
   * Mengkonvert html table ke array php
   *
   * @param string $nama_kecamatan Nama kecamatan terdaftar diwilayah kabupaten semarang
   * @param string $html Raw data html catch from api
   * @return array|boolean Hasil array covid wilayah kab semarang.
   * False jika data gagal dimuat
   */
  private function getData($nama_kecamatan, $html)
  {
    // parameter untukmendapatkan total data per request
    $positif_dirawat    = 0;
    $positif_isolasi    = 0;
    $positif_sembuh     = 0;
    $positif_meninggal  = 0;
    $suspek             = 0;
    $suspek_discharded  = 0;
    $suspek_meninggal   = 0;

    // memuat data dalam bentuk DOM htlm -> mudah di parsing
    $dom = new DOMDocument();
    $dom->loadHTML($html);

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
      "kasus_posi"        => $positif_dirawat,
      "kasus_isol"        => $positif_isolasi,
      "kasus_semb"        => $positif_sembuh,
      "kasus_meni"        => $positif_meninggal,
      "suspek"            => $suspek,
      "discharded"        => $suspek_discharded,
      "suspek_meninggal"  => $suspek_meninggal,
      "data"              => $desa
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
