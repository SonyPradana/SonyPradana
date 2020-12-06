<?php

use Simpus\Apps\Middleware;
use Simpus\Helper\HttpHeader;
use Simpus\Message\{Rating, ReadMessage};
use System\Database\MyPDO;

class MessageService extends Middleware
{
    protected $PDO = null;
    public function __construct(MyPDO $PDO = null)
    {
        $this->PDO = $PDO ?? new MyPDO();
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

    public function rating(array $params): array
    {
        // validation
        $validation = new GUMP('id');
        $validation->validation_rules(array (
            'rating' => 'required|numeric|max_len,2',
            'mrating' => 'required|numeric|max_len,2',
            'unit' => 'required|alpha_space|max_len,20'
        ));
        $validation->run($params);
        if ($validation->errors()) $this->errorhandler();

        // get parameter
        $sender         = $_SERVER['REMOTE_ADDR'];
        $ratting        = $params['rating'];
        $max_ratting    = $params['mrating'];
        $unit           = $params['unit'];

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
            'headers'   => ['HTTP/1.1 200 Ok'],
            'error' => $validation->get_errors_array()
        ];
    }

    public function read(array $param)
    {
        $this->useAuth();

        $limit      = $param['limit'] ?? 100;
        $page       = $param['page'] ?? 1;
        $type       = $param['type'] ?? '';
        $resiver    = 'sonypradana@gmail.com';

        $read_message = new ReadMessage($this->PDO);
        $read_message->filterByPenerima($resiver);
        $read_message->filterByType($type);
        $read_message->currentPage($page);
        $read_message->limitView($limit);
        $read_message->viewResiver(true);
        $result = $read_message->bacaPesan();
        $maksData =   $read_message->maxData();

        return [
            'status'    => 'ok',
            'info'      => array (
                'maks_data' => $maksData,
                'page'      => $page,
                'limit'     => $limit,
                'maks_page' => ceil($maksData / $limit)
            ),
            'data'      => $result ?? [],
            'headers'   => ['HTTP/1.1 200 Oke']
        ];
    }

}
