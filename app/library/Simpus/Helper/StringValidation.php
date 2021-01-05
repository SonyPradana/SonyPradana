<?php

namespace Simpus\Helper;

class StringValidation{

    public static function UserValidation($string, $min = '', $max = ''){
        $pattern =  sprintf(  '/^[A-Za-z]{1}[A-Za-z0-9]{%u,%u}$/' , $min, $max);
        if( preg_match($pattern, $string) ){
            return true;
        }

        return false;
    }
    
    public static function EmailValidation($string){                
        $pattern = '/^(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))$/iD';
        if( preg_match($pattern, $string) ) {
            return true;
        }else{
            return false;
        }
    }

    public static function NumberValidation($string, $min = 1, $max = 9){      
        $max = ( $max < $min ) ? $max = $min : $max;
        // $pattern = '/^[0-9]*$/';
        $pattern = sprintf( '/^[0-9]{%u,%u}$/' , $min, $max );

        if( preg_match($pattern, $string) ) {
            return true;
        }

        return false;
    }

    public static function DateValidation($string, $delimiter = '/'){
        if ($delimiter == '/'){ # pimasah menggunkan / 
            $pattern =  '/^((0|1)\d{1})\/((0|1|2)\d{1})\/((19|20)\d{2})/';
        }elseif ($delimiter == '-'){ # pemisahmenggunakan -
            $pattern =  '/^((0|1)\d{1})-((0|1|2)\d{1})-((19|20)\d{2})/';
        }else{ #pemisah tidak di gunakan nilainya otomatis false
            return false;
        }

        # cek pattern
        if( preg_match($pattern, $string) ){
            return true;
        }
        # nilai defaultnya
        return false;
    }

    public static function NoHtmlTagValidation($string){
        $pattern = '/<[^>]*>/';
        if( preg_match($pattern, $string) ) {
            return false;
        }else{
            return true;
        }
    }

    /**
     *  verifikasi password yang baik
     * 
     * terdiri dari:
     * - panjang minimal 8 digit
     * - tidak boleh angka semua
     * - tidak boleh ada  `' " < >
     */
    public static function GoodPasswordValidation($string){
        # panjang minimal 8 digit
        $len = strlen($string);
        if ( $len < 8 ) return false;
        # tidak boleh angka semua
        if ( is_numeric($string) ) return false;        

        # tidak boleh ada tanda baca '"~`<>
        // $pattern = '/[~`]/' . "'<>]/";
        if( preg_match('/[~<>`"]/', $string)
        || preg_match("/[~<>`']/", $string)) {
            return false;
        }else{
            return true;
        }

        return false;
    }


    public static function PlanTextValidation($string){
        $pattern =  '/^[_A-z0-9]*((-|\s)*[_A-z0-9])*$/';
        if( preg_match($pattern, $string) ){
            return false;
        }

        return true;
    }
}
