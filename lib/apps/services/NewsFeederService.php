<?php

use Model\Article\articleModel;
use Simpus\Apps\Middleware;
use System\Database\MyPDO;

class NewsFeederService extends Middleware
{
    protected $PDO = null;

    public function __construct(MyPDO $PDO = null)
    {
        $this->PDO = $PDO ?? new MyPDO();
    }

    public function ResendNews(array $params): array
    {
        $article_feed   = new articleModel($this->PDO);
        $article_feed->selectColomn(
            [
                'id', 
                'discription',
                'create_time',
                'image_url',
                'image_alt',
                'slug',
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
                'url' => '/read/' . $article['slug'],
                'title' => $article['title'],
                'details' =>  $article['discription']
            ];
        }

        return [
            'status'    => empty($result) ? 'no content' : 'ok',
            'data'      => $result,
            'headers'   => ['HTTP/1.1 200 Oke']
        ];
    }
}
