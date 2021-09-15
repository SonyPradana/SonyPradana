<?php

use Convert\Converter\ConvertCode;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use System\Database\MyPDO;
use Model\QuestionAnswer\ask;
use Model\QuestionAnswer\asks;
use Simpus\Apps\Controller;
use System\Database\MyQuery;
use Provider\Session\Session;

class QuestionAnswerController extends Controller
{
    protected $PDO;
    public function __construct(MyPDO $PDO = null)
    {
      $this->PDO = $PDO ?? MyPDO::getInstance();
    }

    public function index()
    {
      $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
      $error = array();

      return $this->view('question-answer/index', array (
        "auth"          => Session::getSession()['auth'],
            "DNT"           => Session::getSession()['DNT'],
            "redirect_to"   => $_GET['redirect_to'] ?? '/',
            "meta"          => array (
              "title"         => "Forum Tanya Jawab - Simpus Lerep",
              "discription"   => "Forum diskusi, Diskusi pelayanan keseahatan di puskesmas lerep",
              "keywords"      => "QnA, Q&A, Question Answer, Tanya Jawab, Forum, Diskusi"
            ),
            "header"        => array (
                "active_menu"   => 'null',
                "header_menu"   => MENU_MEDREC
            ),
            "contents" => array (

            ),
            'error' => $error,
            "message" => array (
                "show"      => $msg['show'],
                "type"      => $msg['type'],
                "content"   => $msg['content']
            )
      ));
    }

    public function thread($request)
    {
      $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
      $error = array();

      $ask = new ask($this->PDO);
      $ask->setID($request);
      if (! $ask->isExist()) {
        DefaultController::page_404(array('path' => '/QnA/thread/' . $request));
      }
      $ask->read();

      $quest = $ask->getAll();
      $quest['date_creat'] = date('d M Y', $quest['date_creat']);
      $vote = $quest['like_post'] - $quest['dislike_post'];
      $quest['vote'] = $vote < 0 ? 0 : $vote;

      $answer = new asks($this->PDO);
      $answerAll = $answer
        ->fillterPerentID($request)
        ->sortByNewst(asks::SORTORDER_ASC)
        ->result();

      $db = new MyQuery($this->PDO);
      foreach ($answerAll as $key => $val) {
        $childs = $db
          ->table('public_quest')
          ->select()
          ->equal('perent_id', $val['id'])
          ->all();
        $childsCount = count($childs);
        $answerAll[$key]['childs'] = $childsCount;
      }

      // random captcha key
      $scrfKey = ConvertCode::RandomCode(5);

      // captcha builder
      $parseBuilder = new PhraseBuilder(5, 'ABCDEFGHIJKLMNOPQRSTU');
      $captcha = new CaptchaBuilder(null, $parseBuilder);
      $captcha
        ->setBackgroundColor(255, 255, 255)
        ->setMaxBehindLines(1)
        ->setMaxFrontLines(1)
        ->build(200, 70);

      // storege captcha to db
      $db = new MyQuery($this->PDO);
      $db('scrf_protection')
        ->insert()
        ->value('id', '')
        ->value('scrf_key', $scrfKey)
        ->value('secret', $captcha->getPhrase())
        ->value('hit', 1)
        ->execute();

      return $this->view('question-answer/thread', array (
        "auth"          => Session::getSession()['auth'],
        "DNT"           => Session::getSession()['DNT'],
        "redirect_to"   => $_GET['redirect_to'] ?? '/',
        "meta"          => array (
          "title"         => $quest['title'] . " - QnA simpus lerep",
          "discription"   => "Forum diskusi, Diskusi pelayanan keseahatan di puskesmas lerep",
          "keywords"      => "QnA, Q&A, Question Answer, Tanya Jawab, Forum, Diskusi"
        ),
        "header"        => array (
          "active_menu"   => 'null',
          "header_menu"   => MENU_MEDREC
        ),
        "contents" => array (
          'perent' => $quest,
          'answers' => $answerAll,
          'scrf_key' => $scrfKey,
          'captcha_image' => $captcha->inline(),
        ),
        'error' => $error,
        "message" => array (
          "show"      => $msg['show'],
          "type"      => $msg['type'],
          "content"   => $msg['content']
        )
      ));
    }

