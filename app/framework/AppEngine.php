<?php

class AppEngine {

	private $request_data	= array();
	private $session_data	= array();
	private $config;


	function __construct($PHP_SERVER)
	{
		$this->config 			= Config::getInstance(appcore\APP_CONFIG);
		$this->router 			= Router::getInstance($PHP_SERVER);
	}

	public function run()
	{
		$c_name		= $this->router->getController();
		$controller = new $c_name;
		$content	= $controller->{$this->router->getAction()}();

		//Everything should be loaded and ready to go
		//How bout' we render some pages?

		Router::respond($content['payload'], $content['code'], $content['content_type']);
	}

	public static function init($PHP_SERVER)
	{
		static $instance = null;

		if ( $instance == null )
		{
			$instance = new AppEngine($PHP_SERVER);
		}

		return $instance;
	}
}