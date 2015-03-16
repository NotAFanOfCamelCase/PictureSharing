<?php

require_once realpath(__DIR__) . '/sql/ModelPhotosSQL.php';

class ModeLPhotos extends ModeLPhotosSQL {

	private $pdo;

	function __construct()
	{
		$this->pdo 		= new PDOFactory::getInstance();
	}

	public function getPhotosByUserId($user_id)
	{
		$stmt	= $this->pdo->prepare($this->queries[__FUNCTION__]);
		$stmt->bindParam(':user_id', $user_id);

		if (  )
	}

	public function getAllPhotos()
	{
		$stmt	= $this->pdo->prepare($this->queries[__FUNCTION__]);

		if ( ! $stmt->execute() )
		{
			throw new Exception("MySQL Error! Could not grab all photos. {$stmt->errorInfo()[2]}");
		}

		return $stmt->fetchAll();
	}
}