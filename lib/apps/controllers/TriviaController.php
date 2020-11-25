<?php

use Model\Trivia\Trivia;
use Simpus\Apps\Controller;
use System\File\UploadFile;

class TriviaController extends Controller
{
  public function submit()
  {
    $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
    
    // upload image
    $fileSize = $_FILES['quest_img']['size'] ?? 0;
    $isUploaded = $fileSize ==  0 ? true : false;
    if (! $isUploaded) {
      // upload file jika file tersedia
      $file_name = 'quest_image_' . time();
      $upload_image = new UploadFile($_FILES['quest_img']);
      $slug = $upload_image
        ->setFileName($file_name)
        ->setFolderLocation('/data/img/trivia/asset/')
        ->setMimeTypes(array ('image/jpg', 'image/jpeg', 'image/png'))
        ->setMaxFileSize( 562500 )
        ->upload();
      $isUploaded = $upload_image->Success();
    }
    
    $error = array();
    if (isset($_POST['sumbit']) && $isUploaded) {
      $trivia = new TriviaService();
      $respone = $trivia->Submit_Ques(array_merge($_POST, array('quest_img' => $slug ?? '')));
      $error = $respone['error'];
      if ($error === true) {
        $msg['show'] = true;
        $msg['type'] = 'success';
        $msg['content'] = 'Berhasil disimpan';        
      } else {
        $msg['show'] = true;
        $msg['type'] = 'danger';
        $msg['content'] = 'Cek Kembali Data Anda';
        // delete file upload
        if (isset($upload_image)) {
          $upload_image->delete($slug);
        }
      }
    } elseif (! $isUploaded) {
      $msg['show'] = true;
      $msg['type'] = 'danger';
      $msg['content'] = 'Gambar gagal diupload';
      $error = array($upload_image->getError());
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
