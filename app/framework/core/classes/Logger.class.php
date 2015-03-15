<?php


class Logger {

	private $common, $log_path, $skip_validation, $file_reader, $written;

	private $log_levels = array('DEBUG', 'INFO', 'WARNING', 'ERROR');

	function __construct($log_path, $threshhold = false, $skip_validation=false)
	{
		$this->common		= Common::getInstance();
		$this->config 		= Config::getInstance(appcore\APP_CONFIG);

		try {
			( ! $this->skip_validation && $this->common->validateDirectory($log_path) );
		}
		catch(Exception $e)
		{
			throw new UnwritableLoggerExcetion("Bad log path! " . $e->getMessage());
		}

		$this->log_path 		= $log_path;
		$this->log_file			= realpath($log_path) . '/log_' . date('d-m-Y') . '.txt';
		$this->file_reader		= fopen($this->log_file, 'a');

		if ( $threshhold !== false )
		{
			$this->threshhold		= $threshhold;
		}
		elseif ( $this->config->getOption('debug') )
		{
			$this->threshhold		= 1;
		}
		else{
			$this->threshhold		= 0;
		}
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


	public function log($level, $message)
	{
		$level 		= strtoupper($level);
		$date		= date('m/d/Y h:i:s');
		$level_k	= array_search($level, $this->log_levels);

		if ( ! in_array($level, $this->log_levels) )
		{
			throw new LoggerException("Log level '{$level}' is invalid. Use [" . implode(',', $this->log_levels) . "]" );
		}

		if ( $this->threshhold >= $level_k )
		{
			$this->written = fwrite($this->file_reader, "[{$this->getTimestamp}] [{$level}] {$message}\n");
		}

		if ( $this->written === false )
		{
			throw new UnwritableLoggerExcetion("Unable to write to log: {$message}");
		}
	}

    /**
     * Gets the correctly formatted Date/Time for the log entry.
     * 
     * PHP DateTime is dump, and you have to resort to trickery to get microseconds
     * to work.
     * 
     * @return string
     */
    private function getTimestamp()
    {
        $originalTime 	= microtime(true);
        $micro 			= sprintf("%06d", ($originalTime - floor($originalTime)) * 1000000);
        $date 			= new DateTime(date('Y-m-d H:i:s.'.$micro, $originalTime));

        return $date->format($this->dateFormat);
    }
}