    public function ask()
    {
      $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
      $error = array();

      // random captcha key
      $scrfKey = ConvertCode::RandomCode(5);

      // captcha builder
      $parseBuilder = new PhraseBuilder(5, 'ABCDEFGHIJKLMNOPQRSTU');
      $captcha = new CaptchaBuilder(null, $parseBuilder);
      $captcha
        ->setBackgroundColor(255, 255, 255)
        ->setMaxBehindLines(1)
        ->setMaxFrontLines(1)
        ->build(200, 70);

      // storege captcha to db
      $db = new MyQuery($this->PDO);
      $db('scrf_protection')
        ->insert()
        ->value('id', '')
        ->value('scrf_key', $scrfKey)
        ->value('secret', $captcha->getPhrase())
        ->value('hit', 1)
        ->execute();

      return $this->view('question-answer/ask', array (
        "auth"          => Session::getSession()['auth'],
        "DNT"           => Session::getSession()['DNT'],
        "redirect_to"   => $_GET['redirect_to'] ?? '/',
        "meta"          => array (
          "title"         => "QnA - simpus lerep",
          "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
          "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
        ),
        "header"        => array (
          "active_menu"   => 'null',
          "header_menu"   => MENU_MEDREC
        ),
        "contents" => array (
          'scrf_key' => $scrfKey,
          'captcha_image' => $captcha->inline(),
          'isPerent' => false,
          'perent_id' => '',
          'perent_content' => '',
          'perent_author' => '',
        ),
        'error' => $error,
        "message" => array (
          "show"      => $msg['show'],
          "type"      => $msg['type'],
          "content"   => $msg['content']
          )
      ));
    }

    public function answer($question_id)
    {
      $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
      $error = array();

      // id check
      $ask = new ask();
      $ask->setID($question_id);
      if (! $ask->isExist()) {
        DefaultController::page_404(array());
      }
      $ask->read();

      // random captcha key
      $scrfKey = ConvertCode::RandomCode(5);

      // captcha builder
      $parseBuilder = new PhraseBuilder(5, 'ABCDEFGHIJKLMNOPQRSTU');
      $captcha = new CaptchaBuilder(null, $parseBuilder);
      $captcha
        ->setBackgroundColor(255, 255, 255)
        ->setMaxBehindLines(1)
        ->setMaxFrontLines(1)
        ->build(200, 70);

      // storege captcha to db
      $db = new MyQuery($this->PDO);
      $db('scrf_protection')
        ->insert('scrf_protection')
        ->value('id', '')
        ->value('scrf_key', $scrfKey)
        ->value('secret', $captcha->getPhrase())
        ->value('hit', 1)
        ->execute();

      return $this->view('question-answer/ask', array (
        "auth"          => Session::getSession()['auth'],
        "DNT"           => Session::getSession()['DNT'],
        "redirect_to"   => $_GET['redirect_to'] ?? '/',
        "meta"          => array (
          "title"         => "QnA - simpus lerep",
          "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
          "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
        ),
        "header"        => array (
          "active_menu"   => 'null',
          "header_menu"   => MENU_MEDREC
        ),
        "contents" => array (
          'scrf_key' => $scrfKey,
          'captcha_image' => $captcha->inline(),
          'isPerent' => true,
          'perent_id' => $question_id,
          'perent_title' => $ask->getTitle(),
          'perent_author' => $ask->getAuthor(),
        ),
        'error' => $error,
        "message" => array (
          "show"      => $msg['show'],
          "type"      => $msg['type'],
          "content"   => $msg['content']
          )
      ));
    }

}
