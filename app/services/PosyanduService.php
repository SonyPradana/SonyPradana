<?php

use Simpus\Apps\Service;
use Convert\Converter\ConvertCode;
use Model\Simpus\{PosyanduRecords, GroupsPosyandu};

class PosyanduService extends Service
{
  public function __construct()
  {
    $this->error = new DefaultService();
    $this->useAuth();
  }

  public function search(array $request)
  {
    $code_hash   = $request['idhash'] ?? $this->error(400);

    $data = new PosyanduRecords();
    $data
      ->filtterById( ConvertCode::ConvertToCode( $code_hash ) )
      ->setStrictSearch( true );
    $result = $data->result();

    return array(
      'status'    => empty($result) ? 'no content' : 'ok',
      'code'      => 200,
      'data'      => $result ?? [],
      'headers'   => ['HTTP/1.1 200 Oke']
    );
  }

  public function grup_Posyandu(array $request)
  {
    $desa = $request['desa'] ?? null;
    if ($desa == null) {
      $groups_posyandu = GroupsPosyandu::getPosyanduAll();
    } else {
      $groups_posyandu = GroupsPosyandu::getPosyandu($desa);
    }

    return array(
      'status'    => 'ok',
      'data'      => $groups_posyandu,
      'headers' => ['HTTP/1.1 200 Oke']
    );
  }
}
