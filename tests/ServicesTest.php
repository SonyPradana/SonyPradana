<?php

namespace Simpus\Tests;

use AntrianPoliService;
use AuthService;
use CovidKabSemarangService;
use JadwalPelayananService;
use JadwalVaksinService;
use MessageService;
use Model\Stories\Story;
use NewsFeederService;
use PHPUnit\Framework\TestCase;
use QAResponseService;
use QuestionAnswerService;
use RekamMedisService;
use System\Database\MyPDO;
use TriviaService;
use WilayahKabSemarangService;
use Simpus\Apps\Middleware;
use StoriesService;
use System\Database\MyQuery;

final class ServicesTest extends TestCase
{
  private function PDO(): MyPDO
  {
    return MyPDO::getInstance('test_simpus_lerep');
  }

  protected array $middleware = array (
    "auth" => array(
      "token"                 => 'unitTest',
      "login"                 => true,
      "user_name"             => 'unitTest',
      "display_name"          => 'unitTest',
      "display_picture_small" => 'unitTest'
    ),
    'DNT' => false
  );

  public function testMypdo(): void
  {
    $this->assertEquals($this->PDO(), MyPDO::getInstance('test_simpus_lerep'));
  }

  public function testServiceAntrian(): void
  {
    Middleware::setMiddleware($this->middleware);
    $api = new AntrianPoliService($this->PDO());

    // success

    // success antrian
    $data_antrian = $api->antrian(array());
    $this->assertEquals('ok', $data_antrian['status']);
    $this->assertNotEmpty($data_antrian['last']);
    $this->assertNotEmpty($data_antrian['data']);

    // success baru
    $data_baru = $api->baru(
      array(
        'poli' => 'A',
        'antrian' => 1
      )
    );
    $this->assertEquals('ok', $data_baru['status']);

    // success dipaggil
    $data_dipanggil = $api->dipanggil(
      array(
        'poli' => 'A',
        'antrian' => 1
      )
    );
    $this->assertEquals('ok', $data_dipanggil['status']);

    // success reset
    $data_reset = $api->reset(
      array(
        'poli' => 'full_reset'
      )
    );
    $this->assertEquals('ok', $data_reset['status']);
  }

  // ERROR: cant modife middleware
  public function testServiceAuth(): void
  {
    Middleware::setMiddleware($this->middleware);
    $api = new AuthService();

    // success auth
    $data = $api->login_status(array());
    $this->assertEquals('ok', $data['status']);

    // not login auth
    // $data = $api->login_status(array());
    // $this->assertEquals('not login', $data['status']);

    // session end
    // $data = $api->login_status(array());
    // $this->assertEquals('Session end', $data['status']);
  }

  public function testServiceCovidKabSemarang(): void
  {
    $api = new CovidKabSemarangService($this->PDO());

    // success

    // success daftar kecamatan
    $data_kecamatan = $api->daftar_kecamatan(array());
    $this->assertNotEmpty($data_kecamatan['data']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data_kecamatan['headers']);

    // success ferch
    $data_fetch = $api->fetch(
      array (
        'kecamatan' => 'ungaran-barat'
      )
    );
    $this->assertEquals('ok', $data_fetch['status']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data_fetch['headers']);

    // success info
    $data_info = $api->info(array());
    $this->assertEquals('ok', $data_info['status']);

    // success track date
    $data_date = $api->track_record(array());
    $this->assertIsArray($data_date['data']);

    // success tracker
    $data_tracker = $api->tracker(array());
    $this->assertEquals('ok', $data_tracker['status']);

    // success track all
    $data_trackAll = $api->tracker_all(array());
    $this->assertNotEmpty($data_trackAll['data']);

    // succcess track data
    $data_trackData = $api->tracker_data(array());
    $this->assertEquals('ok', $data_trackData['status']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data_trackData['headers']);

    //  failed

    // faild ferch
    $data_fetch = $api->fetch(
      array (
        'kecamatan' => 'unit-test'
      )
    );
    $this->assertEquals('Bad Request', $data_fetch['status']);
    $this->assertEquals(['HTTP/1.1 400 Bad Request'], $data_fetch['headers']);

   // faild tracker
    $data_tracker = $api->tracker(
      array (
        'range_waktu' => 0
      )
    );
    $this->assertEquals('Bad Request', $data_tracker['status']);

    // faild track data
    $data_trackData = $api->tracker_data(
      array (
        'range_waktu' => 0
      )
    );
    $this->assertEquals('Bad Request', $data_trackData['status']);
    $this->assertEquals(['HTTP/1.1 400 Bad Request'], $data_trackData['headers']);
  }

