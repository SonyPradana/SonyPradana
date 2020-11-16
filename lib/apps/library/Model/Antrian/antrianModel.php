<?php namespace Model\Antrian;

use System\Database\MyModel;
use System\Database\MyPDO;

class antrianModel extends MyModel
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
