<?php

namespace System\Cron;

use Model\CronLog\CronLog;

class ScheduleTime
{
  private $call_back;
  private $params = [];
  private $time;
  private $log;
  private $event_name = 'animus';


  public function __construct($call_back, array $params, $time)
  {
    $this->call_back = $call_back;
    $this->params = $params;
    $this->time = $time;
    $this->log = new CronLog();
  }

  public function eventName($val)
  {
    $this->event_name = $val;
    return $this;
  }

  private function exect(array $events)
  {
    $dayLetter  = date("D", $this->time);
    $day        = date("d", $this->time);
    $hour       = date("H", $this->time);
    $minute     = date("i", $this->time);


    foreach ($events as $event) {
      $eventDayLetter = $event['D'] ?? $dayLetter; // default day letter every event

      if ($eventDayLetter == $dayLetter
      && $event['d'] == $day
      && $event['h'] == $hour
      && $event['m'] == $minute) {

        // stopwatch
        $watch_start = microtime(true);

        $out_put = call_user_func($this->call_back, $this->params) ?? [];

        // stopwatch
        $watch_end = round(microtime(true) - $watch_start, 3) * 1000;

        $this->log->convertFromArray([
          'id'              => null,
          'schedule_time'   => $this->time,
          'execution_time'  => $watch_end,
          'event_name'      => $this->event_name,
          'status'          => $out_put['code'] ?? 200,
          'output'          => json_encode($out_put),
        ]);
        $this->log->cread();
      }
    }
  }

  public function everyTenMinute()
  {
    $minute = [];
    for ($i=0; $i < 60; $i++) {

      if ($i % 10 == 0) {
        $minute[] = [
          'd' => date('d', $this->time),
          'h' => date('H', $this->time),
          'm' => $i
        ];
      }
    }

    return $this->exect($minute);
  }

  public function everyThirtyMinutes()
  {
    return $this->exect([
      [
        'd' => date('d', $this->time),
        'h' => date('H', $this->time),
        'm' => 0,
      ],
      [
        'd' => date('d', $this->time),
        'h' => date('H', $this->time),
        'm' => 30,
      ],
    ]);
  }

  public function everyTwoHour()
  {

    $thisDay = date("d");
    $hourly = []; // from 00.00 to 23.00 (12 time)
    for ($i=0; $i < 24; $i++) {
      if ($i % 2 == 0) {
        $hourly[] = [
          'd' => $thisDay,
          'h' => $i,
          'm' => 0
        ];
      }
    }

    return $this->exect($hourly);
  }

  public function everyTwelveHour()
  {
    return $this->exect([
      [
        'd' => date('d'),
        'h' => 0,
        'm' => 0,
      ],
      [
        'd' => date('d'),
        'h' => 12,
        'm' => 0,
      ]
    ]);
  }

  public function hourly()
  {
    $thisDay = date("d");
    $hourly = []; // from 00.00 to 23.00 (24 time)
    for ($i=0; $i < 24; $i++) {
      $hourly[] = [
        'd' => $thisDay,
        'h' => $i,
        'm' => 0
      ];
    }

    return $this->exect($hourly);
  }

  public function hourlyAt(int $hour24)
  {
    return $this->exect([
      [
        'd' => date('d'),
        'h' => $hour24,
        'm' => 0
      ]
    ]);
 }

  public function daily()
  {
    return $this->exect([
      // from day 1 to 31 (31 time)
      ['d' => date('d'), 'h' => 0, 'm' => 0],
    ]);
  }

  public function dailyAt(int $day)
  {
    return $this->exect([
      [
        'd' => $day,
        'h' => 0,
        'm' => 0,
      ]
    ]);
  }

  public function weekly()
  {
    return $this->exect([
      [
        'D' => "Sun",
        'd' => date('d'),
        'h' => 0,
        'm' => 0
      ],
    ]);
  }

  public function mountly()
  {
    return $this->exect([
      [
        'd' => 1,
        'h' => 0,
        'm' => 0
      ],
    ]);
  }

}
