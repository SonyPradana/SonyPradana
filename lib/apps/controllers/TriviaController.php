<?php

use Model\Trivia\Trivia;
use Simpus\Apps\Controller;

class TriviaController extends Controller
{
  public function submit()
  {
    $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
    
    $error = array();
    if (isset($_POST['sumbit'])) {
      $trivia = new TriviaService();
      $respone = $trivia->Submit_Ques($_POST);
      $error = $respone['error'];
      if ($error === true ) {
        $msg['show'] = true;
        $msg['type'] = 'success';
        $msg['content'] = 'Berhasil disimpan';
      } else {        
        $msg['show'] = true;
        $msg['type'] = 'danger';
        $msg['content'] = 'Cek Kembali Data Anda';
      }
    }

    return $this->view('/trivia/submit', array (
      'auth' => $this->getMiddleware()['auth'],
      'meta' => array (
        'title' => 'submit pertanyaan Anda',
        'discription' => 'Submit pertanyan dari Anda untuk dibagikan kepada yang lain',
        'keywords' => 'simpus, trivia, kuis, quest, pertanyaan, kirim, kontribusi'
      ),
      'header' => array (
        'acrive_menu' => 'home',
        'header_menu' => $_SESSION['active_menu'] ?? MENU_MEDREC
      ),
      'contents' => array (

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
