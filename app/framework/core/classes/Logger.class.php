<?php


class Logger {

	private $common, $log_path, $skip_validation, $file_reader, $written;

	private $log_levels = array('DEBUG', 'INFO', 'WARNING', 'ERROR');

	function __construct($log_path, $threshhold = false, $skip_validation=false)
	{
		$this->common		= Common::getInstance();

		try {
			( ! $this->skip_validation && $this->common->validateDirectory($log_path) );
		}
		catch(Exception $e)
		{
			throw new UnwritableLoggerExcetion("Bad log path! " . $e->getMessage());
		}

		$this->log_path 		= $log_path;
		$this->log_file			= realpath($log_path) . date('d-m-Y') . '.txt';
		$this->file_reader		= fopen($this->log_file, 'a');
		$this->threshhold		= $threshhold;
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
		$level 		= strtoupper($level);
		$date		= date('m/d/Y h:i:s a');
		$level_k	= array_search($level, $this->log_levels);

		if ( ! in_array($level, $this->log_levels) )
		{
			throw new LoggerException("Log level '{$level}' is invalid. Use [" . implode(',', $this->log_levels) . "]" );
		}

		if ( $this->threshhold >= $level_k )
		{
			$this->written = fwrite($this->file_reader, "[{$date}][{$level}] {$message}");
		}
	}
}