<?php

class ControllerApi extends Rest
{
	private $auth, $PHP_REQUEST, $APP_ROUTE_DATA;

	function __construct($PHP_REQUEST, $APP_ROUTE_DATA)
	{
		$this->auth 			= new Authentication();
		$this->PHP_REQUEST		= $PHP_REQUEST;
		$this->APP_ROUTE_DATA	= $APP_ROUTE_DATA;
		$this->user 			= new User($this->auth->getCurrentId());
		$this->photoModel		= new ModelPhotos();
		$this->config 			= Config::getInstance();
		$this->logger			= new Logger($this->config->getOption('log'));
	}

	private function errorWrapper($message)
	{
		return array('message' => $message);
	}

	public function getPhotos()
	{
		$this->logger->log('debug', "API call for Photos");

		if ( ! $auth->isAuthenticated() )
		{
			$this->logger->log('debug', "Request denied. User was not authenticated.");
			return $this->respond($this->errorWrapper('Not authenticated'), 401);
		}

		if ( isset($this->APP_ROUTE_DATA[2]) ) //Specific user set
		{
			$photos 	= $this->photoModel->getPhotosByUserId($this->APP_ROUTE_DATA[2]);
		}
		else
		{
			$photos 	= $this->photoModel->getAllPhotos();
		}

		return $this->respond($photos);
	}

	public function deletePhotos()
	{
		//if it's owner or if it's admin
	}

	public function postPhotos()
	{
		$this->logger->log('debug', "API call for Photo upload");

		if ( ! $auth->isAuthenticated() )
		{
			$this->logger->log('debug', "Request denied. User was not authenticated.");
			return $this->respond($this->errorWrapper('Not authenticated'), 401);
		}
	}

	public function getSearch()
	{
		if 
	}
}