<?php

class Common 
{
	public static function getInstance()
	{
		static $instance = null;

		if ($instance === null)
		{
			$instance = new Common();
		}

		return $instance;
	}

	public function validateDirectory($user_config)
	{
		$perms = base_convert(fileperms($user_config), 10, 8); 
		$perms = (int) substr($perms, (strlen($perms) - 3)); 

		if ( ! file_exists($user_config) )
		{
			throw new Exception("Folder does not exist.");
		}
		elseif( ! is_dir($user_config) )
		{
			throw new Exception("Path is not a directory.");
		}
		elseif( $perms !== 777 )
		{
			throw new Exception("Folder is not writable under the current user.");
		}
	}
}