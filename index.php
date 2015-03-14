<?php

require_once realpath(__DIR__) . '/app/autoload.php';

define(APP_CONFIG, 'myAppConfig');

AppEngine::START($_SERVER['REQUEST_URI']);