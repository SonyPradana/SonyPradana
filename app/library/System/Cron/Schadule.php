<?php

namespace System\Cron;

class Schadule
{
  private $time;

  public function __construct(int $time = null)
  {
    $this->time = $time ?? time();
  }

  public function call($call_back, array $params = [])
  {
    return new ScheduleTime($call_back, $params, $this->time);
  }
}
