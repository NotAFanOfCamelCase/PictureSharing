<?php

class Database {

	private $pdo;
	private $options = array();

	function __constructor($host, $user, $password, $databse)
	{
		try {
			$this->pdo = new PDO("mysql:host={$host};dbname={$databse};", $user, $password, $this->options);
		}catch(Exception $e){
			throw new AppDatabaseException($e->getMessage());
		}
	}

	function __invoke()
	{
		return $this->pdo;
	}

	public static function getInstance($host, $user, $password, $database)
	{
		static $instance = null;

		if ( $instance == null )
		{
			$instance = new Database($host, $user, $password, $database);
		}

		return $instance;
	}
}