<?php

use Simpus\Apps\Middleware;
use Simpus\Simpus\KIAAnakRecords;
use Simpus\Helper\HttpHeader;

class AuthService extends Middleware
{
    public function login_status(array $params)
    {
        
        if( $this->getMiddleware()['auth']['login'] ){
            return [
                'status'    => 'ok',
                'headers'   => ['HTTP/1.1 200 Oke']
            ];
        }

        if( $this->getMiddleware()['auth']['user_nam'] == null ){
            return [
                'status'    => 'Session end',
                'headers'   => ['HTTP/1.1 200 Oke']
            ];
        }

        // default
        return [
            'status'    => 'not login',
            'headers'   => ['HTTP/1.1 200 Oke']
        ];

        
    }

}
