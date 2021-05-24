<?php

use Simpus\Apps\Command;
use System\Cron\Schadule;

class CronCommand extends Command
{

  public static array $command = array(
    [
      "cmd"       => "cron",
      "mode"      => "start",
      "class"     => self::class,
      "fn"        => "switcher",
    ],
  );

  public function switcher()
  {
    // get category command
    $makeAction = explode(':', $this->CMD);

    // stopwatch
    $watch_start = microtime(true);

    // find router
    switch ($makeAction[1] ?? '') {
      case '':
        $this->schaduler(new Schadule());
        break;

      case 'work':
        echo $this->textBlue("\nSimulate Cron in terminal (every minute)");
        echo $this->textGreen("\n\nCtrl+C to stop\n");

        while (1) {
          if (date('s') == 00) {
            echo $this->textDim("Run cron at - ". date("D, H i"));
            echo "\n";

            $this->schaduler(new Schadule());
            sleep(60);
          } else {
            sleep(1);
          }
        }
        break;

      default:
        echo $this->textRed("\nArgumnet not register");
        break;
    }

    // end stopwatch
    $watch_end = round(microtime(true) - $watch_start, 3) * 1000;
    echo "\nDone in " . $this->textYellow($watch_end ."ms\n");
  }

  public function schaduler(Schadule $schadule): void
  {
    $pdo = new System\Database\MyPDO();

    // covid web scrab
    $schadule
      ->call(function() use ($pdo) {
        return (new CovidKabSemarangService($pdo))->indexing_compiere([]);
      })
      ->eventName('info covid')
      ->hourly();

    // delete old database rows
    $schadule
      ->call(function() use ($pdo) {
        System\Database\MyQuery::conn('scrf_protection', $pdo)
          ->delete()
          ->execute();

          return [];
      })
      ->eventName('scrf_protection')
      ->weekly();

    // delete old story
    $schadule
      ->call(function() use ($pdo) {
        $all = Model\Stories\Stories::call($pdo)->resultAll() ?? [];
        $delete = [];

        foreach ($all as $story) {
          if (time() > $story['date_end']) {
            // delete file (keep save small image)
            unlink(BASEURL . '/public/data/img/stories/original/' . $story['image_id']);
            unlink(BASEURL . '/public/data/img/stories/thumbnail/' . $story['image_id']);

            // delete database
            System\Database\MyQuery::conn('stories', $pdo)
              ->delete()
              ->equal('id', $story['id'])
              ->execute();

            $delete[] = $story;
          }
        }

        return ['deleted' => $delete];
      })
      ->eventName('story cleaner')
      ->daily();

    // create jadwal
    $schadule
      ->call(function() use ($pdo) {
        $create = new \Model\JadwalKia\JadwalKia($pdo);
        $success = $create->autoCreatJadwal(date('m'), date('Y'));

        $error = false;
        if (! $success) {
          $error['server'] = 'gagal menyimpan data / data sudah tersedia';
        }

        return array (
          'status'  => $success ? 'ok' : 'not save',
          'code'    => 200,
          'data'    => array(),
          'error'   => $error
        );
      })
      ->eventName('jadwal imunisasi')
      ->mountly();
    // cron log maintance
    $schadule
      ->call(function() use ($pdo) {
        System\Database\MyQuery::conn('cron_log', $pdo)
          ->delete()
          ->equal('output', '[]')
          ->execute();
      })
      ->eventName('cron log maintance')
      ->mountly();

    // others schadule
  }
}
