<?php

use Simpus\Apps\Controller;
use Model\Article\articleModel;
use Simpus\Apps\Cache;
use Simpus\Auth\User;
use Provider\Session\Session;

class ArticleController extends Controller
{
  public function articleNotFound($path)
  {
    DefaultController::page_404(array('path' => '/read/'.$path));
  }

  public function index(string $articleID)
  {
    $result = Cache::remember(
      'ArticleController' . $articleID,
      86400, // caching 1 day
      function() use ($articleID) {
        $read_article = new articleModel();
        $read_article->selectColomn(['*']);
        $read_article->filterURLID($articleID);
        $result = $read_article->result()[0] ?? null;

        return $result;
    });

    if ($result == null ) $this->articleNotFound($articleID);
    if ($result['status'] == 'draft') $this->articleNotFound($articleID);

    $author = new User($result['author']);
    $selisih_waktu = time() - $result['update_time'];
    $format_tanggal = $selisih_waktu < 86400
      ? date('h:i:sa',  $result['update_time'])
      : date('d M Y',  $result['update_time']);

    return $this->view('article/index', [
      "auth"    => Session::getSession()['auth'],
      "meta"     => [
        "title"         => $result['title'],
        "discription"   => $result['discription'],
        "keywords"      => $result['keywords'],
        "css"           => $result['css'],
        "js"            => $result['js']
      ],
      "header"   => [
        "active_menu"   => 'home',
        "header_menu"   => $_SESSION['active_menu'] ?? MENU_MEDREC
      ],
      "contents" => [
        'article'   => [
          "display_name"          => $author->getDisplayName(),
          "display_picture_small" => $author->getSmallDisplayPicture(),
          'title'                 => $result['title'],
          'article_date'          => $format_tanggal,
          'media_type'            => $result['update_time'],
          'image_url'             => $result['image_url'],
          'image_alt'             => $result['image_alt'],
          'media_note'            => $result['media_note'],
          'raw_content'           => $result['raw_content']
        ]

      ]
    ]);
  }
}
