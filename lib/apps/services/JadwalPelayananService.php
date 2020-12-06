<?php

use Simpus\Apps\Middleware;
use Simpus\Services\JadwalKia;
use System\Database\MyPDO;

class JadwalPelayananService extends Middleware
{
    protected $PDO = null;
    public function __construct(MyPDO $PDO = null)
    {
        $this->PDO = $PDO ?? new MyPDO();
    }

    public function Imunisasi(array $params) :array
    {
        $month = $params['month'] ?? date('m');
        $year  = $params['year'] ?? date('Y');

        $jadwal = new JadwalKia($this->PDO);
        $result = $jadwal->getdata($month, $year);
        $result['status']   = 'ok';
        $result['headers']  = ['HTTP/1.1 200 Oke'];
        return $result;
    }
}
