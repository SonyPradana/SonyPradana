<?php
/**
 * TODO tambah fiture remove duplivcate by xxx (id_hash)
 */
class PosyanduRecords extends QueryBuilder{
    private $_options = ["imperssion" => [":", ""], "operator"   => "="];

    public function filtterById( $val){
        $this->_FILTERS['id_hash']  = [
            "value"     => $val,
            "option"    => $this->_options,
            "type"      => PDO::PARAM_STR
        ];

        return $this;
    }

    public function filtterByAlamat(int $val){
        $this->_FILTERS['tempat_pemeriksaan']  = [
            "value"     => $val,
            "option"    => $this->_options,
            "type"      => PDO::PARAM_INT
        ];
        return $this;
    }

    public function __construct($PDO = null){
        $this->_TABELS[] = 'data_posyandu';

        // contoh penambahan groub filter
        $this->_GROUP_FILTER = [
           "costume-grub" => [
                "filters" => [
                    "jenis_pemeriksaan" => [
                        "value" => "posyandu",
                        "option" => $this->_options,
                        "type" => null
                    ],
                    "tenaga_pemeriksaan" => [                        
                        "value" => "angger",
                        "option" => $this->_options,
                        "type" => null
                    ]
                ],
                "strict" => true
            ]
        ];

        if( $PDO == null){
            $this->PDO = new MyPDO();
        }else{
            $this->PDO = $PDO;
        }
    }

    public function CountID(){
        if( $this->PDO == null) return [];                              
        $whereStatment = $this->getWhere();// WHERE $whereStatment

        $this->PDO->query("SELECT
                            COUNT(`id`) AS jumlah_kunjungan, `id_hash` 
                         FROM
                            `data_posyandu`
                         GROUP BY 
                            `id_hash`");
        
        foreach( $this->mergeFilters() as $filters){
            foreach( $filters['filters'] as $key => $val){
                if( isset( $val['value']) && $val['value'] != ''){
                    $type = $val['type'] ?? null;
                    $this->PDO->bind(":" . $key, $val['value'], $type);
                }
            }
        }
        return $this->PDO->resultset();
    }
}
