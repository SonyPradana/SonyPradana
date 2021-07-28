<?php

namespace System\Broadcast;

class Broadcast
{
  private $pusher;
  private $channels;
  private $event;

  /**
   * @param string $channels Channel name to sand to clinet
   * @param string $event Event name to sand to clinet
   */
  public function __construct($channels = null, $event = null)
  {
    $this->pusher = new \Pusher\Pusher(
      PUSHER_APP_KEY,
      PUSHER_APP_SECRET,
      PUSHER_APP_ID,
      array (
        'cluster' => PUSHER_APP_CLUSTER,
        'useTLS'  => true
      )
    );

    $this->channels = $channels;
    $this->event = $event;
  }

  /**
   * @param array $data Data to send to clients
   * @return array Data send to client
   */
  public function trigerPusher(array $data)
  {
    if ($this->channels !== null && $this->event !== null) {
      $this->pusher->trigger(
        $this->channels,
        $this->event,
        $data
      );
    }

    return $data;
  }

  /**
   * @param string $channels Channel name to sand to clinet
   */
  public function channels(string $channels)
  {
    $this->channels = $channels;
    return $this;
  }

  /**
   * @param string $event Event name to sand to clinet
   */
  public function event(string $event)
  {
    $this->event = $event;
    return $this;
  }
}
