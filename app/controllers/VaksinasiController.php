<?php

use Simpus\Apps\Controller;
use Provider\Session\Session;

class VaksinasiController extends Controller
{

  public function index()
  {
    $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
    $error = array();
    $author = new Simpus\Auth\User("angger");

    return $this->view('Vaksinasi/Vaksinasi', array (
      "auth"          => Session::getSession()['auth'],
      "DNT"           => Session::getSession()['DNT'],
      "redirect_to"   => $_GET['redirect_to'] ?? '/',
      "meta"          => array (
        "title"         => "Informasi Kuota Vaksin Puskesmas Lerep",
        "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
        "keywords"      => "simpus lerep, puskesmas lerep, vaksinasi covid, kuota vaksin, vaksin ungran"
      ),
      "header"        => array (
        "active_menu"   => 'null',
         "header_menu"   => MENU_MEDREC
      ),
      "contents" => array (
        "article" => [
          "display_name" => $author->getDisplayName(),
          "display_picture_small" => $author->getSmallDisplayPicture(),
          'article_create' => date("d M Y"),
          'title' => 'Informasi Kuota Vaksin Puskesmas Lerep'
        ],
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
