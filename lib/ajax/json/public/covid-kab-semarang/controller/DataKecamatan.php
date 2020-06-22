<?php
/**
 * api untuk mendapatkan informasi covid-19 untuk wilayah kabupaten semarang,
 * api ini dibuat untuk keperluan pribadi, terbuka juga untuk umun untuk mengembangkannya.
 * sumber data dimabil dari situs resmi dinkes kabupaten semarang, dengan cara menkovert data sumber.
 * data sumber berformat html-table(rest api berformat html) dan kemudian di format ulang dalam bentuk JSON.
 * isi dari data tidak dirubah sama sekali, hanya dikonvert ulang, segala bentuk penyalahguanan data bukan tanggung masing-masing.
 * 
 * foramt api dapat berubah sewaktu-waktu
 * @link https://corona.semarangkab.go.id/covid/ 
 * @author sonypradana@gmail.com
 */
class DataKecamatan{
    /** @var integer jumlah positif covid dirawat */
    private $_positif_dirawat = 0;
    /** @var integer Jumlah isolasi */
    private $_positif_isolasi = 0;
    /** @var integer jumlah positif covid sembuh */
    private $_positif_sembuh = 0;
    /** @var integer jumlah positif covid meninggal */
    private $_positif_meninggal = 0;

    /** @return integer jumlah positif covid dirawat*/
    public function positifDirawat(){
        return (int) $this->_positif_dirawat;
    }
    /** @return integer Jumlah pasien positif yang diisolasi */
    public function positifIsolasi(){
        return (int) $this->_positif_isolasi;
    }
    /** @return integer jumlah positif covid dirawat*/
    public function positifSembuh(){
        return (int) $this->_positif_sembuh;
    }
    /** @return integer jumlah positif covid dirawat*/
    public function positifMeninggal(){
        return (int) $this->_positif_meninggal;
    }

    /** @var array array id kecamatan se kabupaten */
    public $Dafatar_Kecamatan = [
        "getasan" => 1,
        "tengaran" => 2,
        "susukan" => 3,
        "suruh" => 4,
        "pabelan" => 5,
        "tuntang" => 6,
        "banyubiru" => 7,
        "jambu" => 8,
        "sumowono" => 9,
        "ambarawa" => 10,
        "bawen" => 11,
        "bringin" => 12,
        "bergas" => 13,
        "pringapus" => 14,
        "bancak" => 15,
        "kaliwungu" => 16,
        "ungaran-barat"  => 17,
        "ungaran-timur"  => 18,
        "bandungan" => 19
    ];

    /** mengkonvert data table kedalam array
     * @param string $nama_kecamatan nama kecamatan terdaftar diwillayah kabupaten semarang
     * @return array hasil kovert data table covid wilayah ksb semarang
     */
    public function getData($nama_kecamatan){
        // parameter untukmendapatkan total data per request
        $positif_dirawat = 0;
        $positif_isolasi = 0;
        $positif_sembuh = 0;
        $positif_meninggal = 0;

        // mengkonvert nama wilayah kedalam id kecamatan
        $id = $this->Dafatar_Kecamatan[$nama_kecamatan];
        
        // memuat data mentah dari web dinkes
        $ch = curl_init("https://corona.semarangkab.go.id/covid/data_desa?id_kecamatan=$id");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $html = curl_exec($ch);
        curl_close($ch);

        // memuat data dalam bentuk DOM htlm -> mudah di parsing
        $dom = new DOMDocument();
        $dom->loadHTML($html);

        // mengambil konten utama (isi data)
        $body_table = $dom->getElementsByTagName('tbody');

        // me loop data per row atau per wilayah
        $desa = [];
        foreach( $body_table as $th){
            // per wilayah di bagi berdsarkan tr (table row)
            $tr = $th->getElementsByTagName('tr');
            foreach( $tr as $row){ # satu desa
                // mengambil data satu persatu dari setiap colomn di baris (desa)
                $td = $row->getElementsByTagName('td');
                
                $data_pdp = []; # grub array untuk data pdp
                $data_pdp["dirawat"] = $this->removeDoubleSpace($td->item(3)->nodeValue);
                $data_pdp["sembuh"] = $this->removeDoubleSpace($td->item(3)->nodeValue);
                $data_pdp["meninggal"] = $this->removeDoubleSpace($td->item(4)->nodeValue);
                $data_pdp["keterangan"] = $this->removeDoubleSpace($td->item(5)->nodeValue);

                $positif_covid = []; # grup array untuk grup positif covid
                $positif_covid["dirawat"] = $this->removeDoubleSpace($td->item(6)->nodeValue);
                $positif_covid["isolasi"] = $this->removeDoubleSpace($td->item(7)->nodeValue);
                $positif_covid["sembuh"] = $this->removeDoubleSpace($td->item(8)->nodeValue);
                $positif_covid["meninggal"] = $this->removeDoubleSpace($td->item(9)->nodeValue);
                $positif_covid["keterangan"] = $this->removeDoubleSpace($td->item(10)->nodeValue);

                //  mengakumulasi jumlah pasien dalam satu kelurahan / request
                $positif_dirawat += $positif_covid["dirawat"];
                $positif_isolasi += $positif_covid["isolasi"];
                $positif_sembuh += $positif_covid["sembuh"];
                $positif_meninggal +=  $positif_covid["meninggal"];

                // memasukan data satu row / desa kedalam arra utama (array kebupaten)
                $satu_desa = [];
                $satu_desa["desa"] = $this->removeDoubleSpace($td->item(1)->nodeValue);
                $satu_desa["pdp"] = $data_pdp;
                $satu_desa["positif"] = $positif_covid;

                // hasil data satu desa
                $desa[] = $satu_desa;                
            }
        }
        // mengkelompok kan semua hasil menjadi satu
        $paket = [
            "kecamatan" => $nama_kecamatan,
            "kasus_posi" => $positif_dirawat,
            "kasus_isol" => $positif_isolasi,
            "kasus_semb" => $positif_sembuh,
            "kasus_meni" => $positif_meninggal,
            "data" => $desa
        ];

        // memberitahu ke clinet hasil komulatif data (internal class)
        $this->_positif_dirawat = $positif_dirawat;
        $this->_positif_isolasi = $positif_isolasi;
        $this->_positif_sembuh = $positif_sembuh;
        $this->_positif_meninggal = $positif_meninggal;

        // hasil akhir data
        return $paket;
    }

    /** helper -> mengilangakan whitespace pada string
     * @return string string tanpa ada doble space
     */
    private function removeDoubleSpace( $text ){
        // menghilkan multy space
        $text = preg_replace('/\s+/', ' ', $text);
        // menghilankan space di depan
        $text = preg_replace('/^( )/', '', $text);
        // menghilangkan space dibelkang
        $text = preg_replace('/( )$/', '', $text);
        return $text;
    }
}
