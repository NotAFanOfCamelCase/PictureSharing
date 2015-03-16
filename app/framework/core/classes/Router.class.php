<?php

class Router {

	private $request_data	= array();
	private $request_info, $controller, $action;

	function __construct($PHP_SERVER, $PHP_REQUEST)
	{
		$this->request_info						= $PHP_SERVER;
		$this->request_data['HTTP']				= array();
		$this->request_data['PAYLOAD']			= $PHP_REQUEST;
		$this->config 							= Config::getInstance(appcore\APP_CONFIG);

		//Explode by slashes, filter blank string subsets, rebase array keys to account for unset blank subsets
		$this->request_data['HTTP']['URL_PATH'] = array_values(array_filter(explode('/', strtok($this->request_info['REQUEST_URI'], '?')), function($val){ return strlen($val); }));
		$this->request_data['HTTP']['METHOD']	= $this->request_info['REQUEST_METHOD'];

		$this->determineRoute();
	}

	public static function getInstance($PHP_SERVER=null, $PHP_REQUEST=null)
	{
		$instance = null;

		if( $instance === null )
		{
			if ( $PHP_SERVER === null || $PHP_REQUEST === null)
			{
				trigger_error("Router cannot be initialized. Missing or null arguments", E_USER_ERROR);
			}

			$instance = new Router($PHP_SERVER, $PHP_REQUEST);
		}

		return $instance;
	}

	public function getRouteData()
	{
		return $this->request_data['HTTP']['URL_PATH'];
	}

	private function determineRoute()
	{
		//Check if default
		if ( ! array_key_exists(0, $this->request_data['HTTP']['URL_PATH']) || ! (is_string($this->request_data['HTTP']['URL_PATH']) && strlen ($this->request_data['HTTP']['URL_PATH'])) )
		{
			//Do default controller and action
			$this->controller 	= 'controller' . $this->config->getOption('default_controller');
			$this->action 		= 'action' . $this->config->getOption('default_action');

			return;
		}

		$assumed_class	= 'controller' . strtolower($this->request_data['HTTP']['URL_PATH'][0]);
		$path_exists 	= in_array($assumed_class . '.php', array_map('basename', array_map('strtolower', glob(appcore\APP_CONTROLLERS . '*.php'))));
		$c 	= new ControllerPortal();
		if ( $path_exists && class_exists($assumed_class) )
		{
			//Controller exists
			$this->controller 	= 'controller' . $this->request_data['HTTP']['URL_PATH'][0];
			$controller 		= $this->controller;

			if( array_key_exists(1, $this->request_data['HTTP']['URL_PATH']) )
			{
				$action = $controller::actionExists($this->request_data['HTTP']['URL_PATH'][1], $this->request_data['HTTP']['METHOD']);

				if ( $action !== false )
				{
					$this->action = $action;
				}
				else
				{
					throw new UnreachableRouteException("Action {$this->request_data['HTTP']['URL_PATH'][1]} does not exist in controller {$this->controller}");
				}
			}
			else
			{
				$action = $controller::actionExists('index', $this->request_data['HTTP']['METHOD']);

				//Try default controller action (index)
				if($action !== false) {
					$this->action = $action;
				}
				else
				{
					throw new UnreachableRouteException("No action specified for controller {$this->controller}");
				}
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

	public function error($code)
	{
		$this->controller 				= Config::getInstance(appcore\APP_CONFIG)->getOption('error_controller');
		$this->action 					= Config::getInstance(appcore\APP_CONFIG)->getOption('error_action');
		$this->request_data['ROUTER']	= array('code' => $code );
	}

	public static function respond($payload, $code, $content_type)
	{
		http_response_code($code);
		header('Content-Type: ' . $content_type);
		echo $payload;
	}
}