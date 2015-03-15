<?php

class Config
{
	private $allowed	= array('db', 'uploads', 'debug', 'salt', 'log', 'app_name', 'error_controller', 'error_action');
	private $required 	= array('db', 'uploads', 'debug', 'salt', 'error_controller', 'error_action');
	private $config 	= array();
	private $common;

	function __construct($extract_settings)
	{
		$this->common = Common::getInstance();

		if ( ! is_string($extract_settings) )
		{
			throw new AppConfigException("Invalid configuration string. You are expected to spply the name of the function where I can get your settings");
		}

		if( ! $this->loadConfigFile($extract_settings) || ! function_exists($extract_settings) )
		{
			throw new AppConfigException("Invalid config function. \"{$extract_settings}\" does not exist");
		}

		$settings = $extract_settings();

		if ( ! is_array($settings) )
		{
			throw new AppConfigException("Your configuration function should return an array, instead a(n) " . get_type($settings) . " was returned.");
		}

		//check that all required config is present
		$missing = array_diff($this->required, array_keys($settings));

		if ( $missing )
		{
			throw new AppConfigException("Missing required configuration values: [" . implode(',', $missing) . "]");
		}

		//validate all settings
		foreach( $this->allowed as $option )
		{
			$v_func = 'setup_' . $option;

			if ( method_exists($this, $v_func) && array_key_exists($option, $settings))
			{
				$this->{$v_func}($settings[$option]);
			}
			else
			{
				$this->config[$option] = false;
			}
		}

	}

	public static function getInstance($settings_func)
	{
		static $instance = null;

		if ( $instance === null )
		{
			$instance = new Config($settings_func);
		}

		return $instance;
	}

	private function set_up_error_action($config_name)
	{
		if (method_exists('controller' . $this->config['error_controller'], 'action' . $config_name))
		{
			return true;
		}

		throw new UnreachableErrorPageException("There is no action \"{$config_name}\" in controller {$this->config['error_controller']}");
	}

	private function set_up_error_controller($config_name)
	{
		if (class_exists('controller' . $config_name))
		{
			return true;
		}

		throw new UnreachableErrorPageException("The error controller {$config_name} was not found.");
	}

	private function loadConfigFile($config_name)
	{
		$config_file = appcore\APP_ROOT . 'app/config/' . $config_name . '.php';

		if ( ! file_exists($config_file) )
		{
			return false;
		}

		require_once $config_file;

		return true;
	}

	public function getOption($option_name)
	{
		if ( array_key_exists($option_name, $this->config) )
		{
			return $this->config[$option_name];
		}

		return false;
	}

	private function setup_app_name($user_config)
	{
		$this->config['app_name'] = $user_config;

		return true;
	}

	private function setup_require(array $user_config)
	{
		//This part simply fetches the files, the engine will take care of syntax validation 

		$this->config['require'] = array();

		foreach ( $user_config as $file )
		{
			if ( file_exists($file) && is_dir($file) )
			{
				$this->config['require'][] = glob($file . '/*.php');
			}
			elseif ( file_exists($file) && pathinfo($file, PATHINFO_EXTENSION) == 'php')
			{
				$this->config['require'][] = $file;
			}
		}

		return true;
	}

	private function setup_log($user_config)
	{
		try{
			$this->common->validateDirectory($user_config);
		}catch(Exception $e)
		{
			throw new AppConfigException("Invalid log folder. " . $e->getMessage());
		}

		$this->config['log'] = $user_config;

		return true;
	}

	private function setup_salt($user_config)
	{
		if ( ! is_string($user_config) )
		{
			throw new AppConfigException("You must supply a string to salt with. You gave me a \"" . gettype($user_config) . '"');
		}

		$salt_len = strlen($user_config);

		if (  $salt_len < 32 )
		{
			throw new AppConfigException("It is required that you supply as 32 character salt. This was only {$salt_len} characters long... weeeak!");
		}

		$this->config['salt'] = $user_config;

		return false;
	}

	private function setup_debug($user_config)
	{
		$this->config['debug'] = $user_config ? true : false; //make sure we always have a boolean here

		return true;
	}

	private function setup_uploads($user_config)
	{
		try{
			$this->common->validateDirectory($user_config);
		}catch(Exception $e)
		{
			throw new AppConfigException("Invalid path for \"uploads\"." . $e->getMessage());
		}

		$this->config['uploads'] = $user_config;

		return false;
	}

	private function setup_db(array $user_config)
	{
		$req 					= array('host', 'username', 'password', 'database');
		$missing				= array();
		$this->config['db']		= array();

		foreach ( $req as $field )
		{
			if ( ! array_key_exists($field, $user_config) )
			{
				$missing[] = $field;
				continue;
			}

			$this->config['db'][$field]	= $user_config[$field];
		}

		if ( count($missing) )
		{
			throw new AppConfigException("Missing database fields: [" . implode(',', $missing) . "]");
		}

		return true;
	}
}