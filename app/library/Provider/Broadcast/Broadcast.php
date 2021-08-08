<?php

namespace System\Provider;

class Broadcast
{
  /**
   * Static to call new pusher channels-event
   *
   * @param string $channels Channel name to sand to clinet
   * @param string $event Event name to sand to clinet
   * @param array $data Data array to sand nto clinet
   * @return array A data
   */
  public static function broadcast(string $channels, string $event, array $data): array
  {
    return (new \System\Broadcast\Broadcast($channels, $event))->trigerPusher($data);
  }
}
