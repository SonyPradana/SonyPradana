<?php
class jadwalKIA{
    private $_month;
    private $_year;

    public function __construct(string $bulan, string $tahun){
        $this->_month = $bulan;
        $this->_year = $tahun;
    }

    public function getdata():array{
        $month = $this->_month;
        $year  = $this->_year;

        $date = [];
        $data = [];
        $first_week = [];   $date_fw = date("Y-m-d", strtotime("first friday 2020-06"));
        $third_week = [];   $date_tw = date("Y-m-d", strtotime("third friday 2020-06"));

        $db = new MyPDO();
        $db->query("SELECT `date`, `event_detail` FROM `list_of_services` WHERE `event`=:event AND MONTH(Date) = :m");
        $db->bind(':event', "imunisasi anak");
        $db->bind(':m', $month);
        foreach( $db->resultset() as $row){
            $data[$row['event_detail']][] = date("d M", strtotime($row['date']));
            $date[] = date("d M", strtotime($row['date']));
            
            if( $date_fw == $row['date']){
                $first_week[] = $row['event_detail'];
            }
            if( $date_tw == $row['date']){
                $third_week[] = $row['event_detail'];
            }
        }

        $date = array_values( array_unique( $date ) );
        
        $result = [
            "version" => "1.0",
            "bulan" => date("M Y", strtotime("$year-$month-1")),
            "jadwal" => $date,
            "jumat pertama" => $first_week,
            "jumat ketiga" => $third_week,
            "data" => $data
        ];

        return $result;
    }

    public static function getAvilabeMonth():array{
        $db = new MyPDO();
        $db->query("SELECT `date` FROM `list_of_services` WHERE `event`=:event");
        $db->bind(':event', 'imunisasi anak');
        $arr = [];
        foreach( $db->resultset() as $row){
            $arr[] = date("m", strtotime($row['date']));
        }
        return array_unique($arr);
    }
}
