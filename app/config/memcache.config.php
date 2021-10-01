<?php

return array(
  'MEMCACHED_HOST' => $_ENV['MEMCACHED_HOST'] ?? '127.0.0.1',
  'MEMCACHED_PASS' => $_ENV['MEMCACHED_PASS'] ?? '',
  'MEMCACHED_PORT' => $_ENV['MEMCACHED_PORT'] ?? 6379,
);
