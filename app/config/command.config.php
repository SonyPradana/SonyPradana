<?php

return array_merge(
  // help command
  HelpCommand::$command,
  // make somthink command
  MakeCommand::$command,
  // cron
  CronCommand::$command,
  // cache
  CacheCommand::$command,
	// Debug
	DebugCommand::$command
	// more command here
);
