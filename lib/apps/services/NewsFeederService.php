<?php

use Model\Article\articleModel;
use Simpus\Apps\Middleware;

class NewsFeederService extends Middleware
{
    public function ResendNews(array $params): array
    {
        $article_feed   = new articleModel();
        $article_feed->selectColomn(
            [
                'id', 
                'discription',
                'create_time',
                'image_url',
                'image_alt',
                'url_id',
                'title',
                'raw_content'
            ]);

        $articles = $article_feed->resultAll();
        $result = [];
        foreach( $articles as $article) {
            $selisih_waktu = time() - $article['create_time'];
            $format_tanggal = $selisih_waktu < 86400 ? date('h:i:sa',  $article['create_time'])
                : date('d M Y',  $article['create_time']); 

            $img_loc = pathinfo($article['image_url']);
            $img_loc = str_replace($img_loc['basename'], 'small-' . $img_loc['basename']
                , $article['image_url']);

            $result[] = [
                'id' => $article['id'],
                'date' => $format_tanggal,
                'image' => $img_loc,
                'alt' => $article['image_alt'],
                'url' => '/read/' . $article['url_id'],
                'title' => $article['title'],
                'details' =>  $article['discription']
            ];
        }

        return [
            'status'    => 'ok',
            'data'      => $result,
            'headers'   => ['HTTP/1.1 200 Oke']
        ];
    }
}
