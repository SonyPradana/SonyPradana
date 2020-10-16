<?php

namespace Model\Antrian;

use Simpus\Database\QueryBuilder;
use \PDO;
use Simpus\Database\MyPDO;

class antrianModel extends QueryBuilder
{
    public function __construct()
    {
        $this->_TABELS[]  = 'antrian';
        $this->PDO = new MyPDO();
    }

    public function lastUpdate()
    {
        $this->PDO->query(
            "SELECT
                `poli`, `current`
            FROM
                `antrian`
            ORDER BY
                `current_times`
            DESC LIMIT 1
        ");
        $this->PDO->execute();
        return $this->PDO->single();
    }

    
}
