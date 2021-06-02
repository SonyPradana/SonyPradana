<?php

return array(
  'REDIS_HOST' => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
  'REDIS_PASS' => $_ENV['REDIS_PASS'] ?? '',
  'REDIS_PORT' => $_ENV['REDIS_PORT'] ?? 6379,
);
