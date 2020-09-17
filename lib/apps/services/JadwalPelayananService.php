<?php

use Simpus\Apps\Middleware;
use Simpus\Services\JadwalKia;

class JadwalPelayananService extends Middleware
{
    public function Imunisasi(array $params) :array
    {
        $month = $params['month'] ?? date('m');
        $year  = $params['year'] ?? date('Y');

        $jadwal = new JadwalKia($month, $year);
        $result = $jadwal->getdata();
        $result['status']   = 'ok';
        $result['headers']  = ['HTTP/1.1 200 Oke'];
        return $result;
    }
}
