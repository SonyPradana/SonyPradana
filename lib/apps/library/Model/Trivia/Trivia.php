<?php 

namespace Model\Trivia;

use System\Database\MyCRUD;
use System\Database\MyPDO;

class Trivia extends MyCRUD
{
  // getter

  public function get_ID(): int
  {
    return $this->getter('id') ?? 0;
  }

  /** Mengambil pertanyaan berdasarkan ID
   * @return string Pertanyaan berdsarkan ID
   */
  public function get_quest(): string
  {
    return $this->getter('quest') ?? null;
  }

  /** Mengambil Gambar dari pertanyaan
   * @return string jika ada img urlnya
   * @return null jika pertanyaan tidak ada gambarnya
   */
  public function get_quest_image()
  {
    return $this->getter('quest_img') == '' ? null : $this->getter('quest_img');
  }

  /** Mengambil pilihan jawaban dari pertnyaan sesuai ID
   * @return array random pilihan jawaban
   */
  public function get_options(): array
  {
    $options = $this->getter('options');
    $options = json_decode($options);
    return $this->shuffle_options($options);
  }

  /** Mengirim jawaban  */
  public function submit_answer(int $answer)
  {
    $summary_str = $this->getter('summary');
    $summary_arr = json_decode($summary_str, true);
    $key = array_search($answer, array_column($summary_arr, 'id'));
    $summary_arr[$key]['summary'] += 1;

    $this->setter('summary', json_encode($summary_arr));
    $this->update();
    return $summary_arr;
  }

  public function get_answer(): int
  {
    return $this->getter('correct_answer');
  }

  // int

  public function __construct(string $id_quest = null)
  {
    $this->PDO = new MyPDO();
    $id_quest = $id_quest ?? $this->getRandom();

    $this->TABLE_NAME = 'trivia_quest';
    $this->ID = array('id' => $id_quest);
    $this->COLUMNS = [
      'id' => null,
      'quest_id' => null,
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
  private function getRandom(): int
  {
    $this->PDO->query(
      "SELECT
        max(`id`) as max_number
      FROM
        `trivia_quest`
      "
    );
    ;
    $this->PDO->execute();
    $row = (int) $this->PDO->resultset()[0]['max_number'];
    return rand(1, $row);
  }

  /** Mengacak urutan jawaban dari pertanyaan
   * @param array opsi jawawab urut
   * @return array opsi jawaban random
   */
  private function shuffle_options(array $sorted_array): array
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
