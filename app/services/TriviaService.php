<?php

use Model\Trivia\Trivia;
use Simpus\Apps\Service;
use System\Database\MyPDO;

class TriviaService extends Service
{
  protected $PDO = null;
  public function __construct(MyPDO $PDO = null)
  {
    $this->error = new DefaultService();
    $this->PDO = $PDO ?? new MyPDO();;
  }

  public function Delete_Ques(array $params)
  {
    $this->UseAuth();

    $triva = new Trivia($this->PDO);
    $triva->setID($params['id'] ?? 0);

    return array (
      'status' => 'ok',
      'success_deleted' => $triva->delete(),
      'headers' => ['HTTP/1.1 200 Oke']
    );
  }

  public function Get_Ques(array $params): array
  {
    $rev = $params['rev'] ?? 0;
    $triva = new Trivia(null, $rev);
    $triva->randomID($rev)->read();

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

    $triva = new Trivia($this->PDO);
    $triva->setID($question_id)->read();

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

    $trivia = new Trivia($this->PDO);
    $status = $trivia->newQuest($data);

    return array (
      'status' => $status === true ? 'ok' : 'error',
      'error' => $status,
      'info' => array (
        'last_id' => $trivia->getLastInsertID()
      ),
      'headers' => ['HTTP/1.1 200 Oke']
    );
  }
}
