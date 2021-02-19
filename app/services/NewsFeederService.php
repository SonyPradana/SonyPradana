<?php

use Model\Article\articleModel;
use Simpus\Apps\Middleware;
use System\Database\MyPDO;
use Simpus\Helper\HttpHeader;

class NewsFeederService extends Middleware
{
  protected $PDO = null;

  private function useAuth()
  {
    // cek access
    if( $this->getMiddleware()['auth']['login'] == false ){
      HttpHeader::printJson(['status' => 'unauthorized'], 500, [
        "headers" => [
          'HTTP/1.0 401 Unauthorized',
          'Content-Type: application/json'
        ]
      ]);
    }
  }

  public function __construct(MyPDO $PDO = null)
  {
    $this->PDO = $PDO ?? new MyPDO();
  }

  public function ResendNews(array $request): array
  {
    $feeds   = new articleModel($this->PDO);
    $feeds->selectColomn(
      array (
        'id', 'discription', 'update_time', 'image_url',
        'image_alt', 'slug', 'title', 'status'
      )
    );
    $feeds->filterStatus($request['status'] ?? 'publish');

    $result = [];
    foreach ($feeds->result() as $article) {
      $selisih_waktu = time() - $article['update_time'];
      $format_tanggal = $selisih_waktu < 86400 ? date('h:i:s a',  $article['update_time'])
        : date('d M Y',  $article['update_time']);

      $arr_img = explode('/', $article['image_url']);
      $img_loc = str_replace(end($arr_img), 'small-' . end($arr_img)
        , $article['image_url']);

      $result[] = [
        'id'      => $article['id'],
        'date'    => $format_tanggal,
        'image'   => $img_loc,
        'alt'     => $article['image_alt'],
        'url'     => '/read/' . $article['slug'],
        'title'   => $article['title'],
        'details' => $article['discription']
      ];
    }

    return array(
      'status'    => empty($result) ? 'no content' : 'ok',
      'code'      => 200,
      'data'      => $result,
      'headers'   => ['HTTP/1.1 200 Oke']
    );
  }

  public function AllNews(array $request): array
  {
    $this->useAuth();

    $feeds   = new articleModel($this->PDO);
    $feeds->selectColomn(
      array (
        'id', 'discription', 'update_time', 'image_url',
        'image_alt', 'slug', 'title', 'status'
      )
    );

    $result = [];
    foreach ($feeds->resultAll() as $article) {
      $selisih_waktu = time() - $article['update_time'];
      $format_tanggal = $selisih_waktu < 86400 ? date('h:i:s a',  $article['update_time'])
        : date('d M Y',  $article['update_time']);

      $arr_img = explode('/', $article['image_url']);
      $img_loc = str_replace(end($arr_img), 'small-' . end($arr_img)
        , $article['image_url']);

      $result[] = [
        'id'      => $article['id'],
        'date'    => $format_tanggal,
        'image'   => $img_loc,
        'alt'     => $article['image_alt'],
        'url'     => '/read/' . $article['slug'],
        'title'   => $article['title'],
        'details' => $article['discription'],
        'status'  => $article['status']
      ];
    }

    return array(
      'status'    => empty($result) ? 'no content' : 'ok',
      'code'      => 200,
      'data'      => $result,
      'headers'   => ['HTTP/1.1 200 Oke']
    );
  }

}
