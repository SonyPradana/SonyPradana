<?php

use Gumlet\ImageResize;
use Helper\String\Manipulation;
use Model\Article\article;
use Simpus\Apps\Service;
use System\Database\MyPDO;
use System\File\UploadFile;

class ArticlesService extends Service
{
  protected $PDO;
  public function __construct(MyPDO $PDO = null)
  {
    $this->error = new DefaultService();
    $this->PDO = $PDO ?? MyPDO::getInstance();
    $this->useAuth();
  }

  public function Create_Article(array $request): array
  {
    // only accept put method
    if ($request['x-method'] != 'POST') {
      return $this->error(parent::CODE_METHOD_NOT_ALLOWED);
    }

    // validate file to upload
    $file  = $request['files']['image_article'] ?? false;
    $error = $file == false ? array('files' => 'file to upload not found') : array();

    // validate article contents
    $validate = new GUMP();
    $validate->validation_rules(
      array(
        'title'       => 'required|between_len,20;200|alpha_numeric_space',
        'discription' => 'required|between_len,30;250',
        'keywords'    => 'required|between_len,4;100',
        'image_alt'   => 'required|between_len,4;32|alpha_numeric_space',
        'media_note'  => 'required|between_len,4;100|alpha_numeric_space',
        'content'     => 'required',    // main content
      )
    );
    $validate->filter_rules(
      array(
        'title'       => 'trim',
        'discription' => 'sanitize_string'
      )
    );
    $validate->run($request);
    $error  = array_merge($validate->get_errors_array(), $error);
    $status = 'bad required';

    if (empty($error) && $file != false) {
      // pre require
      $slug = Manipulation::slugify($request['title']);
      $extension = explode('.', $file['name']);
      $fileExtension = strtolower(end($extension));
      $imageurl = "/data/img/article/$slug.$fileExtension";

      $submit = new article($this->PDO);
      $submit->convertFromArray(
        array(
          'id'            => '',
          'slug'          => $slug,
          'author'        => Session::getSession()['auth']['user_name'],
          'title'         => $request['title'],
          'discription'   => $request['discription'],
          'keywords'      => $request['keywords'],
          'create_time'   => time(),
          'update_time'   => time(),
          'image_url'     => $imageurl,
          'image_alt'     => $request['image_alt'],
          'media_note'    => $request['media_note'],
          'raw_content'   => $request['content'],
          'css'           => '',
          'js'            => '',
          'status'        => $request['status'] ?? 'draf'
        )
      );

      if ($submit->cread()) {
        // upload image

        $upload = new UploadFile($file);
        $upload
          ->setFolderLocation('/public/data/img/article/')
          ->setFileName($slug)
          ->setMimeTypes(array('image/jpg', 'image/jpeg', 'image/png'))
          ->setMaxFileSize(500_000)
          ->upload();

        if ($upload->Success()) {
          // success submit and upload image
          $status = 'ok';

          // resize image for thumbnail
          (new ImageResize(BASEURL . '/public' . $imageurl))
            ->resizeToWidth(250)
            ->save(BASEURL . "/public/data/img/article/small-$slug.$fileExtension");

        } else {
          // gagal upload, submit dibatalkan

          // ambil idnya
          $id = $this->PDO->lastInsertId();
          $submit->setID($id);
          if ($submit->isExist()) {
            $submit->delete();
          }

          $error['file_upload'] = 'gagal mengupload data';
          $status = 'not save';
        }
      } else {
        // tidak dapat meyimpan article

        $error['submit_article'] = 'gagal menyimpan data';
        $status = 'not save';
      }
    }

    // final result
    return array (
      'status'  => $status,
      'code'    => 200,
      'data'    => array(),
      'error'   => $error ?? false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function Update_Article(array $request): array
  {
    // only accept put method
    if ($request['x-method'] != 'POST') {
      return $this->error(parent::CODE_METHOD_NOT_ALLOWED);
    }

    // validate article contents
    $validate = new GUMP();
    $validate->validation_rules(
      array(
        'discription' => 'required|between_len,30;250',
        'keywords'    => 'required|between_len,4;100',
        'image_alt'   => 'required|between_len,4;32|alpha_numeric_space',
        'media_note'  => 'required|between_len,4;100|alpha_numeric_space',
        'content'     => 'required',    // main content
      )
    );
    $validate->filter_rules(
      array(
        'discription' => 'sanitize_string'
      )
    );
    $validate->run($request);
    $error  = $validate->get_errors_array();
    $status = 'bad required';

    if (empty($error)) {
      $submit = new article($this->PDO);
      $submit->setID($request['id']);
      $submit->convertFromArray(
        array(
          'discription'   => $request['discription'],
          'keywords'      => $request['keywords'],
          'update_time'   => time(),
          'image_alt'     => $request['image_alt'],
          'media_note'    => $request['media_note'],
          'raw_content'   => $request['content'],
          'status'        => $request['status'] ?? 'draft'
        )
      );

      if ($submit->isExist()) {
        if ($submit->update()) {
          $status = 'ok';
        } else {
          // tidak dapat meyimpan article

          $error['submit_article'] = 'gagal menyimpan data';
          $status = 'not save';
        }
      } else {
        $error['submit_article'] = 'Article tidak ditemukan';
        $status = 'not save';
      }

    }

    // final result
    return array (
      'status'  => $status,
      'code'    => 200,
      'data'    => array(),
      'error'   => $error ?? false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function Review_Article(array $request): array
  {
    $article = new article();
    $article->setID($request['id']);

    if (! $article->isExist()) {
      return array (
        'status'  => 'no content',
        'code'    => 200,
        'data'    => array(),
        'error'   => array(
          'id'  => 'data not found/exist'
        ),
        'headers' => array('HTTP/1.1 200 Oke')
      );
    }

    $article->read();
    $result = $article->convertToArray();
    unset($result['css']);
    unset($result['js']);

    return array (
      'status'  => 'ok',
      'code'    => 200,
      'data'    => $result,
      'error'   => false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }
}

