<?php

class Router {

	private $request_data	= array();
	private $request_info, $controller, $action;

	function __construct($PHP_SERVER)
	{
		$this->request_info				= $PHP_SERVER;
		//Explode by slashes, filter blank string subsets, rebase array keys to account for unset blank subsets
		$this->request_data['URL_PATH'] = array_values(array_filter(explode('/', strtok($this->request_info['REQUEST_URI'], '?')), function($val){ return strlen($val); }));
		$this->request_data['METHOD']	= $this->request_info['REQUEST_METHOD'];

		$this->determineRoute();
	}

	public static function getInstance($PHP_SERVER)
	{
		$instance = null;

		if( $instance === null )
		{
			$instance = new Router($PHP_SERVER);
		}

		return $instance;
	}

	private function determineRoute()
	{
		$assumed_class	= 'controller' . strtolower($this->request_data['URL_PATH'][0]);
		$path_exists 	= in_array($assumed_class . '.php', array_map('basename', array_map('strtolower', glob(appcore\APP_CONTROLLERS . '*.php'))));
		$c 	= new ControllerPortal();
		if ( $path_exists && class_exists($assumed_class) )
		{
			//Controller exists
			$this->controller 	= 'controller' . $this->request_data['URL_PATH'][0];
			$controller 		= $this->controller;

			if( array_key_exists(1, $this->request_data['URL_PATH']) )
			{
				$action = $controller::actionExists($this->request_data['URL_PATH'][1], $this->request_data['METHOD']);

				if ( $action !== false )
				{
					$this->action = $action;
				}
				else
				{
					throw new UnreachableRouteException("Action {$this->request_data['URL_PATH'][1]} does not exist in controller {$this->controller}");
				}
			}
			else
			{
				throw new UnreachableRouteException("No action specified for controller {$this->controller}");
			}
		}
		else {
			throw new UnreachableRouteException("Controller {$assumed_class} does not exist");
		}
	}

	public function getController()
	{
		return $this->controller;
	}

	public function getAction()
	{
		return $this->action;
	}

	public static function respond($payload, $code, $content_type)
	{
		http_response_code($code);
		header('Content-Type: ' . $content_type);
		echo $payload;
	}
}