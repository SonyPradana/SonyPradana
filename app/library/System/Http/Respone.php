<?php

namespace System\Http;

class Respone
{
  public const HTTP_OK = 200;
  public const HTTP_CREATED = 201;
  public const HTTP_ACCEPTED = 202;
  public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
  public const HTTP_NO_CONTENT = 204;
  public const HTTP_MOVED_PERMANENTLY = 301;
  public const HTTP_BAD_REQUEST = 400;
  public const HTTP_UNAUTHORIZED = 401;
  public const HTTP_PAYMENT_REQUIRED = 402;
  public const HTTP_FORBIDDEN = 403;
  public const HTTP_NOT_FOUND = 404;
  public const HTTP_METHOD_NOT_ALLOWED = 405;
  //
  public static $statusTexts = [
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    301 => 'Moved Permanently',
    304 => 'Not Modified',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
  ];
  //
  private $content;
  private $respone_code;
  private $headers;
  private $is_array;  // content type

  public function __construct($content = '', int $respone_code = Respone::HTTP_OK, array $headers = [])
  {
    $headers_content = $content['headers'] ?? [];
    // remove header information
    if (isset($content['headers'])) {
      unset($content['headers']);
    }

    $this->content = $content;
    $this->respone_code = $respone_code;
    $this->headers = array_merge($headers, $headers_content);

    $this->sendHeaders();

    $this->is_array = is_array($content);
  }

  private function sendHeaders(): void
  {
    // header respone code
    $respone_code = $this->respone_code;
    $respone_text = Respone::$statusTexts[$respone_code];
    $respone_template = "HTTP/1.1 $respone_code $respone_text";
    header($respone_template);

    // header
    foreach ($this->headers as $header) {
      header($header);
    }
  }

  private function sendContent($content)
  {
    if ($this->is_array) {
      echo json_encode($content, JSON_NUMERIC_CHECK);
    } else {
      echo $content;
    }
  }

  public function removeHeader(array $headers)
  {
    foreach ($headers as $header) {
      header_remove($header);
    }
    return $this;
  }

  public function send()
  {
    $this->sendContent($this->content);

    return $this;
  }

  public function json()
  {
    header_remove("Content-Type");
    header('Content-Type: application/json');

    $this->sendContent($this->content);

    return $this;
  }

  public function html()
  {
    header_remove("Content-Type");
    header('Content-Type: text/html');

    $this->sendContent($this->content);

    return $this;
  }

  public function planText()
  {
    header_remove("Content-Type");
    header('Content-Type: text/html');

    $this->sendContent($this->content);

    return $this;
  }

  public function minify()
  {
    if ($this->is_array) {
      return $this;
    }
    $search = array(
      '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
      '/[^\S ]+\</s',     // strip whitespaces before tags, except space
      '/(\s)+/s',         // shorten multiple whitespace sequences
      '/<!--(.|\s)*?-->/' // Remove HTML comments
    );

    $replace = array(
      '>',
      '<',
      '\\1',
      ''
    );

    $this->content = preg_replace($search, $replace, $this->content);
    return $this;
  }

  public function close()
  {
    exit;
  }

  public function setContent(string $content)
  {
    $this->content = $content;

    return $this;
  }

  public function setResponeCode(int $respone_code)
  {
    $this->respone_code = $respone_code;

    return $this;
  }

  public function setHeaders(array $headers)
  {
    $this->headers = $headers;

    return $this;
  }

  public function followRequest(Request $request, array $headers = [])
  {
    $follow_rule = array_merge($headers, [
      'cache-control',
      'conten-type'
    ]);
    // header based on the Request
    foreach ($follow_rule as $rule) {
      if ($request->hasHeader($rule)) {
        header($request->getHeaders($rule));
      }
    }

    return $this;
  }
}
