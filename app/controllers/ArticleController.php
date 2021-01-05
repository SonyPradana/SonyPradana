<?php

use Simpus\Apps\Controller;
use Model\Article\articleModel;
use Simpus\Auth\User;

class ArticleController extends Controller
{
    public function articleNotFound($path)
    {
        DefaultController::page_404(array('path' => '/read/'.$path));
    }

    public function index(string $articleID)
    {
        $read_article = new articleModel();
        $read_article->selectColomn(['*']);
        $read_article->filterURLID($articleID);
        $result = $read_article->result()[0] ?? null;
        if( $result == null ) $this->articleNotFound($articleID);

        $author = new User($result['author']);
        $selisih_waktu = time() - $result['create_time'];
        $format_tanggal = $selisih_waktu < 86400 ? date('h:i:sa',  $result['create_time'])
            : date('d M Y',  $result['create_time']); 

        return $this->view('article/index', [
            "auth"    => $this->getMiddleware()['auth'],
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
                    'article_create'        => $format_tanggal,
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
