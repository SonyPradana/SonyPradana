<?php

use Simpus\Apps\Command;
use System\Cron\Schedule;

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

  public function printHelp()
  {
    return array(
      'option' => array(
        "\n\t" . $this->textGreen("cron") . $this->tabs(6) . "Run cron job (all shadule)",
        "\n\t" . $this->textGreen("cron") . ":work" . $this->tabs(5) . "Run virtual cron job in terminal",
      ),
      'argumnet' => array()
    );
  }

  public function switcher()
  {
    // get category command
    $makeAction = explode(':', $this->CMD);

    // stopwatch
    $watch_start = microtime(true);

    // property

    // find router
    switch ($makeAction[1] ?? '') {
      case '':
        $this->scheduler($schedule = new Schedule());
        $schedule->execute();
        break;

      case 'list':
        echo "\n";
        $this->scheduler($schedule = new Schedule());
        foreach ($schedule->getPools() as $cron) {
          echo "#  ";
          if ($cron->isAnimusly()) {
            echo $this->textDim($cron->getTimeName()), "\t";
          } else {
            echo $this->textGreen($cron->getTimeName()), "\t";
          }

          echo $this->textYellow($cron->getEventname()), "\n";
        }
        break;

      case 'work':
        echo $this->textBlue("\nSimulate Cron in terminal (every minute)");
        echo $this->textGreen("\n\nCtrl+C to stop\n");

        while (1) {
          if (date('s') == 00) {
            echo $this->textDim("Run cron at - ". date("D, H i"));
            echo "\n";

            $this->scheduler($schedule = new Schedule());
            $schedule->execute();

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

  public function scheduler(Schedule $schedule): void
  {
    $pdo = new System\Database\MyPDO();

    // covid web scrab
    $schedule
      ->call(function() use ($pdo) {
        // clear covid cached
        Simpus\Apps\Cache::static()->clear('CKSS');

        // return indexing function
        return (new CovidKabSemarangService($pdo))->indexing_compiere([]);
      })
      ->eventName('info covid')
      ->hourly();

    // delete old database rows
    $schedule
      ->call(function() use ($pdo) {
        System\Database\MyQuery::conn('scrf_protection', $pdo)
          ->delete()
          ->execute();

          return [];
      })
      ->eventName('scrf_protection')
      ->weekly();

    // delete old story
    $schedule
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
    $schedule
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
    $schedule
      ->call(function() use ($pdo) {
        System\Database\MyQuery::conn('cron_log', $pdo)
          ->delete()
          ->equal('output', '[]')
          ->execute();
      })
      ->eventName('cron log maintance')
      ->mountly();

    // others schedule
  }
}
