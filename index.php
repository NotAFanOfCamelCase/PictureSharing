<?php

require_once realpath(__DIR__) . '/app/autoload.php';

define('appcore\APP_CONFIG', 'myAppConfig');

AppEngine::init($_SERVER)->run();