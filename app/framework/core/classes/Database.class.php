<?php

class Database {

	private $pdo, $config;
	private $options = array();

	function __constructor()
	{
		$this->config 	= Config::getInstance(appcore\APP_CONFIG);

		try {
			$this->pdo = new PDO("mysql:host={$this->config->getOption('db')['host']};dbname={$this->config->getOption('db')['database']};", 
									$this->config->getOption('db')['username'],
									$this->config->getOption('db')['password'],
									$this->options);
		}catch(Exception $e){
			throw new AppDatabaseException($e->getMessage());
		}
	}

	function __invoke()
	{
		return $this->pdo;
	}

	public static function getInstance()
	{
		static $instance = null;

		if ( $instance == null )
		{
			$instance = new Database();
		}

		return $instance;
	}
}