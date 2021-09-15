<?php

use Simpus\Apps\Controller;
use System\Database\MyPDO;
use Provider\Session\Session;

class RegistrationMRController extends Controller
{
  private $PDO = null;
  public function useAuth()
  {
    if (Session::getSession()['auth']['login'] == false) {
      DefaultController::page_401(array (
        'links' => array (
          array('Login',  '/login?url=' . $_SERVER['REQUEST_URI'])
        )
      ));
    }
  }

  public function __construct()
  {
    if (isset($_GET['active_menu'])) {
      $_SESSION['active_menu'] = MENU_MEDREC;
    }

    $this->PDO = MyPDO::getInstance();

    // auth cek
    $this->useAuth();
  }

  public function index()
  {
    $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
    $error = array();

    return $this->view('PendaftaranRM/MedicalRegistration', array (
      "auth"          => Session::getSession()['auth'],
      "DNT"           => Session::getSession()['DNT'],
      "redirect_to"   => $_GET['url'] ?? null,
      "meta"          => array (
        "title"         => "Pendaftaran RM",
        "discription"   => "Sistem Informasi Manajemen Puskesmas SIMPUS Lerep",
        "keywords"      => "simpus lerep, puskesmas lerep, puskesmas, ungaran, kabupaten semarang"
      ),
      "header"        => array (
        "active_menu"   => 'Pendaftaran',
        "header_menu"   => MENU_MEDREC
      ),
      "contents" => array (),
      'error' => $error,
      "message" => array (
        "show"      => $msg['show'],
        "type"      => $msg['type'],
        "content"   => $msg['content']
      )
    ));
  }
}
