<?php

use Model\articleModel;
use Simpus\Apps\Middleware;

class NewsFeederService extends Middleware
{
    public function ResendNews(array $params): array
    {
        $article_feed   = new articleModel();
        $article_feed->selectColomn(['id', 'discription', 'create_time', 'image_url', 'image_alt', 'url_id', 'title', 'raw_content']);
        $articles        = $article_feed->resultAll();
        // var_dump($articles);
        $result = [];
        foreach( $articles as $article) {
            $result[] = [
                'id' => $article['id'],
                'date' => date('Y-m-d h:i:sa', $article['create_time']),
                'image' => $article['image_url'],
                'alt' => $article['image_alt'],
                'url' => '/read/' . $article['url_id'],
                'title' => $article['title'],
                'details' =>  $article['discription']
            ];
        }

        return [
            'status'    => 'ok',
            'data'      => $result ?? [],
            'headers'   => ['HTTP/1.1 200 Oke']
        ];
    }
}
