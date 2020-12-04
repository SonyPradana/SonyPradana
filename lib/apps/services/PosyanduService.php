<?php

use Simpus\Apps\Middleware;
use Convert\Converter\ConvertCode;
use Model\Simpus\PosyanduRecords;
use Simpus\Helper\HttpHeader;
use Model\Simpus\GroupsPosyandu;

class PosyanduService extends Middleware
{
    public function __construct()
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

    private function errorhandler(){
        HttpHeader::printJson(['status' => 'bad request'], 500, [
            "headers" => [
                'HTTP/1.1 400 Bad Request',
                'Content-Type: application/json'
            ]
        ]);
    }

    public function search(array $params)
    {
        $code_hash   = $params['idhash'] ?? $this->errorhandler();

        $data = new PosyanduRecords();
        $data
            ->filtterById( ConvertCode::ConvertToCode( $code_hash ) )
            ->setStrictSearch( true );
        $result = $data->result();

        return [
            'status'    => 'ok',
            'data'      => $result ?? [],
            'headers'   => ['HTTP/1.1 200 Oke']
        ];
    }


    public function grup_Posyandu(array $params)
    {
        $desa = $params['desa'] ?? null;
        if( $desa == null ){
            $groups_posyandu = GroupsPosyandu::getPosyanduAll();
        }else{
            $groups_posyandu = GroupsPosyandu::getPosyandu($desa);
        }

        return [
            'status'    => 'ok',
            'data'      => $groups_posyandu,
            'headers' => ['HTTP/1.1 200 Oke']
        ];
    }
}
