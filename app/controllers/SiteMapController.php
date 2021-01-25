<?php

use Helper\String\Manipulation;
use Model\Article\articleModel;
use Model\QuestionAnswer\asks;
use Simpus\Apps\Controller;

class SiteMapController extends Controller
{
  public function index()
  {
    header("Content-Type: application/xml, charset=UTF-8");

    $xml = new SimpleXMLElement('<urlset/>');
    $xml->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');


    $urls = array_merge(
      $this->static_urls(),
      $this->premanent_url(),
      $this->article_urls(),
      $this->aks_urls(),
    );

    foreach ($urls as $val) {
      $url = $xml->addChild('url');
      $url->addChild('loc', "https://simpuslerep.com" . $val['loc']);
      $url->addChild('lastmod', $val['lastmod']);
      $url->addChild('changefreq', $val['changefreq']);
      $url->addChild('priority', $val['priority']);
    }

    echo $xml->asXML();
  }

  public function html()
  {
    header("Content-Type: text, charset=UTF-8");

    $urls = array_merge(
      $this->static_urls(),
      $this->premanent_url(),
      $this->article_urls(),
      $this->aks_urls(),
    );

    foreach ($urls as $val) {
      echo 'https://simpuslerep.com' . $val['loc'] . "\n";
    }
  }

  public function article_urls(): array
  {
    $articles = new articleModel();
    $articles->selectColomn(['slug', 'update_time']);
    $articlesData = $articles->result();

    $res = [];
    foreach ($articlesData as $article) {
      $dateformat = date('Y-m-d', $article['update_time']);

      $url = [];
      $url['loc']         = '/' . $article['slug'];
      $url['lastmod']     = $dateformat;
      $url['changefreq']  = 'monthly';
      $url['priority']    = '0.85';

      $res[] = $url;
    }
    return $res;
  }

  public function aks_urls(): array
  {
    $qna = new asks();

    $res = [];
    foreach ($qna->resultAll() as $thread) {
      $url = [];
      $dateformat = date('Y-m-d', $thread['date_update']);

      $url['loc']         = '/question/' . $thread['id'] . '/' . Manipulation::slugify($thread['title']);
      $url['lastmod']     = $dateformat;
      $url['changefreq']  = 'daily';
      $url['priority']    = '0.85';

      $res[] = $url;
    }
    return $res;
  }

  public function static_urls(): array
  {
    return array (
      [
        'loc'         => '/info/antrian-online/',
        'lastmod'     => '2020-08-15',
        'changefreq'  => 'monthly',
        'priority'    => '0.7'
      ],
      [
        'loc'         => '/info/jadwal-pelayanan/',
        'lastmod'     => '2020-08-15',
        'changefreq'  => 'monthly',
        'priority'    => '0.8'
      ],
      [
        'loc'         => '/info/covid-kabupaten-semarang',
        'lastmod'     => '2020-04-11',
        'changefreq'  => 'hourly',
        'priority'    => '0.8'
      ],
    );
  }

  public function premanent_url(): array
  {
    return array (
      [
        'loc'         => '/about',
        'lastmod'     => '2020-03-25',
        'changefreq'  => 'monthly',
        'priority'    => '0.7'
      ],
      [
        'loc'         => '/Ourteam',
        'lastmod'     => '2020-03-25',
        'changefreq'  => 'monthly',
        'priority'    => '0.7'
      ],
      [
        'loc'         => '/QnA',
        'lastmod'     => '2021-01-22',
        'changefreq'  => 'daily',
        'priority'    => '0.8'
      ],
      [
        'loc'         => '/Stories',
        'lastmod'     => '2020-01-1',
        'changefreq'  => 'daily',
        'priority'    => '0.75'
      ],
    );
  }
}
