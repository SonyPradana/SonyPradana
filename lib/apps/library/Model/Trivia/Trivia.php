<?php 

namespace Model\Trivia;

use GUMP;
use System\Database\MyCRUD;
use System\Database\MyPDO;

class Trivia extends MyCRUD
{
  // getter

  /** Mengambil id sesuai id yang submit */
  public function getID(): int
  {
    return $this->getter('id') ?? 0;
  }

  /** Mengambil pertanyaan berdasarkan ID
   * @return string Pertanyaan berdsarkan ID
   */
  public function getQuest(): string
  {
    return $this->getter('quest') ?? null;
  }

  /** Mengambil Gambar dari pertanyaan
   * @return string jika ada img urlnya
   * @return null jika pertanyaan tidak ada gambarnya
   */
  public function getQuestImage()
  {
    return $this->getter('quest_img') == '' ? null : $this->getter('quest_img');
  }

  /** Mengambil pilihan jawaban dari pertnyaan sesuai ID
   * @return array random pilihan jawaban
   */
  public function getOptions(): array
  {
    $options = $this->getter('options');
    $options = json_decode($options);
    return $this->shuffleOptions($options);
  }

  /** Mengirim jawaban sekaligus mengambil hasil jawaban */
  public function submitAnswer(int $answer)
  {
    $summary_str = $this->getter('summary');
    $summary_arr = json_decode($summary_str, true);
    $key = array_search($answer, array_column($summary_arr, 'id'));
    $summary_arr[$key]['summary'] += 1;

    $this->setter('summary', json_encode($summary_arr));
    $this->update();
    return $summary_arr;
  }

  /** Mengambil jawaban benar dari pertanyaan */
  public function getAnswer(): int
  {
    return $this->getter('correct_answer');
  }

  /** Get informasi pembuat quest
   * @return array info pembuat quest
   */
  public function getInfo(): array
  {
    return array (
      'author' => $this->getter('author'),
      'date_create' => $this->getter('date_create')
    );
  }

  public function newQuest($params)
  {
    $validation = new GUMP('id');
    $validation->validation_rules(array (
      'author' => 'required|max_len,20|min_len,3|alpha_dash',
      'level' => 'required|numeric',
      'quest' => 'required|max_len,255|min_len,6',
      'options' => 'required|max_len,500',
      'summary' => 'required|max_len,255',
      'correct_answer' => 'required|numeric',
      'explanation' => 'max_len,500'
    ));
    $validation->filter_rules(array (
      'id' => 'sanitize_numbers',
      'quest' => 'sanitize_string|trim',
      'explanation' => 'sanitize_string|trim'
    ));
    $validation->run($params);

    if (! $validation->errors()) {
      $this->setter('id', '')
        ->setter('author', $params['author'])
        ->setter('level', $params['level'])
        ->setter('date_create', time())
        ->setter('quest', $params['quest'])
        ->setter('quest_img', $params['quest_img'])
        ->setter('options', $params['options'])
        ->setter('summary', $params['summary'])
        ->setter('correct_answer', $params['correct_answer'])
        ->setter('explanation', $params['explanation']);
      
      return $this->cread();
    }

    return $validation->get_errors_array();
  }

  // int

  public function __construct(string $id_quest = null, int $rev = 0)
  {
    $this->PDO = new MyPDO();
    $id_quest = $id_quest ?? $this->getRandom($rev);

    $this->TABLE_NAME = 'trivia_quest';
    $this->ID = array('id' => $id_quest);
    $this->COLUMNS = [
      'id' => null,
      'author' => null,
      'level' => null,
      'date_create' => null,
      'quest' => null,
      'quest_img' => null,
      'options' => null,
      'summary' => null,
      'correct_answer' => null,
    ];
  }

  /** Random id pertanyaan
   * @return int random id quest (1-max)
   */
  private function getRandom(int $rev = 0): int
  {
    // TODO: random quest menurut kategory
    
    $this->PDO->query(
      "SELECT
        `id`
      FROM
        `trivia_quest`
      "
    );
    ;
    $this->PDO->execute();
    $res = $this->PDO->resultset();
    $list = array_values(array_column($res, 'id'));
    $rand = array_rand($list);
    $return = (int) $list[$rand];
    return $return == $rev ? $this->getRandom($rev) : $return;
  }

  /** Mengacak urutan jawaban dari pertanyaan
   * @param array opsi jawawab urut
   * @return array opsi jawaban random
   */
  private function shuffleOptions(array $sorted_array): array
  {
    $shuffle_array = array();
    $keys = array_keys($sorted_array);
    shuffle($keys);
    foreach ($keys as $key) {
      $shuffle_array[] = $sorted_array[$key];
    }
    return $shuffle_array;
  }


}
