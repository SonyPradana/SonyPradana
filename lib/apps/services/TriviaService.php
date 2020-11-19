<?php

use Model\Trivia\Trivia;
use Simpus\Apps\Middleware;

class TriviaService extends Middleware
{
  public function Get_Ques(): array
  {
    $triva = new Trivia(null);
    $triva->read();

    return array (
      'status' => 'ok',
      'data' => array (
        'quest_id' => $triva->get_ID(),
        'quest' => array (
          'quest' => $triva->get_quest(),
          'image' => $triva->get_quest_image(),
        ),
        'options' => $triva->get_options()
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

    $summary = $triva->submit_answer($question_answer);
    $sumbited = 0;
    foreach ($summary as $val) {
      $sumbited += $val['summary'];
    }

    return array (
      'status' => 'ok',
      'data' => array(
        'correct' => $triva->get_answer(),
        'submited' => $sumbited,
        'summary' => $summary
      ),
      'headers' => ['HTTP/1.1 200 Oke']
    );
  }

  
}
