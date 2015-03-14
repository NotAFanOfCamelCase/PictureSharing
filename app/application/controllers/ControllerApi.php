<?php

class API extends Ajax
{
	function __construct()
	{

	}

	public function getHelloWorld($action_route, $payload)
	{
		$this->requirePayload(array('name'));

		$this->response('Hello ' . $payload['name']);
	}

	public function getTriggerError($action_route, $payload)
	{
		$this->response('Bad Request', 400);
	}
}