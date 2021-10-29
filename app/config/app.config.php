<?php

return array (
  'BASEURL' => dirname(__DIR__, 2),
  'time_zone' => $_ENV['TIME_ZONE'] ?? 'Asia/Jakarta',

  'ENVIRONMENT'   => $_ENV['ENVIRONMENT'] ?? 'dev',

  /**
   * path to Model, View, Controller, Service, component
   *
   * MODEL_PATH folder container model and models
   * VIEW_PATH folder container view template html
   * CONTROLLER_PATH container controller
   * SERVICES_PATH container Services
   * COMPONENT container html component (widget, cards, etc)
   * COMMAND_PATH contain command class folder
   *
   */
  'MODEL_PATH'      => '/app/library/model/',
  'VIEW_PATH'       => '/app/views/',
  'CONTROLLER_PATH' => '/app/controllers/',
  'SERVICES_PATH'   => '/app/services/',
  'COMPONENT_PATH'  => '/resources/components/',
  'COMMNAD_PATH'    => '/app/commands/',
  'CACHE_PATH'      => '/storage/app/cache',
  'CONFIG'          => '/app/config/',
  'MIDDLEWARE'      => '/app/middleware/',
);
