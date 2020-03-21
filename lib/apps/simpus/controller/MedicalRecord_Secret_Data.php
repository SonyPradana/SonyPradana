<?php
/**
 * Class pembantu untuk menampung data pribadi pasien
 * 
 * @author sonypradana@gmail.com 
 */
class MedicalRecord_Secret_Data extends MedicalRecord{
    /** @var string nomor indok keluarga */
    public $NIK;
    /** @var string Nomor Bpjs */
    public $BPJS; 
    
    public function __construct($No_RM){
        
    }
}
