<?php

namespace appcore;

define(__NAMESPACE__ . '\APP_ROOT', realpath(__DIR__ . "/../../../") . '/');
define(__NAMESPACE__ . '\APP_LOG', APP_ROOT . "app/logs/framework/");
define(__NAMESPACE__ . '\APP_CONTROLLERS', APP_ROOT . "app/application/controllers/");
define(__NAMESPACE__ . '\APP_VIEWS', APP_ROOT . "app/application/views/");
define(__NAMESPACE__ . '\APP_MODELS', APP_ROOT . "app/application/models/");
define(__NAMESPACE__ . '\ENGINE_LOGS', APP_ROOT . "app/logs/framework/");