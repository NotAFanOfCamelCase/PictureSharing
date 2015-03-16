<?php

class User {

	private $pdo, $user_data;

	function __construct($user_id)
	{
		$this->pdo 			= PDOFactory::getInstance();
		$this->user_data	= $this->getUserData($user_id);
	}

	public function isAdmin()
	{
		if ( intval($this->user_data['admin']) === 1)
		{
			return true;
		}

		return false;
	}

	public function getEmail()
	{
		return $this->user_data['user_email'];
	}

	public function getId()
	{
		return $this->user_data['id'];
	}

	private function getUserData($id)
	{
		$query	= "SELECT `id`, `user_email`, `admin` FROM `users` WHERE `id` = :id";
		$stmt	= $this->pdo->prepare($query);
		$stmt->bindParam(':id', $id)

		if ( ! $stmt->execute() )
		{
			throw new AppRuntimeException("MySQL Error! Unable to execute query for id {$id}. {$stmt->errorInfo()[2]}");
		}

		$results = $stmt->fetch();

		if ( ! count($results) )
		{
			throw new AppRuntimeException("User with id {$id} does not exist!");
		}

		return $results;
	}
}