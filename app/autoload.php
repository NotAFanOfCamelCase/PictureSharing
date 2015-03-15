<?php

require_once realpath(__DIR__) . '/framework/core/constants.php';
require_once realpath(__DIR__) . '/framework/AppEngine.php';


function controllerAutoload($controller)
{
	$controller_files	= array_map('basename', glob(appcore\APP_CONTROLLERS . '*.php'));
	$controller_lower	= array_map('strtolower', $controller_files);
	$controller 		= strtolower($controller);
	$search 			= array_search($controller . '.php', $controller_lower);

	if ( $search !== false )
	{
		require_once appcore\APP_CONTROLLERS . $controller_files[$search];
	}
}

function modelAutoload($model)
{
	$attempt = appcore\APP_MODELS . "Model" . $model . '.php';

	if ( file_exists($attempt))
	{
		require_once $attempt;
	}
}

function coreAutoload($core_class)
{
	$attempt = appcore\APP_ROOT . "/app/framework/core/classes/" . $core_class . '.class.php';

	if ( file_exists($attempt))
	{
		require_once $attempt;
	}
}

function exceptionAutoload($exception)
{
	$attempt = appcore\APP_ROOT . "/app/framework/exceptions/" . $exception . '.php';

	if ( file_exists($attempt))
	{
		require_once $attempt;
	}
}

function libAutoload($lib)
{
	$attempt = appcore\APP_ROOT . "/app/framework/core/lib/" . $lib . '.php';

	if ( file_exists($attempt))
	{
		require_once $attempt;
	}
}

spl_autoload_register('libAutoload');
spl_autoload_register('coreAutoload');
spl_autoload_register('modelAutoload');
spl_autoload_register('exceptionAutoload');
spl_autoload_register('controllerAutoload');
require_once realpath(__DIR__) . '/framework/core/handler.php'; //Load error handler