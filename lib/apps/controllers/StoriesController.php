<?php

use Model\Stories\Stories;
use Model\Stories\Story;
use Simpus\Apps\Controller;
use System\Database\MyPDO;

class StoriesController extends Controller
{
  public function index()
  {
    $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
    $error = array();

    return $this->view('stories/index', array (
      "auth"          => $this->getMiddleware()['auth'],
          "DNT"           => $this->getMiddleware()['DNT'],
          "redirect_to"   => $_GET['redirect_to'] ?? '/',
          "meta"          => array (
            "title"         => "Simpus Stories",
            "discription"   => "Lihat Apa Yang Saat Ini Terjadi di Puskesmas Lerep",
            "keywords"      => "simpus lerep, puskesmas lerep, stories, galery, upload"
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

  public function preview(string $storyID)
  {
    $PDO = new MyPDO();
    $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
    $error = array();

    $story = new Story($PDO);
    $story->setID($storyID);
    $storyExist = $story->isExist();
    if ($storyExist && $story->read()) {
      $imageID = $story->getImageID();
      $caption = $story->getCaption();
      $story->setViewer($story->getViewer() + 1);
      $story->update();
    }

    // before and after
    $storyBefore = new Story($PDO);
    $storyBefore->setID($storyID + 1);
    $storyBeforeExist = $storyBefore->isExist();
    if ($storyBeforeExist && $storyBefore->read()) {
      $imageIDBefore = $storyBefore->getImageID();
      $captionBefore = $storyBefore->getCaption();
    }
    $storyAfter = new Story($PDO);
    $storyAfter->setID($storyID - 1);
    $storyAfterExist = $storyAfter->isExist();
    if ($storyAfterExist && $storyAfter->read()) {
      $imageIDAfter = $storyAfter->getImageID();
      $captionAfter = $storyAfter->getCaption();
    }

    return $this->view('stories/preview', array (
      "auth"          => $this->getMiddleware()['auth'],
        "DNT"           => $this->getMiddleware()['DNT'],
        "redirect_to"   => $_GET['redirect_to'] ?? '/',
        "meta"          => array (
          "title"         => "Story Preview",
          "discription"   => "Lihat Apa Yang Saat Ini Terjadi di Puskesmas Lerep",
          "keywords"      => "simpus lerep, puskesmas lerep, stories, galery, upload"
        ),
        "header"        => array (
          "active_menu"   => 'null',
          "header_menu"   => MENU_MEDREC
        ),
        "contents" => array (
          'exist' => $storyExist,
          'imageID' => $imageID ?? '',
          'caption' => $caption ?? '',
          'storiesCount' => 3,
          // other,
          'beforeExist' => $storyBeforeExist,
          'imageBefore' => $imageIDBefore ?? '',
          'captionBefore' => $captionBefore ?? '',
          'afterExist' => $storyAfterExist,
          'imageAfter' => $imageIDAfter ?? '',
          'captionAfter' => $captionAfter ?? '',
        ),
        'error' => $error,
        "message" => array (
          "show"      => $msg['show'],
          "type"      => $msg['type'],
          "content"   => $msg['content']
        )
    ));
  }

  public function roll(string $groupUploader)
  {
    $msg = array('show' => false, 'type' => 'info', 'content' => 'oke');
    $error = array();
    $strories = new Stories();
    $strories->filterByUploader($groupUploader);

    $data = $strories->result();
    $countData = count($data);
    $oddStories = $countData % 2 === 0 ? true : false;

    return $this->view('stories/roll', array (
      "auth"          => $this->getMiddleware()['auth'],
        "DNT"           => $this->getMiddleware()['DNT'],
        "redirect_to"   => $_GET['redirect_to'] ?? '/',
        "meta"          => array (
          "title"         => "Story Preview",
          "discription"   => "Lihat Apa Yang Saat Ini Terjadi di Puskesmas Lerep",
          "keywords"      => "simpus lerep, puskesmas lerep, stories, galery, upload"
        ),
        "header"        => array (
          "active_menu"   => 'null',
          "header_menu"   => MENU_MEDREC
        ),
        "contents" => array (
          'exist'   => true,
          'stories' => $data,
          'storiesCount' => $countData,
          'isOdd'   => $oddStories,
          'fristItem' => true
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
