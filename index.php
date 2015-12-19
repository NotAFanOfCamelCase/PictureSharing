<?php
define('appcore\APP_CONFIG', 'myAppConfig');
require_once realpath(__DIR__) . '/app/autoload.php';

// We'll run the engine in some fancy
// hipsterish way here.
AppEngine::init($_SERVER, $_REQUEST)->run();