  public function testServiceJadwalPelayanan(): void
  {
    $api = new JadwalPelayananService($this->PDO());

    // success
    $data = $api->Imunisasi(array());
    $this->assertNotEmpty($data['data']);

    // failed
    $data = $api->Imunisasi (
      array (
        'month' => 3,
        'year' => 1994
      )
    );
    $this->assertEmpty($data['data']['data']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data['headers']);
  }

  public function testServiceMessage(): void
  {
    // setup midleware for simulation login
    Middleware::setMiddleware($this->middleware);
    $api = new MessageService($this->PDO());

    // success
    $data = $api->read(array());
    $this->assertNotEmpty($data['data']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data['headers']);
  }

  public function testServiceNewsReader(): void
  {
    $api = new NewsFeederService($this->PDO());

    // success
    $data = $api->ResendNews(array());
    $this->assertEquals('ok', $data['status']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data['headers']);
  }

  public function testServiceTrivia(): void
  {
    // setup midleware for simulation login
    Middleware::setMiddleware($this->middleware);
    $api = new TriviaService($this->PDO());

    // success assert test

    // success get quest
    $data_quest = $api->Get_Ques(array());
    $this->assertEquals('ok', $data_quest['status']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data_quest['headers']);

    // success submit data
    $data_submit = $api->Submit_Ques(
      array (
        'author'  => 'test',
        'level'   => '1',
        'quest'   => 'is success',
        'quest_img' => '',
        'option'  => array(
          'answer_1' => 'one',
          'answer_2' => 'two',
          'answer_3' => 'three'
        )
      )
    );
    $this->assertEquals('ok', $data_submit['status']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data_submit['headers']);

    // success delete quest
    $last_successID = array('id' => $data_submit['info']['last_id']);
    $data_delete = $api->Delete_Ques($last_successID);
    $this->assertFalse(! $data_delete['success_deleted']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data_delete['headers']);

    // success get answer
    $data_answer = $api->Get_Answer(array(
      'question_id' => 1,
      'question_answer' => 1
    ));
    $this->assertEquals('ok', $data_answer['status']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data_answer['headers']);

    // failed assert test

    // failed deleted data
    $data_delete = $api->Delete_Ques(array('id' => 0))['success_deleted'];
    $this->assertFalse($data_delete);

    // failed submit data
    $data_submit = $api->Submit_Ques(array(
      'author' => 'x',
      'level' => '',
      'quest' => 'x'
    ));
    $this->assertEquals('error', $data_submit['status']);
  }

  public function testServiceRekamMedis(): void
  {
    // setup midleware for simulation login
    Middleware::setMiddleware($this->middleware);
    $api = new RekamMedisService($this->PDO());

    // success feact API

    // success search
    $data_search = $api->search (
      array (
        'main-search' => 'Agus Arif Afandi',
        'nomor-rm-search' => '008701',
        'alamat-search' => 'Bandarjo',
        'no-rt-search' => '8',
        'no-rw-search' => '8',
        'nama-kk-search' => 'Agus Arif Afandi',
        'nomor-rm-kk-search' => '008701',
        'strict-search' => true
      )
    );
    $this->assertEquals('ok', $data_search['status']);

    // success nomor kk
    $data_nomorKK = $api->search_nomor_rm_kk (
      array (
        'n' => 'Agus Arif Afandi',
        'a' => 'Bandarjo',
        'r' => '8',
        'w' => '8',
      )
    );
    $this->assertEquals('ok', $data_nomorKK['status']);

    // success search rm
    $data_searchRm = $api->search_rm (
      array (
        'nomor_rm' => '008701'
      )
    );
    $this->assertEquals('ok', $data_searchRm['status']);

    // success validate rm
    $data_validRm = $api->valid_nomor_rm (
      array (
        'nr' => '008701'
      )
    );
    $this->assertEquals('ok', $data_validRm['status']);

    // failed feact API

    // failed search
    $data_search = $api->search (
      array (
        'main-search' => 'unit test',
        'nomor-rm-search' => '99999',
        'alamat-search' => 'unit test',
        'no-rt-search' => '99',
        'no-rw-search' => '99',
        'nama-kk-search' => 'unit test',
        'nomor-rm-kk-search' => '999999',
        'strict-search' => true
      )
    );
    $this->assertEquals('no content', $data_search['status']);

    // failed nomor kk
    $data_nomorKK = $api->search_nomor_rm_kk (
      array (
        'n' => 'unit test',
        'a' => 'unit test',
        'r' => '99',
        'w' => '99',
      )
    );
    $this->assertEquals('no content', $data_nomorKK['status']);

    // failed search rm
    $data_searchRm = $api->search_rm (
      array (
        'nomor_rm' => '99999'
      )
    );
    $this->assertEquals('no content', $data_searchRm['status']);

    // failed valid rm
    $data_validRm = $api->valid_nomor_rm (
      array (
        'nr' => '999999'
      )
    );
    $this->assertEquals('no content', $data_validRm['status']);
  }

