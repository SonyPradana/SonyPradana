<?php

use System\Router\Controller;

class DefaultController extends Controller{
    public static function error($param)
    {
        // default param
        $code = $param['code'] ?? 200;
        $status = $param['status'] ?? 'Ok';
        $message = $param['message'] ?? 'respone success';
        $info = $param['info'] ?? null;
        $links = $param['links'] ?? array(array('Home Page', '/'));

        // header
        header("HTTP/1.0 $code $status");

        $portal = array (
            'meta' => array (
                'title' => $status,
                'discription' => $message,
                'keywords' => implode(', ', array($status, $code))
            ),
            'error' => array (
                'code' => $code,
                'message' => $message,
                'info' => $info,
                'links' => $links
            )
        );

        // short hand to access content
        if (isset( $portal['error'])) {
            $error = (object) $portal['error'];
        }

        // render template
        require_once APP_FULLPATH['view'] . 'default/error.template.php';
        exit();
    }

    public static function page_400()
    {
        $path = $params['path'] ?? '';
        self::error(array (
            'code' => 400,
            'status' => 'Bad Request',
            'message' => ' Bad Request',
            'info' => $_SERVER['SERVER_NAME'].$path
        ));

    }

    public static function page_401(array $params)
    {
        $link = $params['links'] ?? array(array('Login', '/login'));
        self::error(array (
            'code' => 401,
            'status' => 'Unauthorized',
            'message' => 'Login Terlebih Dahulu',
            'info' => 'Unauthorized',
            'links' => $link
        ));

    }

    public static function page_403()
    {
        $path = $params['path'] ?? '';
        self::error(array (
            'code' => 403,
            'status' => 'Forbidden',
            'message' => 'Akses ditolak',
            'info' => $_SERVER['SERVER_NAME'].$path
        ));

    }

    public static function page_404(array $params)
    {
        $path = $params['path'] ?? '';
        self::error(array (
            'code' => 404,
            'status' => 'Page Not Found',
            'message' => 'Halaman tidak ditemukan',
            'info' => $_SERVER['SERVER_NAME'].$path
        ));
    }

    public static function page_405(array $params)
    {
        $path = $params['path'] ?? '';
        $method = $params['method'];

        self::error(array (
            'code' => 405,
            'status' => 'Method Not Allowed',
            'message' => 'Method tidak dizinkan',
            'info' => 'This path "'.$_SERVER['SERVER_NAME'].$path.'" not allow with method "'.$method.'"'
        ));
    }
}
