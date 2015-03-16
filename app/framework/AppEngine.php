<?php

class AppEngine {

	private $request_data	= array();
	private $session_data	= array();
	private $config, $PHP_SERVER;

	function __construct($PHP_SERVER, $PHP_REQUEST)
	{
		$this->config 			= Config::getInstance(appcore\APP_CONFIG);
		$this->router 			= Router::getInstance($PHP_SERVER, $PHP_REQUEST);
		$this->PHP_SERVER		= $PHP_SERVER;
	}

	public function run()
	{
		$c_name		= $this->router->getController();
		$controller = new $c_name($this->PHP_SERVER, $this->router->getRouteData());
		$content	= $controller->{$this->router->getAction()}();

		//Everything should be loaded and ready to go
		//How bout' we render some pages?

		Router::respond($content['payload'], $content['code'], $content['content_type']);
	}

	public static function init($PHP_SERVER, $PHP_REQUEST)
	{
		static $instance = null;

		if ( $instance == null )
		{
			$instance = new AppEngine($PHP_SERVER, $PHP_REQUEST);
		}

		return $instance;
	}
}