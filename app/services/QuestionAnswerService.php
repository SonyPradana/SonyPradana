<?php

use Helper\String\Manipulation;
use Model\QuestionAnswer\ask;
use Model\QuestionAnswer\asks;
use Simpus\Apps\Service;
use System\Database\MyPDO;
use System\Database\MyQuery;

class QuestionAnswerService extends Service
{
  protected $PDO = null;

  public function __construct(MyPDO $pdo = null)
  {
    $this->error = new DefaultService();
    $this->PDO = $pdo ?? MyPDO::getInstance();
  }

  public function get_post(array $request): array
  {
    $asks = new asks($this->PDO);
    $db = new MyQuery($this->PDO);

    $thread = array();
    $perentPost = $asks->fillterPerentOnly()->resultAll();
    foreach ($perentPost as $child) {
      $thisThread = [];

      $child['date_creat'] = date('d M Y', $child['date_creat']);
      $child['slug'] = Manipulation::slugify($child['title']);
      $vote = $child['like_post'] - $child['dislike_post'];
      $child['vote'] = $vote < 0 ? 0 : $vote;
      $thisThread['perent'] = $child;

      $thisThread['childs_id'] = array_column (
        $db('public_quest')
        ->select(array('id'))
        ->equal('perent_id', $child['id'])
        ->order('date_creat', MyQuery::ORDER_ASC)
        ->all(), 'id'
      );

      $thisThread['best_child'] = $db
        ->table('public_quest')
        ->select(array('*'))
        ->equal('perent_id', $child['id'])
        ->order('like_post', MyQuery::ORDER_DESC)
        ->single();

      if ($child['perent_id'] == null) {
        $thread[] = $thisThread;
      }
    }

    return array(
      'status'  => empty($thread) ? 'no content' :'ok',
      'code'    => 200,
      'data'    => $thread,
      'error'   => false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function submit_post(array $request): array
  {
    // only accept pot method
    if ($request['x-method'] != 'PUT') {
      return array (
        'status'  => 'method not allow',
        'code'    => 405,
        'data'    => '',
        'error'   => array (
          'method' => 'method not match'
        ),
        'headers' => array('HTTP/1.1 405 Method not allow')
      );
    }

    // validation
    $validate = new GUMP('id');
    $validate->validation_rules(
      array (
        'scrf_key' => 'required|alpha_numeric',
        'scrf_secret' => 'required|alpha_numeric',
        'name' => 'required|min_len,4|max_len,25|valid_name',
        'perent_id' => 'min_len,1|max_len,3',
        'title' => 'required|min_len,4|max_len,125',
        'content' => 'max_len,240',
        'tag' => 'max_len,50'
      )
    );
    $validate->filter_rules(
      array (
        'name' => 'trim',
        'title' => 'htmlencode|trim',
        'content' => 'htmlencode|trim',
        'tag' => 'htmlencode|trim',
      )
    );
    $validate->run($request);
    $error = $validate->get_errors_array();
    $save = false;

    // sample scrf protection (resiver)
    $db = new MyQuery($this->PDO);
    $getScrf = $db
      ->table('scrf_protection')
      ->select()
      ->equal('scrf_key', $request['scrf_key'])
      ->single();

    // scrf hit count & scrf secret cek
    $scrfHit = $getScrf['hit'] ?? 0;
    $scrfSecret = $getScrf['scret'] ?? false;
    if ($scrfHit < 1
    && $scrfSecret != $request['scrf_secret']) {
      $error['scrf_secret'] = 'Token tidak valid';
    }

    // reomove scrf access if success
    $scrfHit -= 1;
    if ($scrfHit < 1) {
      $db('scrf_protection')
        ->delete()
        ->equal('scrf_key', $request['scrf_key'])
        ->execute();
    } else {
      $db('scrf_protection')
        ->update()
        ->value('hit', $scrfHit)
        ->equal('scrf_key', $request['scrf_key'])
        ->execute();
    }

    if (empty($error)) {
      $ask = new ask();
      $ask->convertFromArray(
        array (
          'id' => $request['no_storage_id'] ?? '',
          'perent_id' => $request['perent_id'] ?? '',
          'date_creat' => time(),
          'date_update' => time(),
          'author' => $request['name'],
          'like_post' => 0,
          'dislike_post' => 0,
          'title' => $request['title'],
          'content' => $request['content'],
          'tag' => $request['tag'],
          'image' => ''
        )
      );

      $save = $ask->cread();
    }


    return array (
      'status'  => $save ? 'ok' : 'not saved',
      'code'    => 200,
      'data'    => [
        $request,
        'new_id'  => $this->PDO->lastInsertId()
      ],
      'error'   => $error ?? false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  private function getChild($perent_id)
  {
    $db = new MyQuery($this->PDO);
    $find = true;
    $id = $perent_id;
    $childs = array();

    do {
      $post = $db
        ->table('public_quest')
        ->select(['*'])
        ->equal('perent_id', $id)
        ->order('date_creat', MyQuery::ORDER_ASC)
        ->single();

      if (! empty($post)) {
        $childs[] = $post;
      }

      $find = empty($post) ? true : false;
      $id = $post['id'] ?? 0;
    } while ($find == false);

  return $childs;
  }

}

