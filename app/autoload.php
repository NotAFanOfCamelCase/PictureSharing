<?php

define('APP_ROOT', realpath(__DIR__));

function controllerAutoload($controller)
{
	$attempt = APP_ROOT . "/app/application/controllers/Controller" . $controller '.php';

	if ( file_exists($attempt))
	{
		require_once $attempt;
	}
}

function modelAutoload($model)
{
	$attempt = APP_ROOT . "/app/application/models/Model" . $controller '.php';

	if ( file_exists($attempt))
	{
		require_once $attempt;
	}
}

function coreAutoload($core_class)
{
	$attempt = APP_ROOT . "/app/framework/core/" . $core_class '.class.php';

	if ( file_exists($attempt))
	{
		require_once $attempt;
	}
}

function exceptionAutoload($exception)
{
	$attempt = APP_ROOT . "/app/framework/exceptions/" . $core_class '.php';

	if ( file_exists($attempt))
	{
		require_once $attempt;
	}
}

spl_autoload_register('coreAutoload');
spl_autoload_register('modelAutoload');
spl_autoload_register('controllerAutoload');