<?php


class Logger {

	private $common, $log_path, $skip_validation, $file_reader;

	private $log_levels = array('DEBUG', 'INFO', 'WARNING', 'ERROR');

	function __construct($log_path, $threshhold = false, $skip_validation=false)
	{
		$common		= Common::getInstance();

		try {
			( ! $this->skip_validation && $this->common->validate_directory($log_path) )
		}
		catch(Exception $e)
		{
			throw new UnwritableLoggerExcetion("Bad log path! " . $e->getMessage());
		}

		$this->log_path 		= $log_path;
		$this->file_reader		= fopen($log_path, 'a');
	}

	public static function getInstance($log_path, $threshhold = false, $skip_validation=false)
	{
		static $instance = null;

		if ( $instance === null )
		{
			$instance = new Logger($log_path, $threshhold, $skip_validation);
		}

		return $instance;
	}

	function __destruct()
	{
		fclose($this->file_reader);
	}


	function log($level, $message)
	{
		$level 	= strtoupper($level);
		$date	= date('m/d/Y h:i:s a';

		if ( ! in_array($level, $this->log_levels) )
		{
			throw new LoggerException("Log level '{$level}' is invalid. Use [" . implode(',', $this->log_levels) . "]" );
		}

		fwrite($this->file_reader, "[{$date}][{$level}] {$message}");
	}
}