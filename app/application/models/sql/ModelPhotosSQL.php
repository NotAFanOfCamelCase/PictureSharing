<?php


class ModeLPhotosSQL {
	
	private $tables	= array(
								'photos'	=> 'photos',
								'users'		=> 'users'
							);
	public $queries;

	function __construct()
	{
		$this->queries 	= array(
									'getAllPhotos'		=> "SELECT b.`user_emai`, a.`file_name`
															FROM `{$this->tables['photos']}` AS `a`
															LEFT JOIN `{$this->tables['users']}` AS `b`
															ON a.`owner` = b.`id`",

									'getPhotosByUserId'	=> "SELECT a.`file_name`
															FROM `{$this->tables['photos']}` AS `a`
															LEFT JOIN `{$this->tables['users']}` AS `b`
															ON a.`owner` = b.`id`
															WHERE b.`id` = :id"
								);
	}
}