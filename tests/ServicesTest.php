<?php
namespace Simpus\Apps;

use PHPUnit\Framework\TestCase;
use TriviaService;
use WilayahKabSemarangService;

// TODO: all service must be test
class ServicesTest extends TestCase
{

  public function testTrivia(): void
  {
    $api = new TriviaService();

    Middleware::setMiddleware(array (
        "auth" => array(
          "token"                 => 'unitTest',
          "login"                 => true,
          "user_name"             => 'unitTest',
          "display_name"          => 'unitTest',
          "display_picture_small" => 'unitTest'
      )
    ));

    $data_quest = $api->Get_Ques(array())['status'];    
    $this->assertEquals('ok', $data_quest);
    
    // ERROR: Cant modify header information
    $data_delete = $api->Delete_Ques(0)['success_deleted'];
    $this->assertFalse($data_delete);

    $data_submit = $api->Submit_Ques(array(
      'author' => 'x',
      'level' => '',
      'quest' => 'x'
    ))['error'];
    $this->assertIsArray($data_submit);
    $this->assertEquals($data_submit, array (
      'author' => 'Bagian Author memiliki sedikitnya 3 karakter',
      'level' => 'Bagian Level harus diisi',
      'quest' => 'Bagian Quest memiliki sedikitnya 6 karakter'
    ));

    $data_Answer = $api->Get_Answer(array(
      'question_id' => 1,
      'question_answer' => 1
    ))['status'];    
    $this->assertEquals('ok', $data_Answer);
  }
  
  public function testServiceWilayahKabupatenSemarang(): void
  {
    $api  = new WilayahKabSemarangService();
    
    $data_kabupaten = $api->Data_Kabupaten()['status'];
    $this->assertEquals('ok', $data_kabupaten);
    
    $data_desa = $api->Data_Desa(array())['status'];
    $this->assertEquals('ok', $data_desa);

    $data_kecamatan = $api->Data_Kecamatan(array())['status'];
    $this->assertEquals('ok', $data_kecamatan);
  }
}
