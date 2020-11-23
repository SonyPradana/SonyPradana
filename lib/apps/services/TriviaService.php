<?php

use Model\Trivia\Trivia;
use Simpus\Apps\Middleware;
use Simpus\Helper\HttpHeader;

class TriviaService extends Middleware
{
  private function UseAuth()
  {
    // cek access
    if ($this->getMiddleware()['auth']['login'] == false) {
      HttpHeader::printJson(['status' => 'unauthorized'], 500, [
          "headers" => [
              'HTTP/1.0 401 Unauthorized',
              'Content-Type: application/json'
          ]
      ]);
    }
  }

  public function Delete_Ques()
  {
    $this->UseAuth();

    $triva = new Trivia(0);

    return array (
      'status' => 'ok',
      'success_deleted' => $triva->delete()
    );
  }

  public function Get_Ques(array $params): array
  {
    $rev = $params['rev'] ?? 0;
    $triva = new Trivia(null, $rev);
    $triva->read();

    return array (
      'status' => 'ok',
      'test' => $triva->isExist(),
      'data' => array (
        'quest_id' => $triva->getID(),
        'info' => $triva->getinfo(),
        'quest' => array (
          'quest' => $triva->getQuest(),
          'image' => $triva->getQuestImage(),
        ),
        'options' => $triva->getOptions()
        ),
        'headers' => ['HTTP/1.1 200 Oke']
    );
  }

  public function Get_Answer(array $params)
  {
    $question_id = $params['question_id'] ?? 1;
    $question_answer = $params['question_answer'] ?? 1;

    $triva = new Trivia($question_id);
    $triva->read();

    $summary = $triva->submitAnswer($question_answer);
    $sumbited = 0;
    foreach ($summary as $val) {
      $sumbited += $val['summary'];
    }

    return array (
      'status' => 'ok',
      'data' => array(
        'correct' => $triva->getAnswer(),
        'info' => $triva->getInfo(),
        'submited' => $sumbited,
        'summary' => $summary
      ),
      'headers' => ['HTTP/1.1 200 Oke']
    );
  }

  public function Submit_Ques(array $params)
  {
    // TODO: corrent answer id tidak selalu di '0'

    $option = array (
      array ('id' => 0, 'answer' => $params['answer_1'] ?? null),
      array ('id' => 1, 'answer' => $params['answer_2'] ?? null),
      array ('id' => 2, 'answer' => $params['answer_3'] ?? null),
      array ('id' => 3, 'answer' => $params['answer_4'] ?? null)
    );
    $summary = array (
      array ('id' => 0, 'summary' => 0),
      array ('id' => 1, 'summary' => 0),
      array ('id' => 2, 'summary' => 0),
      array ('id' => 3, 'summary' => 0),
    );

    $data = array (
      'author' => $params['author'] ?? null,
      'level' => $params['level'] ??  null,
      'quest' => $params['quest'] ?? null,
      'quest_img' => $params['quest_img'] ?? null,
      'options' => json_encode($option),
      'summary' => json_encode($summary),
      'correct_answer' => 0,
      'explanation' => $params['explanation'] ?? ''
    );

    $trivia = new Trivia(1);
    $status = $trivia->newQuest($data);

    return array (
      'status' => $status === true ? 'ok' : 'error',
      'error' => $status,
      'headers' => ['HTTP/1.1 200 Oke']
    );
  }  
}
