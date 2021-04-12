<?php namespace Model\Antrian;

use System\Database\MyModel;
use System\Database\MyPDO;

class antrianModel extends MyModel
{
    /**
     * @param MyPDO $PDO DataBase class Dependency Injection
     */
    public function __construct(MyPDO $PDO = null)
    {
        $this->_TABELS[]  = 'antrian';
        $this->PDO = $PDO ?? MyPDO::getInstance();
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
