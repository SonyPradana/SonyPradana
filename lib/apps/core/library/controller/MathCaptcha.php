<?php
/**
 * clas ini berfungsi untuk membuat simple captcha matematika
 * 
 * @author sonypradana@gamil.com
 */
class MathCaptcha{
    /** @var string hasil jawab benar */
    private $_result;
    /** @var string pertanyaan yg harus dijawab user */
    private $_quest;

    /**
     * membuat captcha baru
     */
    public function __construct(){
      // membuat pertanyan dan jawaban baru
      $this->Captcha();
    }

    /**
     * membuat soal/pertanya sekaligus jawabanya
     */
    private function Captcha(){
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        $equal = 0;
        $operator = '+';
        $selectOperator = rand(1,3);
        if( $selectOperator == 1){
          $num1 = rand(1, 20);
          $num2 = rand(1, 20);
          $operator = ' + ';
          $equal = $num1 + $num2;
        } elseif( $selectOperator == 2){
          $operator = ' - ';
          $equal = $num1 - $num2;
        } else{
          $operator = ' x ';
          $equal = $num1 * $num2;
        }

        $this->_quest = $num1 . $operator . $num2 . ' = ';
        $this->_result = $equal;
    }

    /**
     * menapilkan hasil peryanyan yang telah dibuat sebelumnya.
     * 
     * hasil berupa pertanyaan berbetuk string (human read able)
     * @return string menapilkan pernyataan yang harus dijawab
     */
    public function ChaptaQuest(){
        return $this->_quest;
    }

    /**
     * menampilkan jawaban benar dari pernyataan yang telah dibuat sebelunya
     * 
     * jawaban ini harus sama dengan jawaban yang dijawab user
     * @return string jawab benar dari pertanyaan yang telah dibuat
     */
    public function ChaptaResult(){
        return $this->_result;
    }
}
