<?php

use Convert\Converter\ConvertCode;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Simpus\Apps\Middleware;
use Simpus\Helper\HttpHeader;
use System\Database\MyQuery;

class CaptchaService extends Middleware
{
  // private function
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

  private function errorhandler()
  {
    HttpHeader::printJson(['status' => 'bad request'], 500, [
        "headers" => [
            'HTTP/1.1 400 Bad Request',
            'Content-Type: application/json'
        ]
    ]);
  }

  public function Generate(array $request): array
  {
    // captcha builder
    $parseBuilder = new PhraseBuilder(5, 'ABCDEFGHIJKLMNOPQRSTU');
    $captcha = new CaptchaBuilder(null, $parseBuilder);
    $captcha
      ->setBackgroundColor(255, 255, 255)
      ->setMaxBehindLines(1)
      ->setMaxFrontLines(1)
      ->build(200, 70);

    // scrf builder
    $scrfKey = ConvertCode::RandomCode(5);
    $db = new MyQuery();
    $db->insert('scrf_protection')
      ->value('id', '')
      ->value('scrf_key', $scrfKey)
      ->value('secret', $captcha->getPhrase())
      ->value('hit', 1)
      ->execute();

    return array(
      'status'  => 'ok',
      'data'    => array (
        'scrf_key' => $scrfKey,
        'captcha_image' => $captcha->inline()
      ),
      'error'   => false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }
}

