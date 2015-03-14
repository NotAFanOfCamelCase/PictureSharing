<?php

class Common 
{
	public static function getInstance()
	{
		static $instance = null;

		if ($instance === null)
		{
			$instance = static;
		}

		return $instance;
	}

	private function validateDirectory($user_config)
	{
		if ( ! file_exists($user_config) )
		{
			throw new Exception("Folder does not exist.");
		}
		elseif( ! is_dir($user_config) )
		{
			throw new Exception("Path is not a directory.");
		}
		elseif( is_writable($user_config) )
		{
			throw new Exception("Folder is not writeable under the current user.")
		}
	}
}