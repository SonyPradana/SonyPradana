<?php

use Convert\Converter\ConvertCode;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Simpus\Apps\Service;
use System\Database\MyQuery;

class CaptchaService extends Service
{
  public function __construct()
  {
    $this->error = new DefaultService();
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
    $db('scrf_protection')
      ->insert()
      ->value('id', '')
      ->value('scrf_key', $scrfKey)
      ->value('secret', $captcha->getPhrase())
      ->value('hit', 1)
      ->execute();

    return array(
      'status'  => 'ok',
      'code'    => 200,
      'data'    => array (
        'scrf_key'      => $scrfKey,
        'captcha_image' => $captcha->inline()
      ),
      'error'   => false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }
}

