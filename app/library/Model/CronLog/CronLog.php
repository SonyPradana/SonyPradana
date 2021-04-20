<?php

namespace Model\CronLog;

use System\Database\MyCRUD;
use System\Database\MyPDO;

class CronLog extends MyCRUD
{
  // getter
  public function getID()
  {
    return $this->ID['id'] ?? null;
  }

  // getter
	public function schedule_time()
	{
		return $this->COLUMNS['schedule_time'];
	}

	public function execution_time()
	{
		return $this->COLUMNS['execution_time'];
	}

	public function event_name()
	{
		return $this->COLUMNS['event_name'];
	}

	public function status()
	{
		return $this->COLUMNS['status'];
	}

	public function output()
	{
		return $this->COLUMNS['output'];
	}


  // setter
  public function setID(int $val)
  {
    $this->ID = array (
      'id' => $val
    );
    return $this;
  }

  // setter
	public function setSchedule_time(int $val)
	{
		$this->COLUMNS['schedule_time'] = $val;
		return $this;
	}
	public function setExecution_time(int $val)
	{
		$this->COLUMNS['execution_time'] = $val;
		return $this;
	}
	public function setEvent_name(int $val)
	{
		$this->COLUMNS['event_name'] = $val;
		return $this;
	}
	public function setStatus(int $val)
	{
		$this->COLUMNS['status'] = $val;
		return $this;
	}
	public function setOutput(int $val)
	{
		$this->COLUMNS['output'] = $val;
		return $this;
	}

  public function __construct()
  {
    $this->PDO = MyPDO::getInstance();
    $this->TABLE_NAME = 'cron_log';
    $this->COLUMNS = array(
      'schedule_time' => null,
			'execution_time' => null,
			'event_name' => null,
			'status' => null,
			'output' => null,

    );
  }

}
