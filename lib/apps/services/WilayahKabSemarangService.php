<?php

use Simpus\Apps\Middleware;
use Simpus\Database\MyPDO;

class WilayahKabSemarangService extends Middleware
{
    /** @var MyPDO */
    private $db;

    public function __construct()
    {
        $this->db = new MyPDO();
    }

    public function Data_Kabupaten()
    {
        $this->db->query("SELECT `kecamatan`
                            FROM `desa_kecamatan`
                            GROUP BY `kecamatan`");
        $result = $this->db->resultset();

        return [
            'status'        => 'ok',
            'level'         => 1,
            'kabupaten'     => 'kab semarang',
            'data'          => $result,
            'headers'       => ['HTTP/1.1 200 Oke']
        ];
    }

    public function Data_Kecamatan(array $params)
    {
        $kecamatan = $params['kecamatan'] ?? 'ungaran-barat';
        $this->db->query("SELECT `desa`
                            FROM `desa_kecamatan`
                            WHERE `kecamatan` = :kecamatan");
        $this->db->bind(':kecamatan', $kecamatan);
        $result = $this->db->resultset();
        $result = array_column($result, 'desa');       

        return [
            'status'        => 'ok',
            'level'         => 2,
            'kabupaten'     => 'kab semarang',
            'kecamatan'     => $kecamatan,
            'data'          => $result,
            'headers'       => ['HTTP/1.1 200 Oke']
        ];
    }

    public function Data_Desa($params)
    {
        $json = '{"branjang":{"1":"branjang","2":"truko","3":"cemanggah lor","4":"cemanggah kidul","5":"dersune"},"bandarjo":{"1":"bandarjo i","2":"bandarjo i","3":"bandarjo ii","4":"bandarjo iii","5":"bandarjo iv","6":"bandarjo v","7":"bandarjo v","8":"bandarjo vi","9":"bandarjo vi","10":"bandarjo vii","11":"bandarjo viii","12":"bandarjo ix"},"kalisidi":{"1":"manikmoyo","2":"mrunten kulon","3":"mrunten wetan","4":"pilahan","5":"compok","6":"kalisidi","7":"bender dukuh","8":"bender desa","9":"gebug"},"keji":{"1":"keji","2":"keji","3":"suruhan","4":"setoyo"},"lerep":{"1":"indrokilo","2":"lerep ii","3":"lerep iii","4":"soko","5":"tegalrejo","6":"lorog","7":"karangbolo","8":"kretek","9":"mapagan","10":"mapagan"},"nyatnyono":{"1":"ngaglik","2":"gelap","3":"dampyak","4":"sipol","5":"krajan","6":"siroto","7":"sigade","8":"sendang putri","9":"sendang rejo","10":"blanggah","11":"balnten"}}';

        return [
            'status'        => 'ok',
            'level'         => 3,
            'kabupaten'     => 'kab semarang',
            'kecamatan'     => 'ungaran-barat',
            'data'          => json_decode($json, true),
            'headers'       => ['HTTP/1.1 200 Oke']
        ];

    }
}
