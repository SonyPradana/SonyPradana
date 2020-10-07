<?php

namespace Model;

use Simpus\Database\QueryBuilder;
use \PDO;
use Simpus\Database\MyPDO;

class articleModel extends QueryBuilder
{
    private $_options = ["imperssion" => [":", ""], "operator"   => "="];

    public function filterURLID(string $val)
    {
        $this->_FILTERS['url_id'] = [
            'value'     => $val,
            'option'    => $this->_options,
            'type'      => PDO::PARAM_STR
        ];
        
        return $this;
    }
    public function selectColomn(array $val)
    {
        $this->_COLUMNS = $val;
    }

    public function __construct()
    {
        $this->_TABELS[]  = 'articles';
        $this->PDO = new MyPDO();
    }

    public function rowCount(): int
    {
        $this->PDO->query($this->query());
        foreach( $this->mergeFilters() as $filters) {
            foreach( $filters['filters'] as $key => $val) {
                if( isset( $val['value']) && $val['value'] != '') {
                    $type = $val['type'] ?? null;
                    $this->PDO->bind(":" . $key, $val['value'], $type);
                }
            }
        }
        return $this->PDO->rowCount();
    }

}
