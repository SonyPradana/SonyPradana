<?php namespace Simpus\Simpus;

use \PDO;
use System\Database\MyPDO;

class GroupsPosyandu{
    /**
     * @return array ALL
     */
    public static function getPosyanduAll(): array
    {
        $db = new MyPDO();
        $db->query("SELECT * FROM groups_posyandu");
        $res = [];
        if ($db->resultset() > 0) {
            // return $db->resultset();
            foreach ($db->resultset() as $row) {
                $res[ $row['id'] ]  = [
                    "posyandu" => $row['posyandu'],
                    "desa"     => $row['desa']
                ];
            }
        }
        return $res;
    }

    /**
     * @return array id dan nama posyandu
     */
    public static function getPosyandu($desa): array
    {
        $db = new MyPDO();
        $db->query("SELECT  `id`, `posyandu` FROM groups_posyandu WHERE `desa` = :desa");
        $db->bind(':desa', $desa, PDO::PARAM_STR);
        if ($db->resultset() > 0) {
            return $db->resultset();
        }
        return [];
    }

     /**
     * @return int id
     */
    public static function getPosyanduId($desa, $nama_posyandu): int
    {
        $db = new MyPDO();
        $db->query("SELECT * FROM groups_posyandu WHERE `desa` = :desa AND `posyandu` = :posyandu");
        $db->bind(':desa', $desa, PDO::PARAM_STR);
        $db->bind(':posyandu', $nama_posyandu, PDO::PARAM_STR);
        // $res = $db->single();
        if( $db->single() ){
            return (int) $db->single()['id'];
        }
        return 0;
    }

    /**
     * @return string nama posyandu
     */
    public static function getPosyanduName($id): string
    {
        $db = new MyPDO();    
        $db->query("SELECT  `posyandu` FROM groups_posyandu WHERE `id` = :id");
        $db->bind(':id', $id, PDO::PARAM_INT);
        // $res = $db->single();
        if( $db->single() ){
            return (string) $db->single()['posyandu'];
        }
        return '';
    }

    /**
     * @return 
     */
    public static function getPosyanduDesa($id): string
    {
        $db = new MyPDO();
        $db->query("SELECT  `desa` FROM groups_posyandu WHERE `id` = :id");
        $db->bind(':id', $id, PDO::PARAM_INT);
        // $res = $db->single();
        if( $db->single() ){
            return (string) $db->single()['desa'];
        }
        return '';
    }

}
