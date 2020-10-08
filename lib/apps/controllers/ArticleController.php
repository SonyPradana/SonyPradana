<?php

use Simpus\Apps\Controller;
use Model\articleModel;
use Simpus\Auth\User;

class ArticleController extends Controller
{
    public function articleNotFound()
    {        
        (new DefaultController())->status(404, []);
        exit;
    }

    public function index(string $articleID)
    {        
        $read_article = new articleModel();
        $read_article->selectColomn(['*']);
        $read_article->filterURLID($articleID);
        $result = $read_article->result()[0] ?? null;
        if( $result == null ) $this->articleNotFound();
        $author = new User($result['author']);

        return $this->view('article/index', [
            "auth"    => $this->getMiddleware()['auth'],
            "meta"     => [
                "title"         => $result['title'],
                "discription"   => $result['discription'],
                "keywords"      => $result['keywords']
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
                    'article_create'        => date('Y-m-d h:i:sa', $result['create_time']),
                    'media_type'            => $result['update_time'],
                    'image_url'             => $result['image_url'],
                    'media_note'            => $result['media_note'],
                    'raw_content'           => $result['raw_content']
                ]

            ]
        ]);
    }
}
