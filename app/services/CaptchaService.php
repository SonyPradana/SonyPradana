<?php

use Convert\Converter\ConvertCode;
use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use Simpus\Apps\Cache;
use Simpus\Apps\Service;

class CaptchaService extends Service
{
  public function __construct()
  {
    $this->error = new DefaultService();
  }

  public function Generate(array $request): array
  {
    // captcha builder
    $parseBuilder = new PhraseBuilder(5, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    $captcha = new CaptchaBuilder(null, $parseBuilder);
    $captcha
      ->setBackgroundColor(255, 255, 255)
      ->setMaxBehindLines(1)
      ->setMaxFrontLines(1)
      ->build(200, 70);

    // scrf builder
    $scrfKey = ConvertCode::RandomCode(6);

    Cache::remember($scrfKey, 600, ['value' => $captcha->getPhrase()]);

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

  public function Validate(array $request): array
  {
    // $this->validate;
    $validate =  new GUMP();
    $validate->validation_rules([
      'key'     => 'required',
      'captcha' => 'required',
    ]);
    $validate->run($request);

    if (! $validate->errors()) {
      if (Cache::static()->hasItem($request['key'])) {
        $get = Cache::static()->getItem($request['key']);
        $value = $get->get()['value'];

        // delete captha if key has hit
        Cache::static()->delete($request['key']);

        return $this->sussess([
          "is_valid" => strtolower($value) === strtolower($request['captcha'])
        ]);
      }
    }

    return $this->sussess(['is_valid' => false]);
  }
}