  public function testServiceWilayahKabupatenSemarang(): void
  {
    $api  = new WilayahKabSemarangService($this->PDO());

    $data_kabupaten = $api->Data_Kabupaten();
    $this->assertEquals('ok', $data_kabupaten['status']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data_kabupaten['headers']);

    $data_desa = $api->Data_Desa(array());
    $this->assertEquals('ok', $data_desa['status']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data_desa['headers']);

    $data_kecamatan = $api->Data_Kecamatan(array());
    $this->assertEquals('ok', $data_kecamatan['status']);
    $this->assertEquals(['HTTP/1.1 200 Oke'], $data_kecamatan['headers']);
  }

  public function testServiceStories(): void
  {
    $pdo = new MyPDO('test_simpus_lerep');
    $api = new StoriesService($pdo);
    $story = new Story($pdo);
    $story->setID(0)->read();
    $viewerBefore = $story->getViewer();

    // test add viewer
    $respone = $api->Add_Viewer(array('stories_id' => 0));
    $this->assertGreaterThan($viewerBefore, $respone['data']['viewer']);

    // test group stories
    $respone = $api->Group_Story(array());
    $this->assertNotEmpty($respone['data']);

    // test roll stories
    $respone = $api->Rolling(array('group_name' => 'dev'));
    $this->assertNotEmpty($respone['data']);

    // test stories
    $respone = $api->Stories(array());
    $this->assertNotEmpty($respone['data']);

    // test upload
    $respone = $api->Upload(
      array (
        'caption' => 'phpunit',
        'uploader' => 'dev',
        'end' => time() + 8240,
        'files' => array (
          'upload_stories' => array (
            'name' => '0.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => __DIR__ . '\asset\0.jpg',
            'error' => 0,
            'size'  => 18_979,
          )
        )
      )
    );
    // skip test conditon couse file distenation cant be rich
    // $this->assertEquals('ok', $respone['status']);
  }

  public function testResponeUser(): void
  {
    $test = new QAResponseService($this->PDO());

    // Like test respone
    $res = $test->Like(['thread_id' => 1]);
    $this->assertIsInt($res['data']['vote']);

    // Dislike test respone
    $res = $test->Dislike(['thread_id' => 1]);
    $this->assertIsInt($res['data']['vote']);
  }

  public function testQuestionAndAnswer(): void
  {
    $test = new QuestionAnswerService($this->PDO());

    $res = $test->get_post(array());
    $this->assertNotEmpty($res['data']);

    // creating scrf to pass the scrf protactin
    $scrfKey = 'key';
    $scrfSecret = 'secret';
    $db = new MyQuery($this->PDO());
    $db('scrf_protection')
      ->insert()
      ->value('id', '')
      ->value('scrf_key', $scrfKey)
      ->value('secret', $scrfSecret)
      ->value('hit', 10)
      ->execute();

    $request = array (
      'x-method' => 'PUT',
      'scrf_key'    => $scrfKey,
      'scrf_secret' => $scrfSecret,
      'name'        => 'Lorem ipsum dolor',
      'perent_id'   => 1,
      'title'       => 'Lorem ipsum dolor sit amet',
      'content'     => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Rerum, eveniet',
      'tag'         => 'Lorem, ipsum'
    );
    $res = $test->submit_post($request);
    $this->assertNotEmpty($res['data']);
    $this->assertEquals($res['status'], 'ok');
  }

  // public function testAdminUserResgister():void
  // {
  //   // panding
  // }

  public function testJadwalVaksin(): void
  {
    $test = new JadwalVaksinService($this->PDO());
    $res = $test->lansia([]);

    $this->assertNotEmpty($res['data']);
  }
}
