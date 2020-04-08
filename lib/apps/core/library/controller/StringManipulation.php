<?php
/**
 * class ini berfungsi untuk memanipulasi text.
 * mulai dari mengganti text, merubah format text, atau memperrbaiki text
 * 
 * @author sonypradana@gmail.com
 */
class StringManipulation{

    /**
     * Menambahkan tag html di antara text yg akan di tambahkan.
     * 
     * contoh: ini adalah contoh, adalah -- <p>adalah</p> -> ini <p>adalah</p>contoh.
     * 
     * @param string $longString String panjang yang akan di cari
     * @param string $findString String yg akan diganti/ditambahkan tah html
     * @param string $prefix Awalan tag html
     * @param string $surfix Akhiran tag html
     * @return string String yang sudah ditambahkan tag html
     */
    public static function addHtmlTag($longString, $findString, $prefix, $surfix){
        if( is_null($longString) || is_null($findString)) return $longString;

        $pattern = '/(.*)' . $findString . '(.*)/';
        $replacement = '$1' . $prefix . $findString . $surfix .'$2';
        
        return preg_replace($pattern, $replacement, $longString);
    }
}
