<?php

use Model\QAResponse\QAResponse;
use Model\QAResponse\QAResponses;
use Model\QuestionAnswer\ask;
use Simpus\Apps\Middleware;
use Simpus\Helper\HttpHeader;
use System\Database\MyPDO;

class QAResponseService extends Middleware
{
  // private function
  private function useAuth()
  {
    // cek access
    if ($this->getMiddleware()['auth']['login'] == false) {
      HttpHeader::printJson(['status' => 'unauthorized'], 500, [
        "headers" => array (
          'HTTP/1.0 401 Unauthorized',
          'Content-Type: application/json'
        )
      ]);
    }
  }

  private function errorhandler()
  {
    HttpHeader::printJson(['status' => 'bad request'], 500, [
      "headers" => array (
        'HTTP/1.1 400 Bad Request',
        'Content-Type: application/json'
      )
    ]);
  }

  protected $PDO;

  public function __construct(MyPDO $PDO = null)
  {
    $this->PDO = $PDO ?? new MyPDO();
  }

  public function Like(array $request): array
  {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'dev';
    // cek thread exis atau tidak
    $threadID = $request['thread_id'] ?? null;
    if ($threadID == null) {
      return $this->errorhandler();
    }

    $ask = new ask($this->PDO);
    if (! $ask->setID($threadID)->isExist()) {
      return $this->errorhandler();
    }
    $ask->read();
    $like_before = $ask->getLike() ?? 0;
    $like = $like_before;

    // cek previews
    $cekResEvent = new QAResponses($this->PDO);
    $cekResEvent
      ->filterEventID($threadID)
      ->filterUserID($ip);
    $getEvent = $cekResEvent->result();

    $newEvent = new QAResponse($this->PDO);
    $newEvent
      ->setEventID($threadID)
      ->setUserID($ip);
    // jika belum pernah samasekali -> buat
    if (empty($getEvent)) {
      $newEvent->addLike()->cread();
      // add like
      $like++;

    } else {
      $lastEvent = $getEvent[0];
      // prevent spammer
      if (count($getEvent) < 20
      && ($lastEvent['date_create'] + 5 < time())) {

        // jika respone tidak sama dr sebelumnya -> tambah
        if ($lastEvent['respone'] == 'dislike:1'
        || $lastEvent['respone'] == 'unvote') {

          $newEvent->addLike()->cread();
          // add like
          $like++;

        } elseif ($lastEvent['respone'] == 'like:1') {

          // mengundo vote
          $newEvent->unvote()->cread();
          $like--;
        }
      }
    }

    if ($like != $like_before) {
      $ask->setLike($like)->update();
    }

    $vote = $like - $ask->getDislike();
    $vote = $vote < 0 ? 0 : $vote;

    return array (
      'status'  => 'ok',
      'data'    => Array (
        'thread_id' => $request['thread_id'] ?? 6,
        'like_before' => $like_before,
        'like' => $like,
        'vote' => $vote,
      ),
      'error'   => false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

  public function Dislike(array $request): array
  {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'dev';
    // cek thread exis atau tidak
    $threadID = $request['thread_id'] ?? null;
    if ($threadID == null) {
      return $this->errorhandler();
    }

    $ask = new ask($this->PDO);
    if (! $ask->setID($threadID)->isExist()) {
      return $this->errorhandler();
    }
    $ask->read();
    $dislike_before = $ask->getDislike() ?? 0;
    $dislike = $dislike_before;

    // cek previews
    $cekResEvent = new QAResponses($this->PDO);
    $cekResEvent
      ->filterEventID($threadID)
      ->filterUserID($ip);
    $getEvent = $cekResEvent->result();

    $newEvent = new QAResponse($this->PDO);
    $newEvent
      ->setEventID($threadID)
      ->setUserID($ip);
    // jika belum pernah samasekali -> buat
    if (empty($getEvent)) {
      $newEvent->addDislike()->cread();
      // add like
      $dislike++;

    } else {
      $lastEvent = $getEvent[0];
      // prevent spammer
      if (count($getEvent) < 20
      && ($lastEvent['date_create'] + 5 < time())) {

        // jika respone tidak sama dr sebelumnya -> tambah
        if ($lastEvent['respone'] == 'like:1'
        || $lastEvent['respone'] == 'unvote') {

          $newEvent->addDislike()->cread();
          // add like
          $dislike++;

        } elseif ($lastEvent['respone'] == 'dislike:1') {

          // mengundo vote
          $newEvent->unvote()->cread();
          $dislike--;
        }
      }
    }

    if ($dislike != $dislike_before) {
      $ask->setDislike($dislike)->update();
    }

    $vote = $ask->getLike() - $dislike;
    $vote = $vote < 0 ? 0 : $vote;

    return array (
      'status'  => 'ok',
      'data'    => Array (
        'thread_id' => $request['thread_id'] ?? 6,
        'dislike_before' => $dislike_before,
        'dislike' => $dislike,
        'vote' => $vote
      ),
      'error'   => false,
      'headers' => array('HTTP/1.1 200 Oke')
    );
  }

}

