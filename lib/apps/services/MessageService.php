<?php

use Simpus\Apps\Middleware;
use Simpus\Helper\HttpHeader;
use Simpus\Message\Rating;
use Simpus\Message\ReadMessage;

class MessageService extends Middleware
{
    public function __construct()
    {
    }

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

    private function errorhandler(){
        HttpHeader::printJson(['status' => 'bad request'], 500, [
            "headers" => [
                'HTTP/1.1 400 Bad Request',
                'Content-Type: application/json'
            ]
        ]);
    }

    public function rating(array $param)
    {
        // get parameter
        $sender         = $_SERVER['REMOTE_ADDR'];
        $ratting        = $param['rating'] ?? $this->errorhandler();
        $max_ratting    = $param['mrating'] ?? $this->errorhandler();
        $unit           = $param['unit'] ?? $this->errorhandler();

        $new_review     = new Rating($sender, $ratting, $max_ratting, $unit);
        if( $new_review->spamDetector() ){
            return [
                'status'    => 'forbiden',
                'headers'   => ['HTTP/1.1 403 Forbidden']
            ];
        }

        $new_review->kirimPesan();
        return [
            'status'    => 'ok',
            'headers'   => ['HTTP/1.1 200 Ok']
        ];
    }

    public function read(array $param)
    {
        $this->useAuth();

        $page       = $_GET['page'] ?? 10;
        $resiver    = 'sonypradana@gmail.com';

        $read_message = new ReadMessage();
        $read_message->filterByPenerima($resiver);
        $read_message->limitView( $page );
        $read_message->viewResiver(true);
        $result = $read_message->bacaPesan();

        return [
            'status'    => 'ok',
            'data'      => $result ?? [],
            'headers'   => ['HTTP/1.1 200 ok']
        ];
    }

}
