<?php
define('appcore\APP_CONFIG', 'myAppConfig');
require_once realpath(__DIR__) . '/app/autoload.php';

AppEngine::init($_SERVER, $_REQUEST)->run();