<?php


class Ajax implements Controller{

	private $header_messages = array (
										100	=>	'Continue',
										101	=>	'Switching Protocols',
										102	=>	'Processing',
										200	=>	'OK',
										201	=>	'Created',
										202	=>	'Accepted',
										203	=>	'Non-Authoritative Information',
										204	=>	'No Content',
										205	=>	'Reset Content',
										206	=>	'Partial Content',
										207	=>	'Multi-Status',
										208	=>	'Already Reported',
										226	=>	'IM Used',
										300	=>	'Multiple Choices',
										301	=>	'Moved Permanently',
										302	=>	'Found',
										303	=>	'See Other',
										304	=>	'Not Modified',
										305	=>	'Use Proxy',
										306	=>	'Switch Proxy',
										307	=>	'Temporary Redirect',
										308	=>	'Permanent Redirect',
										400	=>	'Bad Request',
										401	=>	'Unauthorized',
										402	=>	'Payment Required',
										403	=>	'Forbidden',
										404	=>	'Not Found',
										405	=>	'Method Not Allowed',
										406	=>	'Not Acceptable',
										407	=>	'Proxy Authentication Required',
										408	=>	'Request Timeout',
										409	=>	'Conflict',
										410	=>	'Gone',
										411	=>	'Lenght Required',
										412	=>	'Precondition Failed',
										413	=>	'Request Entity Too Large',
										414	=>	'Request-URI Too Large',
										415	=>	'Unsupported Media Type',
										500	=>	'Internal Server Error',
										501	=>	'Not Implemented',
										502	=>	'Bad Gateway',
										503	=>	'Service Unavailable',
										504	=>	'Gateway Time-out',
										505	=>	'HTTP Version not supported'
									);

	public function json_response($payload, $code = 200)
	{
		$payload = json_encode($payload);

		if ( ! $payload )
		{
			throw new AjaxException("Payload is invalid. Unable to encode into json.");
		}

		if ( array_key_exists($code, $this->header_messages) )
		{
			$header	= "HTTP/1.1 {$code} {$this->header_messages[$code]}";
			$header	= "HTTP/1.1 {$code} {$this->header_messages[$code]}";
		}
		else
		{
			throw new AjaxException("Error code {$code} is invalid.")
		}

		header('Content-Type: application/json');
		header($header);
		echo $payload;
	}

	public function action_exists($action_exists)
	{
		
	}

	public function 
}