<?php
namespace Simpus\Simpus;
/**
 * Class ini mengambil data rekam medis berupa data pribadi dari data base,
 * data berupa data yang telah di endcryption
 * 
 * @author sonypradana@gmail.com
 */
class PersonalData{
    private $_uniqID; // uniq id untuk mengakses data 
    private $_NIK;
    private $_NoKK;
    private $_NoJaminan;
    private $_no_tlp;
    // biodata
    private $_jenis_kelamin;
    private $_golongan_darah;
    private $_kategory; // kia-anak, kia-hamil, dan lain-lain
    // data kia - anak & ibu
    private $_list_anak; // dalam array (id)
    private $_list_hamil; // dalam array (id)

}
