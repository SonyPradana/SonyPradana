<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

// call CLI
$new_cli = new \Simpus\Apps\CLI([
  'simpus',
  'cron'
]);
