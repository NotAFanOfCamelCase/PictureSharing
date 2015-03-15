<?php

class ExceptionHandler {

	private $logger, $config;

	private $handlers;

	function __construct()
	{
		$this->logger 		= new Logger(appcore\APP_LOG);
		$this->config 		= Config::getInstance(appcore\APP_CONFIG);
		$this->handlers	= array(
										'UnreachableRouteException'	
												=> function($exception)
													{
														Router::getInstance()->error(404);
													},
										'UnwritableLoggerExcetion'
												=> function($exception)
													{
														mail($this->config->getOption('it_email'), 'UnwritableLoggerExcetion', $exception->getMessage());
													},
										'UnreachableErrorPageException'
												=> function($exception)
													{
														header("HTTP/1.0 500 Internal Server Error");
														die();
													},
										'default'					
												=> function($exception)
													{
														//Router::getInstance()->error(500);
													}
								);
	}

	function handle($exception)
	{
		$type	= get_class($exception);
		$this->logger->log('error', "{$type}: {$exception->getMessage()}");

		echo $type;

		if ( array_key_exists($type, $this->handlers) )
		{
			$this->handlers[$type]($exception);
		}
		else
		{
			$this->handlers['default']($exception);
		}
	}
}

$ex_handle = new ExceptionHandler;
set_exception_handler(array($ex_handle, 'handle'));

//set_error_handler();